<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'invoice_number', 'user_id', 'total', 'alamat', 'status',
        'metode_pembayaran', 'nomor_resi', 'catatan', 'promo_id', 'discount_amount',
        'bukti_pembayaran'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWithUser()
    {
        return $this->select('orders.*, users.name as customer_name, users.email as customer_email')
                    ->join('users', 'users.id = orders.user_id', 'left')
                    ->orderBy('orders.created_at', 'DESC')
                    ->findAll();
    }

    public function getByUser(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getByStatus(string $status)
    {
        return $this->select('orders.*, users.name as customer_name')
                    ->join('users', 'users.id = orders.user_id', 'left')
                    ->where('orders.status', $status)
                    ->orderBy('orders.created_at', 'DESC')
                    ->findAll();
    }

    public function generateInvoiceNumber(): string
    {
        $date = date('Ymd');
        $lastOrder = $this->like('invoice_number', "INV-{$date}", 'after')
                         ->orderBy('id', 'DESC')
                         ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder['invoice_number'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "INV-{$date}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getDailyReport(string $date)
    {
        return $this->select('orders.*, users.name as customer_name')
                    ->join('users', 'users.id = orders.user_id', 'left')
                    ->where('DATE(orders.created_at)', $date)
                    ->where('orders.status !=', 'cancelled')
                    ->findAll();
    }

    public function getMonthlyReport(int $month, int $year)
    {
        return $this->select('orders.*, users.name as customer_name')
                    ->join('users', 'users.id = orders.user_id', 'left')
                    ->where('MONTH(orders.created_at)', $month)
                    ->where('YEAR(orders.created_at)', $year)
                    ->where('orders.status !=', 'cancelled')
                    ->findAll();
    }
}
