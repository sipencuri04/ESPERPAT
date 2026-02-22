<?php

namespace App\Models;

use CodeIgniter\Model;

class PromoModel extends Model
{
    protected $table            = 'promos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'discount_type',
        'discount_value',
        'quota',
        'claimed_count',
        'product_id',
        'customer_id',
        'valid_from',
        'valid_to',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    public function calculateDiscount($promoCode, $customerId, $itemsData, $totalOrderValue)
    {
        $promo = $this->where('code', $promoCode)
                      ->where('status', 'active')
                      ->first();

        if (!$promo) {
            return ['success' => false, 'message' => 'Kode kupon tidak valid atau tidak aktif.'];
        }

        if (!empty($promo['valid_to']) && strtotime($promo['valid_to']) < time()) {
            return ['success' => false, 'message' => 'Kode kupon telah kedaluwarsa.'];
        }

        if (!empty($promo['valid_from']) && strtotime($promo['valid_from']) > time()) {
            return ['success' => false, 'message' => 'Kode kupon belum aktif.'];
        }

        if (!empty($promo['quota'])) {
            if ($promo['claimed_count'] >= $promo['quota']) {
                 return ['success' => false, 'message' => 'Kuota voucher sudah habis / fully claimed.'];
            }
        }

        if (!empty($promo['customer_id']) && $promo['customer_id'] != $customerId) {
            return ['success' => false, 'message' => 'Kupon ini tidak berlaku untuk akun Anda.'];
        }

        $applicableTotal = 0;
        
        if (!empty($promo['product_id'])) {
            // Berlaku untuk produk spesifik
            $hasProduct = false;
            foreach ($itemsData as $item) {
                if ($item['product_id'] == $promo['product_id']) {
                    $hasProduct = true;
                    $applicableTotal += $item['subtotal'];
                }
            }
            if (!$hasProduct) {
                return ['success' => false, 'message' => 'Kupon ini tidak berlaku untuk produk di keranjang Anda.'];
            }
        } else {
            // Berlaku untuk semua
            $applicableTotal = $totalOrderValue;
        }

        // Hitung diskon
        $discountAmount = 0;
        if ($promo['discount_type'] === 'percent') {
            $discountAmount = ($promo['discount_value'] / 100) * $applicableTotal;
        } else {
            // nominal
            $discountAmount = $promo['discount_value'];
            // Jangan sampai diskon lebih besar dari total
            if ($discountAmount > $applicableTotal) {
                $discountAmount = $applicableTotal;
            }
        }

        return [
            'success'  => true,
            'promo_id' => $promo['id'],
            'discount' => $discountAmount,
            'message'  => 'Kupon berhasil diterapkan!'
        ];
    }
}
