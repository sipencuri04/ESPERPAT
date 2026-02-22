<div class="admin-layout">
    <?= view('superuser/_sidebar', ['active' => 'settings']) ?>

    <div class="admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-800">Pengaturan</h3>
                <p class="text-secondary mb-0">Pengaturan global untuk sistem ESPERPAT.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="card-dark">
            <p class="text-secondary mb-0">
                Halaman ini disiapkan untuk pengaturan global. Tambahkan konfigurasi sesuai kebutuhan (misalnya: pengaturan SMTP, pembayaran, atau branding).
            </p>
        </div>
    </div>
</div>
