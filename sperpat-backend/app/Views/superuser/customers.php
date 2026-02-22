<div class="admin-layout">
    <?= view('superuser/_sidebar', ['active' => 'customers']) ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800">Kelola Customer</h3>
                <p class="text-secondary mb-0">Aktifkan atau nonaktifkan akun customer.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="card-dark">
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
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?= esc($customer['name']) ?></td>
                                    <td><?= esc($customer['email']) ?></td>
                                    <td><?= esc($customer['phone'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($customer['is_active'])): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?= base_url('/superuser/customers/toggle/' . $customer['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <?php if (!empty($customer['is_active'])): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning">Nonaktifkan</button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success">Aktifkan</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Belum ada customer</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
