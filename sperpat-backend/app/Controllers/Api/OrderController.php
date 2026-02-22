<?php

namespace App\Controllers\Api;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\PromoModel;

class OrderController extends BaseApiController
{
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;
    protected ProductModel $productModel;
    protected PromoModel $promoModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel   = new ProductModel();
        $this->promoModel     = new PromoModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');

        if ($status) {
            $orders = $this->orderModel->getByStatus($status);
        } else {
            $orders = $this->orderModel->getWithUser();
        }

        return $this->successResponse($orders);
    }

    public function show($id = null)
    {
        $order = $this->orderModel->select('orders.*, users.name as customer_name, users.email as customer_email, users.phone as customer_phone')
                     ->join('users', 'users.id = orders.user_id', 'left')
                     ->find($id);

        if (!$order) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        $order['items'] = $this->orderItemModel->getByOrder($id);

        return $this->successResponse($order);
    }

    public function create()
    {
        $userData = $this->getUserData();
        $data     = $this->request->getJSON(true);

        if (empty($data['items']) || !is_array($data['items'])) {
            return $this->errorResponse('Item pesanan diperlukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $total = 0;
        $orderItems = [];

        foreach ($data['items'] as $item) {
            if (!isset($item['qty']) || !is_numeric($item['qty']) || $item['qty'] <= 0) {
                $db->transRollback();
                return $this->errorResponse("Kuantitas barang (qty) tidak valid. Harus lebih dari 0.", 400);
            }

            // Mengunci baris produk ini agar request lain mengantre (Pessimistic Locking) mencegah stok minus
            $productQuery = $db->table('products')->where('id', $item['product_id'])->getForUpdate();
            $product = $productQuery ? $productQuery->getRowArray() : null;

            if (!$product) {
                $db->transRollback();
                return $this->errorResponse("Produk ID {$item['product_id']} tidak ditemukan.", 404);
            }

            if ($product['stok'] < $item['qty']) {
                $db->transRollback();
                return $this->errorResponse("Stok {$product['name']} tidak mencukupi. Tersisa: {$product['stok']}");
            }

            $subtotal = $product['harga_jual'] * $item['qty'];
            $total += $subtotal;

            $orderItems[] = [
                'product_id' => $product['id'],
                'qty'        => $item['qty'],
                'harga'      => $product['harga_jual'],
                'subtotal'   => $subtotal,
            ];

            // Reduce stock
            $this->productModel->update($product['id'], [
                'stok' => $product['stok'] - $item['qty'],
            ]);
        }

        $totalDiscount = 0;
        $promoId = null;

        if (!empty($data['promo_code'])) {
            $promoResult = $this->promoModel->calculateDiscount($data['promo_code'], $userData->id, $orderItems, $total);
            if ($promoResult['success']) {
                $totalDiscount = $promoResult['discount'];
                $promoId = $promoResult['promo_id'];
                $total -= $totalDiscount;
            } else {
                $db->transRollback();
                return $this->errorResponse($promoResult['message'], 400);
            }
        }

        $orderData = [
            'invoice_number'    => $this->orderModel->generateInvoiceNumber(),
            'user_id'           => $userData->id,
            'total'             => $total > 0 ? $total : 0,
            'promo_id'          => $promoId,
            'discount_amount'   => $totalDiscount,
            'alamat'            => $data['alamat'] ?? '',
            'status'            => 'pending',
            'metode_pembayaran' => $data['metode_pembayaran'] ?? 'transfer',
            'catatan'           => $data['catatan'] ?? null,
        ];

        $this->orderModel->insert($orderData);
        $orderId = $this->orderModel->getInsertID();

        foreach ($orderItems as &$item) {
            $item['order_id'] = $orderId;
        }

        $this->orderItemModel->insertBatch($orderItems);

        // Increment promo claimed count if a promo was used
        if ($promoId) {
            $promo = $this->promoModel->find($promoId);
            if ($promo) {
               $this->promoModel->update($promoId, [
                   'claimed_count' => $promo['claimed_count'] + 1
               ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->errorResponse('Gagal membuat pesanan.', 500);
        }

        $order = $this->orderModel->find($orderId);
        $order['items'] = $this->orderItemModel->getByOrder($orderId);

        return $this->successResponse($order, 'Pesanan berhasil dibuat.', 201);
    }

    public function userOrders()
    {
        $userData = $this->getUserData();
        $orders   = $this->orderModel->getByUser($userData->id);

        return $this->successResponse($orders);
    }

    public function updateStatus($id = null)
    {
        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->errorResponse('Pesanan tidak ditemukan.', 404);
        }

        $data = $this->request->getJSON(true);
        $validStatuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];

        if (!isset($data['status']) || !in_array($data['status'], $validStatuses)) {
            return $this->errorResponse('Status tidak valid.');
        }

        $updateData = ['status' => $data['status']];

        if ($data['status'] === 'shipped' && isset($data['nomor_resi'])) {
            $updateData['nomor_resi'] = $data['nomor_resi'];
        }

        // If cancelled, restore stock AND restore promo quota securely
        if ($data['status'] === 'cancelled' && $order['status'] !== 'cancelled') {
            $items = $this->orderItemModel->getByOrder($id);
            foreach ($items as $item) {
                $qty = (int) $item['qty'];
                $db = \Config\Database::connect();
                $db->table('products')
                   ->where('id', $item['product_id'])
                   ->set('stok', "stok + {$qty}", false)
                   ->update();
            }

            if (!empty($order['promo_id'])) {
                $db = \Config\Database::connect();
                $db->table('promos')
                   ->where('id', $order['promo_id'])
                   ->where('claimed_count >', 0)
                   ->set('claimed_count', 'claimed_count - 1', false)
                   ->update();
            }
        }

        $this->orderModel->update($id, $updateData);
        $order = $this->orderModel->find($id);

        return $this->successResponse($order, 'Status pesanan berhasil diupdate.');
    }
}
