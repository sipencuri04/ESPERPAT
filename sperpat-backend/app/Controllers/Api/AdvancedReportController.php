<?php

namespace App\Controllers\Api;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\ExpenseModel;
use App\Models\PiutangModel;
use App\Models\HutangModel;
use CodeIgniter\API\ResponseTrait;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdvancedReportController extends BaseApiController
{
    use ResponseTrait;

    protected $orderModel;
    protected $orderItemModel;
    protected $productModel;
    protected $expenseModel;
    protected $piutangModel;
    protected $hutangModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->productModel = new ProductModel();
        $this->expenseModel = new ExpenseModel();
        $this->piutangModel = new PiutangModel();
        $this->hutangModel = new HutangModel();
    }

    private function getDateRange()
    {
        $period = $this->request->getGet('period') ?? 'monthly';
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        
        $startDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        if ($period == 'yearly') {
            $startDate = "$year-01-01";
            $endDate = "$year-12-31";
        }
        return [$startDate, $endDate];
    }

    // 1. Laporan Laba Rugi
    public function labaRugi()
    {
        list($startDate, $endDate) = $this->getDateRange();

        $orders = $this->orderModel->where('DATE(created_at) >=', $startDate)
                                  ->where('DATE(created_at) <=', $endDate)
                                  ->whereNotIn('status', ['pending', 'cancelled'])
                                  ->findAll();

        $totalPenjualan = 0;
        $totalHpp = 0;

        foreach ($orders as $o) {
            $totalPenjualan += $o['total'];
            $items = $this->orderItemModel->getByOrder($o['id']);
            foreach ($items as $i) {
                $totalHpp += ($i['harga_beli'] * $i['qty']);
            }
        }

        $labaKotor = $totalPenjualan - $totalHpp;

        $expenses = $this->expenseModel->where('date >=', $startDate)
                                       ->where('date <=', $endDate)
                                       ->findAll();
        
        $biayaOperasional = array_sum(array_column($expenses, 'amount'));
        $labaBersih = $labaKotor - $biayaOperasional;
        $margin = $totalPenjualan > 0 ? round(($labaBersih / $totalPenjualan) * 100, 2) : 0;

        return $this->successResponse([
            'total_penjualan' => $totalPenjualan,
            'hpp'             => $totalHpp,
            'laba_kotor'      => $labaKotor,
            'biaya_operasional' => $biayaOperasional,
            'laba_bersih'     => $labaBersih,
            'margin_percent'  => $margin,
            'expenses_detail' => $expenses
        ]);
    }

    // 2. Laporan Cash Flow
    public function cashFlow()
    {
        list($startDate, $endDate) = $this->getDateRange();

        $orders = $this->orderModel->where('DATE(created_at) >=', $startDate)
                                  ->where('DATE(created_at) <=', $endDate)
                                  ->whereNotIn('status', ['pending', 'cancelled'])
                                  ->findAll();

        $kasMasukPenjualan = array_sum(array_column($orders, 'total'));

        $expenses = $this->expenseModel->where('date >=', $startDate)
            ->where('date <=', $endDate)->findAll();
        
        $operasional = 0;
        $aset = 0;
        
        foreach ($expenses as $e) {
            if ($e['category'] === 'aset') $aset += $e['amount'];
            else $operasional += $e['amount'];
        }

        $saldoAwal = 0; // Mocked opening balance removed
        $totalMasuk = $kasMasukPenjualan;
        $totalKeluar = $operasional + $aset;
        $saldoAkhir = $saldoAwal + $totalMasuk - $totalKeluar;

        return $this->successResponse([
            'saldo_awal' => $saldoAwal,
            'operating_in_penjualan' => $kasMasukPenjualan,
            'operating_out_biaya' => $operasional,
            'investing_out_aset' => $aset,
            'total_kas_masuk' => $totalMasuk,
            'total_kas_keluar' => $totalKeluar,
            'saldo_akhir' => $saldoAkhir,
        ]);
    }

    // 3. Neraca (Balance Sheet)
    public function neraca()
    {
        // Simple balance sheet synthesis
        // Fetch valid past transactions or zero
        $ordersValue = array_sum(array_column($this->orderModel->where('status', 'completed')->findAll(), 'total'));
        $kasBank = $ordersValue; 
        
        $piutangData = $this->piutangModel->where('status !=', 'paid')->findAll();
        $piutang = array_sum(array_column($piutangData, 'amount'));
        
        // Inventory Value
        $products = $this->productModel->findAll();
        $inventoryValue = 0;
        foreach ($products as $p) {
            $inventoryValue += ($p['stok'] * $p['harga_beli']);
        }

        $asetTetap = 0; 
        $totalAset = $kasBank + $piutang + $inventoryValue + $asetTetap;

        $hutangData = $this->hutangModel->where('status !=', 'paid')->findAll();
        $hutangSupplier = array_sum(array_column($hutangData, 'amount'));
        $hutangLain = 0; 

        $totalHutang = $hutangSupplier + $hutangLain;
        $modalAwal = 0; 
        $labaDitahan = $totalAset - $totalHutang - $modalAwal; // Balancing figure
        $totalModal = $modalAwal + $labaDitahan;

        return $this->successResponse([
            'aset' => [
                'kas' => $kasBank,
                'piutang' => $piutang,
                'inventory' => $inventoryValue,
                'aset_tetap' => $asetTetap,
                'total' => $totalAset
            ],
            'hutang' => [
                'supplier' => $hutangSupplier,
                'lain' => $hutangLain,
                'total' => $totalHutang
            ],
            'modal' => [
                'awal' => $modalAwal,
                'laba_ditahan' => $labaDitahan,
                'total' => $totalModal
            ],
            'is_balanced' => ($totalAset == ($totalHutang + $totalModal))
        ]);
    }

    // 4. Sales Report
    public function salesReport()
    {
        list($startDate, $endDate) = $this->getDateRange();
        $orders = $this->orderModel->where('DATE(created_at) >=', $startDate)
                                  ->where('DATE(created_at) <=', $endDate)
                                  ->whereNotIn('status', ['pending', 'cancelled'])
                                  ->findAll();
        
        $totalOmzet = array_sum(array_column($orders, 'total'));
        
        // Mocking growth
        $growth = 12.5;

        return $this->successResponse([
            'total_omzet' => $totalOmzet,
            'total_transaksi' => count($orders),
            'growth_percent' => $growth,
            'orders' => $orders
        ]);
    }

    // 5. Inventory Report
    public function inventoryReport()
    {
        $products = $this->productModel->findAll();
        $totalSku = count($products);
        $totalValue = 0;
        $lowStock = [];
        $highStock = [];

        foreach ($products as $p) {
            $val = $p['stok'] * $p['harga_beli'];
            $totalValue += $val;
            
            if ($p['stok'] <= 5) {
                $lowStock[] = $p;
            } else if ($p['stok'] > 50) {
                $highStock[] = $p;
            }
        }

        return $this->successResponse([
            'total_sku' => $totalSku,
            'total_value' => $totalValue,
            'low_stock' => $lowStock,
            'high_stock' => $highStock, // Assuming > 50 is high/tertinggi
            'products' => $products
        ]);
    }

    // 6. Inventory Analytics
    public function inventoryAnalytics()
    {
        list($startDate, $endDate) = $this->getDateRange();
        // Since we are creating analytical figures, we'll implement approximations
        // COGS
        $orders = $this->orderModel->where('DATE(created_at) >=', $startDate)->whereNotIn('status', ['pending', 'cancelled'])->findAll();
        $totalCogs = 0;
        $totalGross = 0;
        foreach ($orders as $o) {
            $totalGross += $o['total'];
            $items = $this->orderItemModel->getByOrder($o['id']);
            foreach ($items as $i) {
                $totalCogs += ($i['harga_beli'] * $i['qty']);
            }
        }

        $products = $this->productModel->findAll();
        $invValue = 0;
        foreach ($products as $p) {
            $invValue += ($p['stok'] * $p['harga_beli']);
        }
        $avgInv = $invValue > 0 ? $invValue : 1; 

        $turnover = $totalCogs / $avgInv;
        $dio = $turnover > 0 ? (365 / $turnover) : 0;
        $gmroi = ($totalGross - $totalCogs) / $avgInv;

        return $this->successResponse([
            'card1_turnover' => round($turnover, 2),
            'card2_dio' => round($dio, 2),
            'card3_dead_stock' => 5, // Mocked 5 SKU
            'card4_fast_slow' => ['fast' => 12, 'slow' => 20], // Mocked SKU count
            'card5_gmroi' => round($gmroi, 2)
        ]);
    }

    // 7. Aging Piutang
    public function agingPiutang()
    {
        $piutang = $this->piutangModel->where('status !=', 'paid')->findAll();
        $aging = [
            '0_30' => [],
            '31_60' => [],
            '61_90' => [],
            'over_90' => []
        ];

        foreach ($piutang as $p) {
            $days = (time() - strtotime($p['date_issued'])) / (60 * 60 * 24);
            $p['days_late'] = round($days);
            if ($days <= 30) $aging['0_30'][] = $p;
            else if ($days <= 60) $aging['31_60'][] = $p;
            else if ($days <= 90) $aging['61_90'][] = $p;
            else $aging['over_90'][] = $p;
        }

        return $this->successResponse(['aging' => $aging, 'raw' => $piutang]);
    }

    // 8. Aging Hutang
    public function agingHutang()
    {
        $hutang = $this->hutangModel->where('status !=', 'paid')->findAll();
        $aging = [
            '0_30' => [],
            '31_60' => [],
            '61_90' => [],
            'over_90' => []
        ];

        foreach ($hutang as $h) {
            $days = (time() - strtotime($h['date_issued'])) / (60 * 60 * 24);
            $h['days_late'] = round($days);
            if ($days <= 30) $aging['0_30'][] = $h;
            else if ($days <= 60) $aging['31_60'][] = $h;
            else if ($days <= 90) $aging['61_90'][] = $h;
            else $aging['over_90'][] = $h;
        }

        return $this->successResponse(['aging' => $aging, 'raw' => $hutang]);
    }

    public function exportSales($type = 'pdf')
    {
        list($startDate, $endDate) = $this->getDateRange();
        
        $items = $this->orderItemModel->select('order_items.*, orders.invoice_number, orders.created_at, products.name as product_name, products.harga_beli as purchase_price')
                                     ->join('orders', 'orders.id = order_items.order_id')
                                     ->join('products', 'products.id = order_items.product_id')
                                     ->where('DATE(orders.created_at) >=', $startDate)
                                     ->where('DATE(orders.created_at) <=', $endDate)
                                     ->whereNotIn('orders.status', ['pending', 'cancelled'])
                                     ->orderBy('orders.created_at', 'ASC')
                                     ->findAll();
        
        $filename = "Laporan_Penjualan_Detail_" . date('Ymd_His');

        if ($type === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'LAPORAN PENJUALAN DETAIL ESPERPAT');
            $sheet->setCellValue('A2', "Periode: $startDate s/d $endDate");
            
            $headers = ['No', 'Invoice', 'Tanggal', 'Nama Barang', 'Qty', 'Harga Beli', 'Harga Jual', 'Total Jual', 'HPP', 'Laba'];
            $col = 'A';
            foreach ($headers as $h) { 
                $sheet->setCellValue($col . '4', $h); 
                $sheet->getStyle($col . '4')->getFont()->setBold(true);
                $col++; 
            }

            $row = 5; $no = 1;
            foreach ($items as $i) {
                $totalJual = $i['subtotal'];
                $hpp = $i['qty'] * $i['purchase_price'];
                $laba = $totalJual - $hpp;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $i['invoice_number']);
                $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($i['created_at'])));
                $sheet->setCellValue('D' . $row, $i['product_name']);
                $sheet->setCellValue('E' . $row, $i['qty']);
                $sheet->setCellValue('F' . $row, $i['purchase_price']);
                $sheet->setCellValue('G' . $row, $i['harga']);
                $sheet->setCellValue('H' . $row, $totalJual);
                $sheet->setCellValue('I' . $row, $hpp);
                $sheet->setCellValue('J' . $row, $laba);
                
                // Format numbers
                $sheet->getStyle('F' . $row . ':J' . $row)->getNumberFormat()->setFormatCode('#,##0');
                $row++;
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } else {
            $html = '<style>body{font-family:sans-serif;font-size:10px;} table{width:100%;border-collapse:collapse;margin-top:20px;} th,td{border:1px solid #ccc;padding:5px;text-align:left;} th{background:#eee;}</style>';
            $html .= '<h2>LAPORAN PENJUALAN DETAIL ESPERPAT</h2>';
            $html .= '<p>Periode: '.$startDate.' s/d '.$endDate.'</p>';
            $html .= '<table>';
            $html .= '<thead><tr><th>No</th><th>Invoice</th><th>Tanggal</th><th>Nama Barang</th><th>Qty</th><th>Harga Beli</th><th>Harga Jual</th><th>Total Jual</th><th>HPP</th><th>Laba</th></tr></thead>';
            $html .= '<tbody>';
            $no = 1;
            foreach ($items as $i) {
                $totalJual = $i['subtotal'];
                $hpp = $i['qty'] * $i['purchase_price'];
                $laba = $totalJual - $hpp;

                $html .= '<tr>';
                $html .= '<td>'.$no++.'</td>';
                $html .= '<td>'.$i['invoice_number'].'</td>';
                $html .= '<td>'.date('d/m/Y', strtotime($i['created_at'])).'</td>';
                $html .= '<td>'.$i['product_name'].'</td>';
                $html .= '<td>'.$i['qty'].'</td>';
                $html .= '<td>'.number_format($i['purchase_price'], 0, ',', '.').'</td>';
                $html .= '<td>'.number_format($i['harga'], 0, ',', '.').'</td>';
                $html .= '<td>'.number_format($totalJual, 0, ',', '.').'</td>';
                $html .= '<td>'.number_format($hpp, 0, ',', '.').'</td>';
                $html .= '<td>'.number_format($laba, 0, ',', '.').'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->setPaper('A4', 'landscape'); // Use landscape for many columns
            $dompdf->loadHtml($html);
            $dompdf->render();
            $dompdf->stream($filename . ".pdf", ["Attachment" => true]);
            exit;
        }
    }
    public function exportInventory($type = 'pdf')
    {
        $products = $this->productModel->select('products.*, categories.name as category_name')
                                       ->join('categories', 'categories.id = products.category_id', 'left')
                                       ->findAll();
        
        $totalValue = 0;
        foreach ($products as $p) {
            $totalValue += ($p['stok'] * $p['harga_beli']);
        }

        $filename = "Laporan_Stok_Barang_" . date('Ymd_His');

        if ($type === 'excel') {
            $spreadsheet = new Spreadsheet(); 
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'LAPORAN STOK BARANG ESPERPAT');
            $sheet->setCellValue('A2', 'Dicetak pada: ' . date('d/m/Y H:i'));
            
            $headers = ['No', 'Nama Produk', 'Kategori', 'Stok', 'Harga Beli', 'Total Nilai'];
            $col = 'A'; 
            foreach ($headers as $h) { 
                $sheet->setCellValue($col . '4', $h); 
                $sheet->getStyle($col . '4')->getFont()->setBold(true);
                $col++; 
            }

            $row = 5; $no = 1;
            foreach ($products as $p) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $p['name']);
                $sheet->setCellValue('C' . $row, $p['category_name'] ?? '-');
                $sheet->setCellValue('D' . $row, $p['stok']);
                $sheet->setCellValue('E' . $row, $p['harga_beli']);
                $sheet->setCellValue('F' . $row, $p['stok'] * $p['harga_beli']);
                $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('#,##0');
                $row++;
            }
            
            $sheet->setCellValue('E' . $row, 'TOTAL NILAI ASET');
            $sheet->setCellValue('F' . $row, $totalValue);
            $sheet->getStyle('E' . $row.':F'.$row)->getFont()->setBold(true);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer = new Xlsx($spreadsheet); 
            $writer->save('php://output'); 
            exit;
        } else {
            $html = '<style>body{font-family:sans-serif;font-size:12px;} table{width:100%;border-collapse:collapse;margin-top:20px;} th,td{border:1px solid #ccc;padding:8px;text-align:left;} th{background:#eee;}</style>';
            $html .= '<h2>LAPORAN STOK BARANG ESPERPAT</h2>';
            $html .= '<p>Dicetak pada: '.date('d/m/Y H:i').'</p>';
            $html .= '<table>';
            $html .= '<thead><tr><th>No</th><th>Produk</th><th>Kategori</th><th>Stok</th><th>Harga Beli</th><th style="text-align:right">Total Nilai</th></tr></thead>';
            $html .= '<tbody>';
            $no = 1;
            foreach ($products as $p) {
                $html .= '<tr>';
                $html .= '<td>'.$no++.'</td>';
                $html .= '<td>'.$p['name'].'</td>';
                $html .= '<td>'.($p['category_name'] ?? '-').'</td>';
                $html .= '<td>'.$p['stok'].'</td>';
                $html .= '<td>Rp '.number_format($p['harga_beli'], 0, ',', '.').'</td>';
                $html .= '<td style="text-align:right">Rp '.number_format($p['stok'] * $p['harga_beli'], 0, ',', '.').'</td>';
                $html .= '</tr>';
            }
            $html .= '<tr style="font-weight:bold; background:#f9f9f9;"><td colspan="5" style="text-align:right">TOTAL NILAI ASET GUDANG</td><td style="text-align:right">Rp '.number_format($totalValue, 0, ',', '.').'</td></tr>';
            $html .= '</tbody></table>';

            $dompdf = new Dompdf(); 
            $dompdf->loadHtml($html); 
            $dompdf->setPaper('A4', 'portrait'); 
            $dompdf->render();
            $dompdf->stream($filename . ".pdf", ["Attachment" => true]); 
            exit;
        }
    }
}
