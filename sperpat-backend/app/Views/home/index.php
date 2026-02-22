<?php
// Home Index - DROPS 2026 Aesthetic (Motorcycle Parts Edition)
?>

<!-- Brand Horizontal List - Japanese Motor Parts Brands -->
<div class="row g-0 mb-4 overflow-auto flex-nowrap hide-scrollbar py-2">
    <div class="d-flex gap-3 px-3">
        <?php 
        $brands = [
            ['name' => 'Yoshimura', 'img' => 'https://seeklogo.com/images/Y/yoshimura-logo-975B080C0B-seeklogo.com.png'],
            ['name' => 'Kitaco', 'img' => 'https://seeklogo.com/images/K/kitaco-logo-661C526A4A-seeklogo.com.png'],
            ['name' => 'Daytona', 'img' => 'https://seeklogo.com/images/D/daytona-logo-A91F268158-seeklogo.com.png'],
            ['name' => 'NGK', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/07/NGK_Logo.svg/512px-NGK_Logo.svg.png'],
            ['name' => 'KYB', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/KYB_Logo.svg/512px-KYB_Logo.svg.png'],
            ['name' => 'Takegawa', 'img' => 'https://seeklogo.com/images/S/special-parts-takegawa-logo-60A0C8A79E-seeklogo.com.png']
        ];
        foreach ($brands as $brand): ?>
            <div class="brand-pill-drops">
                <img src="<?= $brand['img'] ?>" alt="<?= $brand['name'] ?>" style="object-fit: contain; max-height: 25px;">
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Hero Sale Banner - Motorcycle Theme -->
<div class="container mb-5 px-3">
    <div class="hero-sale-drops" style="background: linear-gradient(135deg, #0a0a0b 0%, #1a1a1c 100%);">
        <div class="z-index-1">
            <h6 class="text-white fw-400 mb-1 opacity-75">Elite Performance</h6>
            <h2 class="text-white fw-800 mb-2">Original Parts</h2>
            <a href="<?= base_url('/produk') ?>" class="btn-shop-now">Shop All Parts</a>
        </div>
        <!-- High Fidelity Exhaust Overlay -->
        <img src="https://images.unsplash.com/photo-1635073910831-26ec01d02bd7?auto=format&fit=crop&w=400&q=80" 
             style="position:absolute; right:-20px; top:-5px; height:110%; transform:rotate(-10deg); filter:drop-shadow(0 20px 50px rgba(0,0,0,0.8));" alt="Parts">
    </div>
</div>

<!-- New Arrival Section -->
<div class="container mb-5 pb-5 px-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-800 mb-0">New Arrival</h4>
        <a href="<?= base_url('/produk') ?>" class="text-success fw-700 small text-decoration-none">see all</a>
    </div>

    <div class="row g-3">
        <?php 
        $demoImages = [
            'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&q=80', // Piston
            'https://images.unsplash.com/photo-1600705722908-bab1e61c0b4d?auto=format&fit=crop&w=400&q=80', // Shock
            'https://images.unsplash.com/photo-1486006920555-c77dcf18193b?auto=format&fit=crop&w=400&q=80', // Brake
            'https://images.unsplash.com/photo-1591438676302-588aef007323?auto=format&fit=crop&w=400&q=80'  // Engine
        ];
        foreach ($featured ?? [] as $index => $product): 
            $imgUrl = (!empty($product['image']) && strpos($product['image'], 'http') === 0) 
                      ? $product['image'] 
                      : ( (!empty($product['image']) && file_exists(FCPATH . $product['image'])) 
                          ? base_url($product['image']) 
                          : $demoImages[$index % count($demoImages)] );
        ?>
            <div class="col-6">
                <div class="product-card-drops" onclick="window.location.href='<?= base_url('/produk/' . ($product['slug'] ?? $product['id'])) ?>'">
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
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
