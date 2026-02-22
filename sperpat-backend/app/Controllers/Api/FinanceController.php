<?php

namespace App\Controllers\Api;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class FinanceController extends BaseApiController
{
    use ResponseTrait;

    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    public function growth()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));

        // Current Month
        $currentOrders = $this->orderModel->getMonthlyReport($month, $year);
        $currentSales = array_sum(array_column($currentOrders, 'total'));

        // Previous Month
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth == 0) {
            $prevMonth = 12;
            $prevYear--;
        }

        $prevOrders = $this->orderModel->getMonthlyReport($prevMonth, $prevYear);
        $prevSales = array_sum(array_column($prevOrders, 'total'));

        // Growth Percentage
        $growth = 0;
        if ($prevSales > 0) {
            $growth = (($currentSales - $prevSales) / $prevSales) * 100;
        } elseif ($currentSales > 0) {
            $growth = 100;
        }

        // Target (Hardcoded for now, can be moved to settings/DB later)
        $target = 50000000; // 50 Juta
        $achievement = ($currentSales / $target) * 100;

        return $this->successResponse([
            'current_sales' => $currentSales,
            'prev_sales' => $prevSales,
            'growth_percentage' => round($growth, 2),
            'target' => $target,
            'achievement_percentage' => round($achievement, 2),
            'status' => $growth >= 0 ? 'up' : 'down'
        ]);
    }

    public function dailyChart()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $chartData = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            $orders = $this->orderModel->getDailyReport($date);
            $sales = array_sum(array_column($orders, 'total'));
            
            $chartData[] = [
                'day' => $i,
                'sales' => $sales
            ];
        }

        return $this->successResponse($chartData);
    }

    public function monthlyChart()
    {
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            [$year, $month] = explode('-', $date);
            
            $orders = $this->orderModel->getMonthlyReport((int)$month, (int)$year);
            $totalProfit = 0;
            foreach ($orders as $order) {
                $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
                $totalProfit += $profitData['total_profit'];
            }

            $chartData[] = [
                'label' => date('M Y', strtotime("-$i months")),
                'profit' => $totalProfit
            ];
        }

        return $this->successResponse($chartData);
    }

    public function exportExcel()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        
        $orders = $this->orderModel->getMonthlyReport($month, $year);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Invoice');
        $sheet->setCellValue('B1', 'Customer');
        $sheet->setCellValue('C1', 'Total Sales');
        $sheet->setCellValue('D1', 'Profit');
        $sheet->setCellValue('E1', 'Status');

        $row = 2;
        foreach ($orders as $order) {
            $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
            $sheet->setCellValue('A' . $row, $order['invoice_number']);
            $sheet->setCellValue('B' . $row, $order['customer_name']);
            $sheet->setCellValue('C' . $row, $order['total']);
            $sheet->setCellValue('D' . $row, $profitData['total_profit']);
            $sheet->setCellValue('E' . $row, $order['status']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "Finance_Report_{$month}_{$year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $orders = $this->orderModel->getMonthlyReport($month, $year);

        $html = "<h1>Laporan Keuangan {$month}/{$year}</h1>";
        $html .= "<table border='1' width='100%' style='border-collapse: collapse;'>";
        $html .= "<tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Profit</th></tr>";
        
        $grandTotal = 0;
        $grandProfit = 0;
        foreach ($orders as $order) {
            $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
            $html .= "<tr>
                <td>{$order['invoice_number']}</td>
                <td>{$order['customer_name']}</td>
                <td>Rp " . number_format($order['total']) . "</td>
                <td>Rp " . number_format($profitData['total_profit']) . "</td>
            </tr>";
            $grandTotal += $order['total'];
            $grandProfit += $profitData['total_profit'];
        }
        $html .= "<tr><td colspan='2'><b>TOTAL</b></td><td><b>Rp " . number_format($grandTotal) . "</b></td><td><b>Rp " . number_format($grandProfit) . "</b></td></tr>";
        $html .= "</table>";

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Finance_Report_{$month}_{$year}.pdf";
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $dompdf->output();
        exit;
    }
}
