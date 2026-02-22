<?php

namespace App\Controllers\Api;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;

class ReportController extends BaseApiController
{
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel   = new ProductModel();
    }

    public function sales()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year  = (int) ($this->request->getGet('year') ?? date('Y'));

        $orders = $this->orderModel->getMonthlyReport($month, $year);

        $totalSales  = 0;
        $totalOrders = count($orders);

        foreach ($orders as $order) {
            $totalSales += $order['total'];
        }

        return $this->successResponse([
            'period'       => "{$month}/{$year}",
            'total_orders' => $totalOrders,
            'total_sales'  => $totalSales,
            'orders'       => $orders,
        ]);
    }

    public function profit()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year  = (int) ($this->request->getGet('year') ?? date('Y'));

        $orders = $this->orderModel->getMonthlyReport($month, $year);

        $totalProfit = 0;
        $totalSales  = 0;
        $orderDetails = [];

        foreach ($orders as $order) {
            $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
            $totalProfit += $profitData['total_profit'];
            $totalSales += $order['total'];

            $orderDetails[] = [
                'order'        => $order,
                'items'        => $profitData['items'],
                'order_profit' => $profitData['total_profit'],
            ];
        }

        return $this->successResponse([
            'period'       => "{$month}/{$year}",
            'total_sales'  => $totalSales,
            'total_profit' => $totalProfit,
            'margin'       => $totalSales > 0 ? round(($totalProfit / $totalSales) * 100, 2) : 0,
            'orders'       => $orderDetails,
        ]);
    }

    public function stock()
    {
        $products  = $this->productModel->getWithCategory();
        $lowStock  = $this->productModel->getLowStock(10);

        $totalProducts  = count($products);
        $totalLowStock  = count($lowStock);
        $totalValue     = 0;

        foreach ($products as $product) {
            $totalValue += $product['harga_beli'] * $product['stok'];
        }

        return $this->successResponse([
            'total_products'  => $totalProducts,
            'total_low_stock' => $totalLowStock,
            'total_value'     => $totalValue,
            'products'        => $products,
            'low_stock'       => $lowStock,
        ]);
    }
}
