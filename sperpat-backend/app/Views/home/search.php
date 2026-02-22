<?php
// Search Hub - DROPS 2026 Aesthetic
?>

<div class="container py-4">
    <!-- Header with Back & Info -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2 px-1">
        <button class="btn-icon-circle" onclick="<?= (uri_string() == 'search' && !isset($_GET['q'])) ? "window.location.href='".base_url('/')."'" : "history.back()" ?>"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">Search Hub</h6>
        <div style="width: 44px;"></div> <!-- Spacer -->
    </div>

    <!-- Stats -->
    <div class="mb-4 px-2">
        <h4 class="fw-900 text-dark mb-1">"<?= esc($keyword ?? 'Everything') ?>"</h4>
        <div class="text-muted small fw-700"><?= count($products) ?> assets identified</div>
    </div>

    <!-- Product Grid -->
    <div class="row g-4 mb-5">
        <?php 
        $demoImages = [
            'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1600705722908-bab1e61c0b4d?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1486006920555-c77dcf18193b?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1591438676302-588aef007323?auto=format&fit=crop&w=400&q=80'
        ];
        if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): 
                $imgUrl = (!empty($product['image']) && strpos($product['image'], 'http') === 0) 
                          ? $product['image'] 
                          : ( (!empty($product['image']) && file_exists(FCPATH . $product['image'])) 
                              ? base_url($product['image']) 
                              : $demoImages[$index % count($demoImages)] );
            ?>
                <div class="col-6">
                    <div class="product-card-drops" onclick="window.location.href='<?= base_url('/produk/' . ($product['slug'] ?? '')) ?>'">
                        <div class="card-img-wrap">
                            <button class="wishlist-btn" onclick="event.stopPropagation(); this.classList.toggle('active')">
                                <i class="bi bi-heart"></i>
                            </button>
                            <img src="<?= $imgUrl ?>" alt="<?= esc($product['name']) ?>">
                        </div>
                        <div class="card-info">
                            <div class="card-title-drops"><?= esc($product['name']) ?></div>
                            <div class="card-price-drops">Rp<?= number_format($product['harga_jual'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 mt-5">
                <i class="bi bi-search display-1 text-light mb-4"></i>
                <h4 class="fw-900">No assets found</h4>
                <p class="text-muted px-4">We couldn't find any results for "<?= esc($keyword) ?>". Try adjusting your parameters.</p>
                <a href="<?= base_url('/produk') ?>" class="btn btn-primary px-5 rounded-pill mt-4">Browse All</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.x-small { font-size: 11px; }
.uppercase { text-transform: uppercase; }
</style>
