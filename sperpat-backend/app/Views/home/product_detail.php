<?php
// Product Detail - DROPS 2026 Aesthetic
?>

<!-- Custom Header for Detail -->
<div class="d-flex justify-content-between align-items-center mb-4 pt-2">
    <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
    <h6 class="fw-800 mb-0">Component Detail</h6>
    <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
</div>

<!-- Product Stage -->
<div class="product-stage-drops mb-4">
    <div class="stage-inner p-5 position-relative">
        <?php 
            $demoImages = [
                'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&q=80',
                'https://images.unsplash.com/photo-1600705722908-bab1e61c0b4d?auto=format&fit=crop&w=400&q=80',
                'https://images.unsplash.com/photo-1486006920555-c77dcf18193b?auto=format&fit=crop&w=400&q=80'
            ];
            $imgUrl = (!empty($product['image']) && strpos($product['image'], 'http') === 0) 
                      ? $product['image'] 
                      : ( (!empty($product['image']) && file_exists(FCPATH . $product['image'])) 
                          ? base_url($product['image']) 
                          : $demoImages[$product['id'] % count($demoImages)] );
        ?>
        <img src="<?= $imgUrl ?>" alt="<?= esc($product['name']) ?>" class="img-fluid floating-main-img">
        <div class="stage-orbit"></div>
        <button class="btn-orbit-view"><i class="bi bi-box"></i></button>
    </div>
</div>

<!-- Thumbnail Selector -->
<div class="row g-2 mb-4 px-2">
    <div class="col-3">
        <div class="thumb-pill-drops active">
            <img src="<?= $imgUrl ?>" class="img-fluid">
        </div>
    </div>
    <div class="col-3">
        <div class="thumb-pill-drops">
            <img src="<?= $imgUrl ?>" class="img-fluid opacity-50" style="transform: rotate(15deg) scale(0.8);">
        </div>
    </div>
    <div class="col-3">
        <div class="thumb-pill-drops">
            <img src="<?= $imgUrl ?>" class="img-fluid opacity-50" style="transform: scaleX(-1) rotate(-5deg);">
        </div>
    </div>
    <div class="col-3">
        <div class="thumb-pill-drops">
            <i class="bi bi-play-circle fs-4 text-muted"></i>
        </div>
    </div>
</div>

<!-- Product Info -->
<div class="px-2 mb-5">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <h4 class="fw-900 mb-0 text-dark" style="max-width: 80%;"><?= esc($product['name']) ?></h4>
        <button class="btn-heart-lg"><i class="bi bi-heart"></i></button>
    </div>
    <div class="display-6 fw-900 text-dark mb-4">Rp<?= number_format($product['harga_jual'], 0, ',', '.') ?></div>

    <div class="d-flex gap-3 mb-5 overflow-auto hide-scrollbar">
        <div class="info-chip-drops">Stock <b><?= $product['stok'] ?></b> Available</div>
    </div>

    <!-- Size Selection Mockup Hidden -->
</div>

<!-- Sticky Bottom Action Bar -->
<div class="bottom-action-bar-drops p-3">
    <div class="d-flex gap-3 align-items-center">
        <button class="btn-chat-drops"><i class="bi bi-chat-dots"></i></button>
        <form action="<?= base_url('/customer/cart/add') ?>" method="post" class="flex-grow-1">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="qty" value="1">
            <button type="submit" class="btn btn-outline-drops w-100">Add to Cart</button>
        </form>
        <a href="<?= base_url('/customer/beli/' . $product['id']) ?>" class="btn btn-primary-drops flex-grow-1">Buy Now</a>
    </div>
</div>

<style>
.product-stage-drops { background: #f8f8f8; border-radius: 40px; text-align: center; overflow: hidden; }
.stage-inner { min-height: 300px; display: flex; align-items: center; justify-content: center; }
.floating-main-img { 
    max-height: 220px; z-index: 2; transform: rotate(-15deg); 
    filter: drop-shadow(0 20px 40px rgba(0,0,0,0.15));
}
.stage-orbit {
    position: absolute; bottom: 60px; left: 50%; transform: translateX(-50%);
    width: 200px; height: 30px; border: 1.5px solid #eee; border-radius: 50%;
}
.btn-orbit-view {
    position: absolute; bottom: 45px; left: 50%; transform: translateX(-50%);
    background: #000; color: #fff; width: 34px; height: 26px; border: none;
    border-radius: 100px; display: flex; align-items: center; justify-content: center;
}

.thumb-pill-drops {
    background: #fff; border-radius: 20px; padding: 10px; border: 1.5px solid #f1f1f1;
    height: 60px; display: flex; align-items: center; justify-content: center;
}
.thumb-pill-drops.active { border-color: var(--primary); background: var(--primary-light); }
.thumb-pill-drops img { height: 100%; object-fit: contain; }

.btn-heart-lg { background: none; border: none; font-size: 1.5rem; color: #8e8e93; }

.info-chip-drops {
    background: #f8f8f8; padding: 10px 16px; border-radius: 100px; 
    font-size: 13px; color: #8e8e93; white-space: nowrap;
}
.info-chip-drops b { color: #000; }

.size-chip-drops {
    padding: 10px 20px; border-radius: 12px; background: #fff; border: 1.5px solid #f1f1f1;
    font-weight: 700; font-size: 13px; cursor: pointer; transition: var(--transition);
}
.size-chip-drops.active { background: var(--primary); color: #fff; border-color: var(--primary); }

.bottom-action-bar-drops {
    position: fixed; bottom: 0; left: 0; width: 100%; background: #fff;
    border-top-left-radius: 30px; border-top-right-radius: 30px; 
    box-shadow: 0 -10px 30px rgba(0,0,0,0.05); z-index: 1000;
}
.btn-chat-drops {
    width: 48px; height: 48px; border-radius: 14px; border: 1.5px solid #eee;
    background: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.btn-outline-drops {
    border: 1.5px solid var(--primary); color: var(--primary); background: #fff;
    font-weight: 800; border-radius: 100px; padding: 12px; font-size: 14px;
}
.btn-primary-drops {
    background: var(--primary); color: #fff; border: none; text-decoration: none;
    font-weight: 800; border-radius: 100px; padding: 12px; font-size: 14px;
    text-align: center;
}
</style>
