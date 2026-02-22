<?php
// Product Explorer - DROPS 2026 Aesthetic
?>

<div>
    <!-- Header with Back & Filter -->
    <div class="d-flex justify-content-between align-items-center mb-3 pt-0 px-1">
        <button class="btn-icon-circle" onclick="window.location.href='<?= base_url('/') ?>'"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">All Components</h6>
        <button class="btn-icon-circle" id="btnOpenFilter"><i class="bi bi-sliders2-vertical"></i></button>
    </div>

    <!-- Product Grid -->
    <div class="row g-4 mb-5">
        <?php 
        $demoImages = [
            'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1600705722908-bab1e61c0b4d?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1486006920555-c77dcf18193b?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1591438676302-588aef007323?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=400&q=80'
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
                <h4 class="fw-900">No components found</h4>
                <p class="text-muted">Currently stocking our inventory. Check back soon!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 2026 Bottom Sheet Filter Hub -->
<div class="ultra-sheet-overlay" id="filterOverlay"></div>
<div class="ultra-bottom-sheet" id="filterPanel">
    <div class="sheet-handle"></div>
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h4 class="fw-900 mb-0">Sort Config</h4>
            <button class="btn-close-circle" id="btnCloseFilter"><i class="bi bi-x"></i></button>
        </div>
        
        <form action="<?= base_url('/produk') ?>" method="get">
            <div class="mb-5">
                <div class="d-grid gap-3">
                    <button type="submit" name="sort" value="latest" class="filter-option-btn">Newest Items</button>
                    <button type="submit" name="sort" value="price_low" class="filter-option-btn">Lowest Price</button>
                    <button type="submit" name="sort" value="price_high" class="filter-option-btn">Highest Price</button>
                </div>
            </div>
            <button type="button" class="btn btn-primary w-100 py-3 rounded-full fw-900" onclick="document.getElementById('btnCloseFilter').click()">
                Close
            </button>
        </form>
    </div>
</div>

<style>
.filter-option-btn {
    background: #f8f8f8; border: 1.5px solid #f1f1f1; padding: 16px 24px;
    border-radius: 16px; font-weight: 800; text-align: left; transition: var(--transition);
}
.filter-option-btn:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }

.ultra-sheet-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.4); backdrop-filter: blur(4px);
    z-index: 2000; opacity: 0; pointer-events: none; transition: 0.4s;
}
.ultra-sheet-overlay.show { opacity: 1; pointer-events: all; }

.ultra-bottom-sheet {
    position: fixed; bottom: 0; left: 0; width: 100%; background: #fff;
    border-top-left-radius: 40px; border-top-right-radius: 40px;
    z-index: 2001; transform: translateY(100%); transition: 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.ultra-bottom-sheet.show { transform: translateY(0); }
.sheet-handle { width: 40px; height: 5px; background: #eee; border-radius: 100px; margin: 15px auto; }

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.x-small { font-size: 11px; }
.uppercase { text-transform: uppercase; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnOpen = document.getElementById('btnOpenFilter');
    const btnClose = document.getElementById('btnCloseFilter');
    const panel = document.getElementById('filterPanel');
    const overlay = document.getElementById('filterOverlay');

    if(btnOpen) btnOpen.addEventListener('click', () => {
        panel.classList.add('show');
        overlay.classList.add('show');
    });

    const closeFilter = () => {
        panel.classList.remove('show');
        overlay.classList.remove('show');
    };

    if(btnClose) btnClose.addEventListener('click', closeFilter);
    if(overlay) overlay.addEventListener('click', closeFilter);
});
</script>
