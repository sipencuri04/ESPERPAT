<!-- Admin Orders Page -->
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><h6>Admin Panel</h6></div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="<?= base_url('/admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a></li>
            <li><a href="<?= base_url('/admin/categories') ?>"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a href="<?= base_url('/admin/orders') ?>" class="active"><i class="bi bi-bag"></i> Pesanan</a></li>
            <li><a href="<?= base_url('/admin/reports/sales') ?>"><i class="bi bi-graph-up-arrow"></i> Lap. Penjualan</a></li>
            <li><a href="<?= base_url('/admin/reports/profit') ?>"><i class="bi bi-currency-dollar"></i> Lap. Laba</a></li>
            <li><a href="<?= base_url('/admin/reports/stock') ?>"><i class="bi bi-archive"></i> Lap. Stok</a></li>
        </ul>
    </aside>
    
    <div class="admin-content">
        <h3 class="fw-800 mb-4">Kelola Pesanan</h3>
        
        <!-- Status Filter -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="<?= base_url('/admin/orders') ?>" class="btn btn-sm <?= empty($status) ? 'btn-accent' : 'btn-outline-accent' ?>">Semua</a>
            <a href="<?= base_url('/admin/orders?status=pending') ?>" class="btn btn-sm <?= ($status ?? '') === 'pending' ? 'btn-accent' : 'btn-outline-accent' ?>">Pending</a>
            <a href="<?= base_url('/admin/orders?status=paid') ?>" class="btn btn-sm <?= ($status ?? '') === 'paid' ? 'btn-accent' : 'btn-outline-accent' ?>">Paid</a>
            <a href="<?= base_url('/admin/orders?status=shipped') ?>" class="btn btn-sm <?= ($status ?? '') === 'shipped' ? 'btn-accent' : 'btn-outline-accent' ?>">Shipped</a>
            <a href="<?= base_url('/admin/orders?status=completed') ?>" class="btn btn-sm <?= ($status ?? '') === 'completed' ? 'btn-accent' : 'btn-outline-accent' ?>">Completed</a>
        </div>
        
        <div class="card-dark">
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Resi</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-600"><?= esc($order['invoice_number']) ?></td>
                                    <td><?= esc($order['customer_name'] ?? '-') ?></td>
                                    <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                                    <td><?= esc($order['metode_pembayaran'] ?? '-') ?></td>
                                    <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td><?= esc($order['nomor_resi'] ?? '-') ?></td>
                                    <td class="text-secondary"><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('/admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-outline-accent">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-secondary">Belum ada pesanan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
