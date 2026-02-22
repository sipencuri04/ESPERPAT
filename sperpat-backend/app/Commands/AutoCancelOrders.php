<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AutoCancelOrders extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:auto_cancel_orders';
    protected $description = 'Membatalkan otomatis pesanan pending yang lewat 24 jam dan mengembalikan stok.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $orderModel = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemModel();

        // Cari pending orders yang dibuat lebih dari 24 jam yang lalu
        $thresholdTime = date('Y-m-d H:i:s', strtotime('-24 hours'));

        CLI::write("Mencari pesanan pending sebelum {$thresholdTime}...", 'yellow');

        $pendingOrders = $db->table('orders')
            ->where('status', 'pending')
            ->where('created_at <', $thresholdTime)
            ->get()
            ->getResultArray();

        if (empty($pendingOrders)) {
            CLI::write("Tidak ada pesanan pending yang kedaluwarsa.", 'green');
            return;
        }

        foreach ($pendingOrders as $order) {
            $db->transStart();

            // Ubah status ke cancelled
            $orderModel->update($order['id'], ['status' => 'cancelled']);

            // Dapatkan item lalu kembalikan stok secara langsung tanpa race condition query
            $items = $orderItemModel->getByOrder($order['id']);
            foreach ($items as $item) {
                $qty = (int) $item['qty'];
                $db->table('products')
                   ->where('id', $item['product_id'])
                   ->set('stok', "stok + {$qty}", false)
                   ->update();
            }

            // Kembalikan jatah Promo yang sudah terlanjur dipakai
            if (!empty($order['promo_id'])) {
                $db->table('promos')
                   ->where('id', $order['promo_id'])
                   ->where('claimed_count >', 0)
                   ->set('claimed_count', 'claimed_count - 1', false)
                   ->update();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                CLI::error("Gagal membatalkan pesanan Invoice: {$order['invoice_number']}");
            } else {
                CLI::write("Pesanan {$order['invoice_number']} berhasil dibatalkan otomatis dan stok dikembalikan.", 'green');
            }
        }

        CLI::write("Selesai memproses Auto-Cancel.", 'green');
    }
}
