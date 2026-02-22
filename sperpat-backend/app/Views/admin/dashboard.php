<!-- Admin Dashboard -->
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h6>Admin Panel</h6>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('/admin/dashboard') ?>" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="<?= base_url('/admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a></li>
            <li><a href="<?= base_url('/admin/categories') ?>"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a href="<?= base_url('/admin/orders') ?>"><i class="bi bi-bag"></i> Pesanan</a></li>
            <li><a href="<?= base_url('/admin/reports/sales') ?>"><i class="bi bi-graph-up-arrow"></i> Lap. Penjualan</a></li>
            <li><a href="<?= base_url('/admin/reports/profit') ?>"><i class="bi bi-currency-dollar"></i> Lap. Laba</a></li>
            <li><a href="<?= base_url('/admin/reports/stock') ?>"><i class="bi bi-archive"></i> Lap. Stok</a></li>
        </ul>
    </aside>
    
    <!-- Content -->
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800">Dashboard</h3>
                <p class="text-secondary mb-0">Selamat datang, <?= esc($user['name'] ?? 'Admin') ?></p>
            </div>
            <span class="badge-accent"><i class="bi bi-calendar me-1"></i><?= date('d M Y') ?></span>
        </div>
        
        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(13,110,253,0.15);color:var(--primary);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-value"><?= $totalProducts ?? 0 ?></div>
                    <div class="stat-label">Total Produk</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:var(--success);">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div class="stat-value"><?= $totalOrders ?? 0 ?></div>
                    <div class="stat-label">Total Pesanan</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(0,180,216,0.15);color:var(--accent);">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-value"><?= $totalCustomers ?? 0 ?></div>
                    <div class="stat-label">Total Customer</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:var(--warning);">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-value"><?= $pendingOrders ?? 0 ?></div>
                    <div class="stat-label">Pesanan Pending</div>
                </div>
            </div>
        </div>
        
        <!-- Revenue -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card-dark">
                    <h5 class="fw-700 mb-3">Pendapatan Bulan Ini</h5>
                    <h2 class="text-accent fw-800">Rp <?= number_format($monthlyRevenue ?? 0, 0, ',', '.') ?></h2>
                    <p class="text-secondary mt-2 mb-0">Periode: <?= date('F Y') ?></p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-dark">
                    <h5 class="fw-700 mb-3"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Stok Menipis</h5>
                    <?php if (!empty($lowStock)): ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach (array_slice($lowStock, 0, 5) as $ls): ?>
                                <li class="d-flex justify-content-between py-2 border-bottom border-dark">
                                    <span class="text-truncate me-2"><?= esc($ls['name']) ?></span>
                                    <span class="badge bg-danger"><?= $ls['stok'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-secondary mb-0">Semua stok aman ✅</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card-dark">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-700 mb-0">Pesanan Terbaru</h5>
                <a href="<?= base_url('/admin/orders') ?>" class="text-accent">Lihat Semua →</a>
            </div>
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach (array_slice($recentOrders, 0, 10) as $order): ?>
                                <tr>
                                    <td><a href="<?= base_url('/admin/orders/' . $order['id']) ?>"><?= esc($order['invoice_number']) ?></a></td>
                                    <td><?= esc($order['customer_name'] ?? '-') ?></td>
                                    <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                                    <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td class="text-secondary"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Belum ada pesanan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
