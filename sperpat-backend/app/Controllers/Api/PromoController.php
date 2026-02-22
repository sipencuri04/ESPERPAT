<?php

namespace App\Controllers\Api;

use App\Models\PromoModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class PromoController extends BaseApiController
{
    protected PromoModel $promoModel;

    public function __construct()
    {
        $this->promoModel = new PromoModel();
    }

    public function validatePromo()
    {
        $data = $this->request->getJSON(true);
        $promoCode = $data['code'] ?? null;
        $items = $data['items'] ?? [];
        $userData = $this->getUserData();

        if (empty($promoCode) || empty($items)) {
            return $this->errorResponse('Data keranjang atau kode kupon tidak valid.', 400);
        }

        // We need product prices to calculate the discount correctly to stop client spoofing
        $productModel = new ProductModel();
        $totalOrderValue = 0;
        $itemsData = [];

        foreach ($items as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                $subtotal = $product['harga_jual'] * $item['qty'];
                $totalOrderValue += $subtotal;
                $itemsData[] = [
                    'product_id' => $product['id'],
                    'qty'        => $item['qty'],
                    'harga_jual' => $product['harga_jual'],
                    'subtotal'   => $subtotal
                ];
            }
        }

        $result = $this->promoModel->calculateDiscount($promoCode, $userData->id, $itemsData, $totalOrderValue);

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 400);
        }

        return $this->successResponse([
            'discount' => $result['discount'],
            'promo_id' => $result['promo_id'],
            'message'  => $result['message']
        ], $result['message']);
    }

    public function index()
    {
        $promos = $this->promoModel
            ->select('promos.*, products.name as product_name, users.name as customer_name')
            ->join('products', 'products.id = promos.product_id', 'left')
            ->join('users', 'users.id = promos.customer_id', 'left')
            ->orderBy('promos.id', 'DESC')
            ->findAll();

        return $this->successResponse(['promos' => $promos]);
    }

    public function getAvailablePromos()
    {
        $userData = $this->getUserData();
        
        $builder = $this->promoModel
            ->where('status', 'active')
            ->groupStart()
                ->where('valid_to >=', date('Y-m-d H:i:s'))
                ->orWhere('valid_to', null)
            ->groupEnd()
            ->groupStart()
                ->where('customer_id', $userData->id)
                ->orWhere('customer_id', null)
            ->groupEnd()
            ->orderBy('id', 'DESC');
            
        return $this->successResponse($builder->findAll());
    }

    public function create()
    {
        $rules = [
            'code'           => 'required|is_unique[promos.code]',
            'discount_type'  => 'required|in_list[percent,fixed]',
            'discount_value' => 'required|numeric',
            'status'         => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);
        $data['product_id'] = empty($data['product_id']) ? null : $data['product_id'];
        $data['customer_id'] = empty($data['customer_id']) ? null : $data['customer_id'];
        $data['valid_from'] = empty($data['valid_from']) ? null : $data['valid_from'];
        $data['valid_to'] = empty($data['valid_to']) ? null : $data['valid_to'];
        $data['quota'] = empty($data['quota']) ? null : $data['quota'];

        $this->promoModel->insert($data);

        return $this->successResponse(['id' => $this->promoModel->getInsertID()], 'Promo berhasil ditambahkan', 201);
    }

    public function update($id = null)
    {
        $promo = $this->promoModel->find($id);

        if (!$promo) {
            return $this->errorResponse('Promo tidak ditemukan', 404);
        }

        $rules = [
            'code'           => "required|is_unique[promos.code,id,{$id}]",
            'discount_type'  => 'required|in_list[percent,fixed]',
            'discount_value' => 'required|numeric',
            'status'         => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->errorResponse('Validasi gagal.', 422, $this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);
        $data['product_id'] = empty($data['product_id']) ? null : $data['product_id'];
        $data['customer_id'] = empty($data['customer_id']) ? null : $data['customer_id'];
        $data['valid_from'] = empty($data['valid_from']) ? null : $data['valid_from'];
        $data['valid_to'] = empty($data['valid_to']) ? null : $data['valid_to'];
        $data['quota'] = empty($data['quota']) ? null : $data['quota'];

        $this->promoModel->update($id, $data);

        return $this->successResponse(null, 'Promo berhasil diupdate');
    }

    public function delete($id = null)
    {
        $promo = $this->promoModel->find($id);

        if (!$promo) {
            return $this->errorResponse('Promo tidak ditemukan', 404);
        }

        $this->promoModel->delete($id);

        return $this->successResponse(null, 'Promo berhasil dihapus');
    }
}
