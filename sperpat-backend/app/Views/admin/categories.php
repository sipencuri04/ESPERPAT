<!-- Admin Categories Page -->
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><h6>Admin Panel</h6></div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="<?= base_url('/admin/products') ?>"><i class="bi bi-box-seam"></i> Produk</a></li>
            <li><a href="<?= base_url('/admin/categories') ?>" class="active"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a href="<?= base_url('/admin/orders') ?>"><i class="bi bi-bag"></i> Pesanan</a></li>
            <li><a href="<?= base_url('/admin/reports/sales') ?>"><i class="bi bi-graph-up-arrow"></i> Lap. Penjualan</a></li>
            <li><a href="<?= base_url('/admin/reports/profit') ?>"><i class="bi bi-currency-dollar"></i> Lap. Laba</a></li>
            <li><a href="<?= base_url('/admin/reports/stock') ?>"><i class="bi bi-archive"></i> Lap. Stok</a></li>
        </ul>
    </aside>
    
    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-800">Kelola Kategori</h3>
            <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
            </button>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-dark">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-700 mb-1"><?= esc($cat['name']) ?></h5>
                                    <span class="text-secondary"><?= $cat['product_count'] ?? 0 ?> produk</span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm text-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-dark">
                                        <li><a class="dropdown-item" href="#" onclick="editCategory(<?= $cat['id'] ?>, '<?= esc($cat['name']) ?>', '<?= esc($cat['description'] ?? '') ?>')">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a></li>
                                        <li>
                                            <form action="<?= base_url('/admin/categories/delete/' . $cat['id']) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button class="dropdown-item text-danger" data-confirm="Yakin ingin menghapus kategori ini?">
                                                    <i class="bi bi-trash me-2"></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php if (!empty($cat['description'])): ?>
                                <p class="text-secondary mb-0 small"><?= esc($cat['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-secondary">Belum ada kategori.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade modal-dark" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('/admin/categories/store') ?>" method="post" class="form-dark">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-700">Tambah Kategori</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade modal-dark" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" method="post" class="form-dark">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-700">Edit Kategori</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" id="editCatName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" id="editCatDesc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-accent">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, desc) {
    document.getElementById('editCategoryForm').action = '<?= base_url('/admin/categories/update/') ?>' + id;
    document.getElementById('editCatName').value = name;
    document.getElementById('editCatDesc').value = desc;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>
