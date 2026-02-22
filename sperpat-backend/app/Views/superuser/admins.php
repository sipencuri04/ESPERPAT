<div class="admin-layout">
    <?= view('superuser/_sidebar', ['active' => 'admins']) ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800">Kelola Admin</h3>
                <p class="text-secondary mb-0">Tambah, ubah, atau hapus akun admin.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <div><?= esc($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card-dark mb-4">
            <h5 class="fw-700 mb-3">Tambah Admin</h5>
            <form action="<?= base_url('/superuser/admins/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Nama" value="<?= old('name') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="phone" class="form-control" placeholder="Telepon" value="<?= old('phone') ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Simpan Admin</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-dark">
            <h5 class="fw-700 mb-3">Daftar Admin</h5>
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($admins)): ?>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?= esc($admin['name']) ?></td>
                                    <td><?= esc($admin['email']) ?></td>
                                    <td><?= esc($admin['phone'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($admin['is_active'])): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($admin['role'] === 'superuser'): ?>
                                            <span class="text-secondary">Superuser</span>
                                        <?php else: ?>
                                            <form action="<?= base_url('/superuser/admins/update/' . $admin['id']) ?>" method="post" class="d-flex flex-wrap gap-2 mb-2">
                                                <?= csrf_field() ?>
                                                <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($admin['name']) ?>" required>
                                                <input type="email" name="email" class="form-control form-control-sm" value="<?= esc($admin['email']) ?>" required>
                                                <input type="text" name="phone" class="form-control form-control-sm" value="<?= esc($admin['phone'] ?? '') ?>" placeholder="Telepon">
                                                <input type="password" name="password" class="form-control form-control-sm" placeholder="Password baru">
                                                <select name="is_active" class="form-select form-select-sm" style="min-width:120px;">
                                                    <option value="1" <?= !empty($admin['is_active']) ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="0" <?= empty($admin['is_active']) ? 'selected' : '' ?>>Nonaktif</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                            </form>
                                            <form action="<?= base_url('/superuser/admins/delete/' . $admin['id']) ?>" method="post" onsubmit="return confirm('Hapus admin ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Belum ada admin</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
