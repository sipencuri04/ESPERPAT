<!-- Admin Product Form -->
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
            <h3 class="fw-800"><?= isset($product) ? 'Edit Produk' : 'Tambah Produk' ?></h3>
            <a href="<?= base_url('/admin/products') ?>" class="btn btn-outline-accent">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
        
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="card-dark">
            <form action="<?= isset($product) ? base_url('/admin/products/update/' . $product['id']) : base_url('/admin/products/store') ?>" 
                  method="post" enctype="multipart/form-data" class="form-dark">
                <?= csrf_field() ?>
                
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk *</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?= esc(old('name', $product['name'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="5"><?= esc(old('deskripsi', $product['deskripsi'] ?? '')) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Kategori *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" 
                                            <?= old('category_id', $product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Harga Beli (Rp) *</label>
                            <input type="number" class="form-control" name="harga_beli" step="0.01"
                                   value="<?= esc(old('harga_beli', $product['harga_beli'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Harga Jual (Rp) *</label>
                            <input type="number" class="form-control" name="harga_jual" step="0.01"
                                   value="<?= esc(old('harga_jual', $product['harga_jual'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stok *</label>
                            <input type="number" class="form-control" name="stok"
                                   value="<?= esc(old('stok', $product['stok'] ?? '')) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Gambar Produk</label>
                            <input type="file" class="form-control image-input" name="image" accept="image/*"
                                   data-preview="#imagePreview">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= base_url($product['image']) ?>" id="imagePreview" class="mt-2 rounded" style="max-height:150px;">
                            <?php else: ?>
                                <img src="" id="imagePreview" class="mt-2 rounded d-none" style="max-height:150px;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <hr class="border-secondary my-4">
                
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-check-lg me-2"></i><?= isset($product) ? 'Update Produk' : 'Simpan Produk' ?>
                    </button>
                    <a href="<?= base_url('/admin/products') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
