<?php
// Cart - DROPS 2026 Aesthetic Refined
?>

<div class="container py-4">
    <!-- Custom Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
        <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">Cart</h6>
        <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
    </div>

    <!-- Ship To Ribbon -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar-circle">
                <img src="https://i.pravatar.cc/150?u=rex" alt="User">
            </div>
            <div class="small fw-600 text-muted">Ship to <b class="text-dark">Rex Hypebeasts</b></div>
        </div>
        <div class="d-flex align-items-center gap-1">
            <span class="text-success small fw-700">Tegalsari, Surabaya</span>
            <i class="bi bi-chevron-down small text-dark"></i>
        </div>
    </div>

    <!-- Cart Items -->
    <div class="mb-5">
        <?php if(!empty($cart)): ?>
            <?php 
            $demoImages = [
                'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&q=80',
                'https://images.unsplash.com/photo-1600705722908-bab1e61c0b4d?auto=format&fit=crop&w=400&q=80',
                'https://images.unsplash.com/photo-1486006920555-c77dcf18193b?auto=format&fit=crop&w=400&q=80',
                'https://images.unsplash.com/photo-1591438676302-588aef007323?auto=format&fit=crop&w=400&q=80'
            ];
            foreach ($cart as $id => $item): 
                $imgUrl = (!empty($item['image']) && strpos($item['image'], 'http') === 0) 
                          ? $item['image'] 
                          : ( (!empty($item['image']) && file_exists(FCPATH . $item['image'])) 
                              ? base_url($item['image']) 
                              : $demoImages[$id % count($demoImages)] );
            ?>
                <div class="cart-item-refined mb-4">
                    <div class="d-flex gap-3 position-relative">
                        <div class="cart-img-pill">
                            <img src="<?= $imgUrl ?>" alt="<?= esc($item['name']) ?>">
                        </div>
                        <div class="flex-grow-1 d-flex flex-column justify-content-between py-1">
                            <div>
                                <h6 class="fw-600 text-muted small mb-1 line-clamp-2" style="max-width: 180px;"><?= esc($item['name']) ?></h6>
                                <div class="fw-800 fs-5">$<?= number_format($item['harga'], 0, ',', '.') ?></div>
                            </div>
                            
                            <!-- Quantity Stepper Pill -->
                            <div class="qty-pill-drops">
                                <button class="qty-btn"><i class="bi bi-dash"></i></button>
                                <span class="qty-val"><?= $item['qty'] ?></span>
                                <button class="qty-btn active"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Coupon Box -->
            <div class="mb-5 pt-3">
                <div class="text-muted small mb-3 fw-600">Have a coupon code?</div>
                <div class="coupon-pill d-flex justify-content-between align-items-center">
                    <span class="fw-800 text-dark">DROPSYEAREND</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-success small fw-700">Available</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                </div>
            </div>

            <!-- Price Summary -->
            <div class="summary-refined mb-4">
                <div class="summary-row">
                    <span>Sub Total</span>
                    <b class="text-dark">$<?= number_format($totalPrice, 2) ?></b>
                </div>
                <div class="summary-row">
                    <span>Delivery Fee</span>
                    <b class="text-dark">$15.00</b>
                </div>
                <div class="summary-row">
                    <span>Discount</span>
                    <b class="text-success">-$200.00</b>
                </div>
                <div class="divider-dashed my-3"></div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <b class="text-dark fs-4">$<?= number_format($totalPrice - 185, 2) ?></b>
                </div>
            </div>

            <button class="btn btn-checkout-drops w-100 py-3">
                Checkout
            </button>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-light mb-4"></i>
                <h4 class="fw-900">Your cart is empty</h4>
                <a href="<?= base_url('/produk') ?>" class="btn btn-primary rounded-pill px-5 mt-4">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.avatar-circle { width: 32px; height: 32px; border-radius: 50%; overflow: hidden; }
.avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

.cart-img-pill { 
    width: 100px; height: 110px; background: #f1f1f1; border-radius: 24px;
    display: flex; align-items: center; justify-content: center; padding: 15px;
}
.cart-img-pill img { width: 100%; height: 100%; object-fit: contain; }

.qty-pill-drops {
    position: absolute; bottom: 0; right: 0;
    background: #f1f1f1; border-radius: 100px; padding: 4px;
    display: flex; align-items: center; gap: 15px;
}
.qty-btn {
    width: 28px; height: 28px; border-radius: 50%; border: none;
    background: #fff; color: #000; display: flex; align-items: center; justify-content: center;
    font-size: 1rem; transition: 0.3s;
}
.qty-btn.active { background: var(--primary); color: #fff; }
.qty-val { font-weight: 800; font-size: 0.9rem; min-width: 10px; text-align: center; }

.coupon-pill {
    background: #fff; border: 1.5px solid #f1f1f1; border-radius: 20px; padding: 16px 20px;
}

.summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-weight: 600; color: #8e8e93; }
.divider-dashed { border-top: 1.5px dashed #f1f1f1; }
.total-row { font-weight: 800; color: #000; margin-top: 20px; }

.btn-checkout-drops {
    background: var(--primary); color: #fff; border: none; border-radius: 24px;
    font-weight: 800; font-size: 1.1rem; transition: 0.4s;
}
.btn-checkout-drops:hover { transform: translateY(-3px); box-shadow: 0 10px 30px var(--primary-glow); }

.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
