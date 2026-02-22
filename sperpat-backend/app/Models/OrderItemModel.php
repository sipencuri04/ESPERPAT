<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['order_id', 'product_id', 'qty', 'harga', 'subtotal'];
    protected $useTimestamps    = false;

    public function getByOrder(int $orderId)
    {
        return $this->select('order_items.*, products.name as product_name, products.image as product_image, products.harga_beli')
                    ->join('products', 'products.id = order_items.product_id', 'left')
                    ->where('order_items.order_id', $orderId)
                    ->findAll();
    }

    public function getProfitByOrder(int $orderId)
    {
        $items = $this->getByOrder($orderId);
        $totalProfit = 0;
        foreach ($items as &$item) {
            $item['laba'] = ($item['harga'] - $item['harga_beli']) * $item['qty'];
            $totalProfit += $item['laba'];
        }
        return ['items' => $items, 'total_profit' => $totalProfit];
    }
}
