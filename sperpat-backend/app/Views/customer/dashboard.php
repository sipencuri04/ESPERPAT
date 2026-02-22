<?php
// Dashboard - DROPS 2026 Aesthetic Refined
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5 pt-2">
        <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">My profile</h6>
        <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
    </div>

    <!-- Profile Hero -->
    <div class="text-center mb-5">
        <div class="profile-avatar-drops mx-auto mb-3">
            <img src="https://i.pravatar.cc/150?u=rex" alt="Rex">
        </div>
        <h3 class="fw-900 text-dark mb-1"><?= esc($user['name']) ?></h3>
        <div class="text-muted fw-700 small">Member since <?= date('Y') ?></div>
    </div>

    <!-- Stats Pill -->
    <div class="stats-pill-drops mb-5">
        <div class="stat-item">
            <div class="fw-900"><?= $totalOrders ?? 0 ?></div>
            <div class="text-muted extra-small fw-700 uppercase">Orders</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="fw-900">12</div>
            <div class="text-muted extra-small fw-700 uppercase">Vouchers</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="fw-900">750</div>
            <div class="text-muted extra-small fw-700 uppercase">Points</div>
        </div>
    </div>

    <!-- Navigation List -->
    <div class="nav-list-drops mb-5">
        <a href="<?= base_url('/customer/orders') ?>" class="nav-item-drops">
            <div class="d-flex align-items-center gap-3">
                <div class="nav-icon-box bg-primary-light">
                    <i class="bi bi-bag-check-fill text-primary"></i>
                </div>
                <div class="fw-800 text-dark">Order History</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
        </a>
        <a href="<?= base_url('/customer/cart') ?>" class="nav-item-drops">
            <div class="d-flex align-items-center gap-3">
                <div class="nav-icon-box" style="background: #f1f1f1;">
                    <i class="bi bi-cart-fill text-dark"></i>
                </div>
                <div class="fw-800 text-dark">My Shopping Cart</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
        </a>
        <a href="<?= base_url('/customer/profile') ?>" class="nav-item-drops">
            <div class="d-flex align-items-center gap-3">
                <div class="nav-icon-box" style="background: #f1f1f1;">
                    <i class="bi bi-person-fill text-dark"></i>
                </div>
                <div class="fw-800 text-dark">Account Settings</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
        </a>
        <a href="<?= base_url('/logout') ?>" class="nav-item-drops border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="nav-icon-box bg-danger-subtle">
                    <i class="bi bi-power text-danger"></i>
                </div>
                <div class="fw-800 text-danger">Logout</div>
            </div>
        </a>
    </div>

    <!-- Banner Info -->
    <div class="promo-banner-drops p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-900 text-white mb-1">PRO Membership</h5>
                <p class="text-white opacity-75 small mb-0 fw-600">Upgrade for free shipping</p>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-800 small py-2">Upgrade</button>
        </div>
    </div>
</div>

<style>
.profile-avatar-drops { width: 100px; height: 100px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
.profile-avatar-drops img { width: 100%; height: 100%; object-fit: cover; }

.stats-pill-drops { background: #f8f8f8; border-radius: 28px; padding: 20px; display: flex; align-items: center; justify-content: space-around; border: 1.5px solid #f1f1f1; }
.stat-item { text-align: center; }
.stat-divider { width: 1.5px; height: 30px; background: #eee; }
.extra-small { font-size: 10px; }
.uppercase { text-transform: uppercase; letter-spacing: 0.5px; }

.nav-item-drops { display: flex; align-items: center; justify-content: space-between; padding: 20px 0; border-bottom: 1.5px solid #f8f8f8; text-decoration: none; transition: 0.3s; }
.nav-item-drops:hover { transform: translateX(5px); }
.nav-icon-box { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

.promo-banner-drops { background: #000; border-radius: 32px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
.promo-banner-drops::after { content: ''; position: absolute; right: -20px; top: -20px; width: 100px; height: 100px; background: var(--primary); opacity: 0.3; filter: blur(40px); border-radius: 50%; }
</style>
