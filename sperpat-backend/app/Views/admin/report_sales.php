<!-- Admin Sales Report -->
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><h6>Admin Panel</h6></div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="<?= base_url('/admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a></li>
            <li><a href="<?= base_url('/admin/categories') ?>"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a href="<?= base_url('/admin/orders') ?>"><i class="bi bi-bag"></i> Pesanan</a></li>
            <li><a href="<?= base_url('/admin/reports/sales') ?>" class="active"><i class="bi bi-graph-up-arrow"></i> Lap. Penjualan</a></li>
            <li><a href="<?= base_url('/admin/reports/profit') ?>"><i class="bi bi-currency-dollar"></i> Lap. Laba</a></li>
            <li><a href="<?= base_url('/admin/reports/stock') ?>"><i class="bi bi-archive"></i> Lap. Stok</a></li>
        </ul>
    </aside>
    
    <div class="admin-content">
        <h3 class="fw-800 mb-4">Laporan Penjualan</h3>
        
        <!-- Filter -->
        <div class="card-dark mb-4">
            <form action="<?= base_url('/admin/reports/sales') ?>" method="get" class="form-dark">
                <div class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" name="month">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Tahun</label>
                        <select class="form-select" name="year">
                            <?php for ($y = 2024; $y <= 2027; $y++): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-accent">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Summary -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value text-accent">Rp <?= number_format($totalSales ?? 0, 0, ',', '.') ?></div>
                    <div class="stat-label">Total Penjualan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value"><?= count($orders ?? []) ?></div>
                    <div class="stat-label">Jumlah Transaksi</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-value">Rp <?= count($orders) > 0 ? number_format($totalSales / count($orders), 0, ',', '.') : 0 ?></div>
                    <div class="stat-label">Rata-rata per Transaksi</div>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="card-dark">
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Status</th><th>Tanggal</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-600"><?= esc($order['invoice_number']) ?></td>
                                    <td><?= esc($order['customer_name'] ?? '-') ?></td>
                                    <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                                    <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td class="text-secondary"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Tidak ada data penjualan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
