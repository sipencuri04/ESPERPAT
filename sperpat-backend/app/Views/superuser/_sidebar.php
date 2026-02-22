<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h6>Superuser Panel</h6>
    </div>
    <ul class="sidebar-menu">
        <li><a href="<?= base_url('/superuser/dashboard') ?>" class="<?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="<?= base_url('/superuser/admins') ?>" class="<?= ($active ?? '') === 'admins' ? 'active' : '' ?>"><i class="bi bi-person-badge"></i> Admins</a></li>
        <li><a href="<?= base_url('/superuser/customers') ?>" class="<?= ($active ?? '') === 'customers' ? 'active' : '' ?>"><i class="bi bi-people"></i> Customers</a></li>
        <li><a href="<?= base_url('/superuser/settings') ?>" class="<?= ($active ?? '') === 'settings' ? 'active' : '' ?>"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>
</aside>
