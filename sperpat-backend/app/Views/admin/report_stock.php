<!-- Admin Stock Report -->
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><h6>Admin Panel</h6></div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="<?= base_url('/admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a></li>
            <li><a href="<?= base_url('/admin/categories') ?>"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a href="<?= base_url('/admin/orders') ?>"><i class="bi bi-bag"></i> Pesanan</a></li>
            <li><a href="<?= base_url('/admin/reports/sales') ?>"><i class="bi bi-graph-up-arrow"></i> Lap. Penjualan</a></li>
            <li><a href="<?= base_url('/admin/reports/profit') ?>"><i class="bi bi-currency-dollar"></i> Lap. Laba</a></li>
            <li><a href="<?= base_url('/admin/reports/stock') ?>" class="active"><i class="bi bi-archive"></i> Lap. Stok</a></li>
        </ul>
    </aside>
    
    <div class="admin-content">
        <h3 class="fw-800 mb-4">Laporan Stok</h3>
        
        <?php if (!empty($lowStock)): ?>
            <div class="alert alert-warning d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span><strong><?= count($lowStock) ?> produk</strong> memiliki stok menipis (≤ 10 unit)</span>
            </div>
        <?php endif; ?>
        
        <div class="card-dark">
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr><th>#</th><th>Produk</th><th>Kategori</th><th>Stok</th><th>Harga Beli</th><th>Nilai Stok</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-600"><?= esc($p['name']) ?></td>
                                    <td><span class="badge-accent"><?= esc($p['category_name'] ?? '-') ?></span></td>
                                    <td>
                                        <?php if ($p['stok'] <= 5): ?>
                                            <span class="badge bg-danger"><?= $p['stok'] ?></span>
                                        <?php elseif ($p['stok'] <= 10): ?>
                                            <span class="badge bg-warning text-dark"><?= $p['stok'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $p['stok'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="fw-600">Rp <?= number_format($p['harga_beli'] * $p['stok'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
