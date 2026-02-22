<!-- Admin Order Detail Page -->
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800">Detail Pesanan</h3>
                <p class="text-secondary mb-0"><?= esc($order['invoice_number']) ?></p>
            </div>
            <a href="<?= base_url('/admin/orders') ?>" class="btn btn-outline-accent"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
        </div>
        
        <div class="row g-4">
            <!-- Order Info -->
            <div class="col-lg-8">
                <div class="card-dark mb-4">
                    <h5 class="fw-700 mb-3">Item Pesanan</h5>
                    <div class="table-responsive table-dark-custom">
                        <table class="table">
                            <thead>
                                <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td><?= esc($item['product_name'] ?? 'Produk #' . $item['product_id']) ?></td>
                                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                            <td><?= $item['qty'] ?></td>
                                            <td class="fw-600">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="3" class="text-end fw-700">Total:</td>
                                    <td class="fw-800 text-accent">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card-dark mb-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-person me-2"></i>Informasi Customer</h5>
                    <p class="mb-1"><strong>Nama:</strong> <?= esc($order['customer_name'] ?? '-') ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= esc($order['customer_email'] ?? '-') ?></p>
                    <p class="mb-1"><strong>Telepon:</strong> <?= esc($order['customer_phone'] ?? '-') ?></p>
                    <p class="mb-0"><strong>Alamat:</strong> <?= esc($order['alamat'] ?? '-') ?></p>
                </div>
                
                <!-- Status Update -->
                <div class="card-dark mb-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-arrow-repeat me-2"></i>Update Status</h5>
                    <p class="mb-2">Status saat ini: <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></p>
                    <form action="<?= base_url('/admin/orders/' . $order['id'] . '/status') ?>" method="post" class="form-dark">
                        <?= csrf_field() ?>
                        <select class="form-select mb-3" name="status">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-accent btn-sm w-100">Update Status</button>
                    </form>
                </div>
                
                <!-- Resi -->
                <div class="card-dark">
                    <h5 class="fw-700 mb-3"><i class="bi bi-truck me-2"></i>Nomor Resi</h5>
                    <p class="text-secondary mb-2">Resi: <?= esc($order['nomor_resi'] ?? 'Belum diisi') ?></p>
                    <form action="<?= base_url('/admin/orders/' . $order['id'] . '/resi') ?>" method="post" class="form-dark">
                        <?= csrf_field() ?>
                        <input type="text" class="form-control mb-3" name="nomor_resi" value="<?= esc($order['nomor_resi'] ?? '') ?>" placeholder="Masukkan nomor resi">
                        <button type="submit" class="btn btn-accent btn-sm w-100">Simpan Resi & Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
