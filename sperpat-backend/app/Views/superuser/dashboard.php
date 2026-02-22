<div class="admin-layout">
    <?= view('superuser/_sidebar', ['active' => 'dashboard']) ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800">Dashboard Superuser</h3>
                <p class="text-secondary mb-0">Halo, <?= esc($user['name'] ?? 'Superuser') ?></p>
            </div>
            <span class="badge-accent"><i class="bi bi-calendar me-1"></i><?= date('d M Y') ?></span>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(13,110,253,0.15);color:var(--primary);">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="stat-value"><?= $totalAdmins ?? 0 ?></div>
                    <div class="stat-label">Total Admin</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:var(--success);">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-value"><?= $totalCustomers ?? 0 ?></div>
                    <div class="stat-label">Total Customer</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:var(--warning);">
                        <i class="bi bi-person-x"></i>
                    </div>
                    <div class="stat-value"><?= $inactiveCustomers ?? 0 ?></div>
                    <div class="stat-label">Customer Nonaktif</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(0,180,216,0.15);color:var(--accent);">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div class="stat-value"><?= $totalOrders ?? 0 ?></div>
                    <div class="stat-label">Total Pesanan</div>
                </div>
            </div>
        </div>

        <div class="card-dark">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-700 mb-0">User Terbaru</h5>
                <a href="<?= base_url('/superuser/customers') ?>" class="text-accent">Lihat Customer</a>
            </div>
            <div class="table-responsive table-dark-custom">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach ($recentUsers as $u): ?>
                                <tr>
                                    <td><?= esc($u['name']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td><?= esc(ucfirst($u['role'])) ?></td>
                                    <td>
                                        <?php if (!empty($u['is_active'])): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-secondary"><?= !empty($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-secondary">Belum ada user</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
