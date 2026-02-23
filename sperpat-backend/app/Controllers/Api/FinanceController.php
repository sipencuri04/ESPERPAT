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
        
        // Headers
        $headers = ['A1' => 'Invoice', 'B1' => 'Customer', 'C1' => 'Total Sales', 'D1' => 'Profit', 'E1' => 'Status'];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE5E7EB');
        }

        $row = 2;
        foreach ($orders as $order) {
            $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
            $sheet->setCellValue('A' . $row, $order['invoice_number']);
            $sheet->setCellValue('B' . $row, $order['customer_name'] ?? 'Guest');
            $sheet->setCellValue('C' . $row, $order['total']);
            $sheet->setCellValue('D' . $row, $profitData['total_profit']);
            $sheet->setCellValue('E' . $row, strtoupper($order['status']));
            
            // Format Currency
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0_-');
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0_-');
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
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

        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        $period = date('F Y', strtotime("$year-$monthStr-01"));

        $html = "
        <style>
            body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #8b5cf6; padding-bottom: 15px; }
            .header h1 { color: #4c1d95; margin: 0 0 5px 0; font-size: 24px; }
            .header p { margin: 0; color: #6b7280; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
            th { background-color: #f5f3ff; color: #4c1d95; text-align: left; padding: 12px; border-bottom: 2px solid #ddd; }
            td { padding: 10px 12px; border-bottom: 1px solid #eee; }
            .text-right { text-align: right; }
            .total-row { background-color: #f8fafc; font-weight: bold; border-top: 2px solid #333; }
        </style>
        <div class='header'>
            <h1>Laporan Keuangan ESPERPAT</h1>
            <p>Periode: {$period}</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th class='text-right'>Total Transaksi</th>
                    <th class='text-right'>Laba Bersih</th>
                </tr>
            </thead>
            <tbody>";
        
        $grandTotal = 0;
        $grandProfit = 0;
        foreach ($orders as $order) {
            $profitData = $this->orderItemModel->getProfitByOrder($order['id']);
            $custName = $order['customer_name'] ?: 'Guest';
            $html .= "<tr>
                <td>{$order['invoice_number']}</td>
                <td>{$custName}</td>
                <td class='text-right'>Rp " . number_format($order['total'], 0, ',', '.') . "</td>
                <td class='text-right'>Rp " . number_format($profitData['total_profit'], 0, ',', '.') . "</td>
            </tr>";
            $grandTotal += $order['total'];
            $grandProfit += $profitData['total_profit'];
        }
        
        $html .= "<tr class='total-row'>
            <td colspan='2' class='text-right'>GRAND TOTAL</td>
            <td class='text-right'>Rp " . number_format($grandTotal, 0, ',', '.') . "</td>
            <td class='text-right'>Rp " . number_format($grandProfit, 0, ',', '.') . "</td>
        </tr>
        </tbody></table>";

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Finance_Report_{$monthStr}_{$year}.pdf";
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $dompdf->output();
        exit;
    }
}
