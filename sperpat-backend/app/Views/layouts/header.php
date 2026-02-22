<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="ESPERPAT - 2026 Drops Aesthetic Marketplace">
    <title><?= esc($title ?? 'ESPERPAT') ?></title>
    
    <!-- Ultra Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom 2026 UI CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
<?php 
$uri = service('uri');
$hideMainHeader = false;
$mainClass = 'container pt-2 pb-5';
$segments = $uri->getSegments();
if (!empty($segments)) {
    $first = $segments[0];
    // Hide on detail pages and specific sub-pages that have their own custom headers
    if ($first == 'produk' && count($segments) > 1) $hideMainHeader = true;
    if ($first == 'kategori') $hideMainHeader = true;
    if ($first == 'search') $hideMainHeader = true;
    if (in_array($first, ['admin', 'superuser'])) {
        $hideMainHeader = true;
        $mainClass = 'container-fluid pt-2 pb-5';
    }
    if ($first == 'customer') {
        if (isset($segments[1]) && in_array($segments[1], ['cart', 'checkout', 'orders', 'profile', 'dashboard'])) {
             // Only hide if it's not the main dashboard landing perhaps? 
             // Actually, the user wants it gone when entering "detail" and similar.
             $hideMainHeader = true;
        }
    }
}
?>

    <?php if (!$hideMainHeader): ?>
    <!-- Drops Inspired Header Container -->
    <div class="header-outer">
        <div class="container pt-3 pb-1">
            <!-- Top Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
                <div class="brand-logo-drops">
                    <span>D</span><span>R</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/12105/12105051.png" alt="O" style="height: 24px; margin-top: -4px;">
                    <span>P</span><span>S</span>
                </div>
                <a href="<?= base_url('/customer/cart') ?>" class="btn-icon-circle position-relative bg-white shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/3144/3144456.png" alt="Cart" style="height: 20px;">
                    <?php if (($cartCount ?? 0) > 0): ?>
                        <span class="badge-drops"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Search & Delivery Container -->
            <div class="search-delivery-card">
                <form action="<?= base_url('/search') ?>" method="get" class="search-group-drops">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" name="q" placeholder="What are you looking for?" value="<?= esc($keyword ?? '') ?>">
                </form>
                <div class="delivery-selector">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-success"></i>
                        <span class="text-muted small">Ship to <b class="text-dark">Jl. Malioboro, Blok Z, no 18</b></span>
                    </div>
                    <i class="bi bi-chevron-right small text-muted"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <main class="<?= $mainClass ?>">

<!-- Global System Menu (Ultra Bottom Sheet) -->
<div class="ultra-sheet-overlay" id="systemMenuOverlay" onclick="toggleSystemMenu(false)"></div>
<div class="ultra-bottom-sheet" id="systemMenuPanel">
    <div class="sheet-handle"></div>
    <div class="px-4 pb-4 pt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-900 mb-0">System Control</h4>
            <button class="btn-close-circle" onclick="toggleSystemMenu(false)"><i class="bi bi-x fs-5"></i></button>
        </div>
        
        <div class="d-grid gap-3 mb-4">
            <a href="<?= base_url('/customer/profile') ?>" class="menu-option-item">
                <div class="icon-box"><i class="bi bi-person-fill"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-800">My Profile</div>
                    <div class="text-muted extra-small fw-600">Personal information & data</div>
                </div>
                <i class="bi bi-chevron-right small opacity-50"></i>
            </a>
            <a href="<?= base_url('/customer/orders') ?>" class="menu-option-item">
                <div class="icon-box"><i class="bi bi-bag-check-fill"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-800">My Orders</div>
                    <div class="text-muted extra-small fw-600">Track shipments & history</div>
                </div>
                <i class="bi bi-chevron-right small opacity-50"></i>
            </a>
            <a href="<?= base_url('/customer/profile') ?>" class="menu-option-item">
                <div class="icon-box"><i class="bi bi-gear-fill"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-800">Settings</div>
                    <div class="text-muted extra-small fw-600">App preferences & security</div>
                </div>
                <i class="bi bi-chevron-right small opacity-50"></i>
            </a>
            <a href="<?= base_url('/logout') ?>" class="menu-option-item text-danger border-danger-subtle">
                <div class="icon-box bg-danger-subtle text-danger"><i class="bi bi-power"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-800">Logout</div>
                    <div class="text-muted extra-small fw-600">End your current session</div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
function toggleSystemMenu(show = true) {
    const panel = document.getElementById('systemMenuPanel');
    const overlay = document.getElementById('systemMenuOverlay');
    if(show) {
        panel.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    } else {
        panel.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
}
</script>
