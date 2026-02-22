<!-- Admin Products Page -->
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><h6>Admin Panel</h6></div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="<?= base_url('/admin/products') ?>" class="active"><i class="bi bi-box-seam"></i> Produk</a></li>
            <li><a href="<?= base_url('/admin/categories') ?>"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a href="<?= base_url('/admin/orders') ?>"><i class="bi bi-bag"></i> Pesanan</a></li>
            <li><a href="<?= base_url('/admin/reports/sales') ?>"><i class="bi bi-graph-up-arrow"></i> Lap. Penjualan</a></li>
            <li><a href="<?= base_url('/admin/reports/profit') ?>"><i class="bi bi-currency-dollar"></i> Lap. Laba</a></li>
            <li><a href="<?= base_url('/admin/reports/stock') ?>"><i class="bi bi-archive"></i> Lap. Stok</a></li>
        </ul>
    </aside>
    
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-800">Kelola Produk</h3>
            <a href="<?= base_url('/admin/products/create') ?>" class="btn btn-accent">
                <i class="bi bi-plus-lg me-2"></i>Tambah Produk
            </a>
        </div>
        
        <div class="card-dark">
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($p['image'])): ?>
                                                <img src="<?= base_url($p['image']) ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;margin-right:12px;">
                                            <?php else: ?>
                                                <div style="width:40px;height:40px;background:var(--dark-secondary);border-radius:8px;margin-right:12px;display:flex;align-items:center;justify-content:center;">
                                                    <i class="bi bi-image text-secondary"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="fw-600"><?= esc($p['name']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge-accent"><?= esc($p['category_name'] ?? '-') ?></span></td>
                                    <td>Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($p['stok'] <= 5): ?>
                                            <span class="badge bg-danger"><?= $p['stok'] ?></span>
                                        <?php elseif ($p['stok'] <= 10): ?>
                                            <span class="badge bg-warning text-dark"><?= $p['stok'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $p['stok'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('/admin/products/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-accent me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= base_url('/admin/products/delete/' . $p['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Yakin ingin menghapus produk ini?">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-secondary">Belum ada produk</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
