<?php
// Checkout - DROPS 2026 Aesthetic Refined
?>

<div class="container py-4 mb-5">
    <!-- Custom Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2 px-1">
        <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0 text-dark">Checkout</h6>
        <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-floating rounded-pill px-4" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('/customer/checkout/process') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="alamat" value="<?= !empty($user['address']) ? esc($user['address']) : 'Jl. Malioboro, Blok Z, no 18, Tegalsari, Surabaya, Jawa Timur, 60262' ?>">

        <!-- Shipping Section -->
        <div class="section-title-drops mb-3">Shipping Address</div>
        <div class="address-card-drops mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-800 text-dark"><?= esc($user['name'] ?? 'Rex Hypebeasts') ?></div>
                <span class="badge bg-primary-light text-primary rounded-pill px-3 py-2 small fw-700">Main</span>
            </div>
            <div class="text-muted small mb-3 line-height-1-5">
                <?= !empty($user['address']) ? esc($user['address']) : 'Jl. Malioboro, Blok Z, no 18, Tegalsari, Surabaya, Jawa Timur, 60262' ?>
            </div>
            <button type="button" class="btn-change-address">Change Address</button>
        </div>

        <!-- Order Items Summary (Compact) -->
        <div class="section-title-drops mb-3">Order Summary</div>
        <?php foreach ($cart as $id => $item): ?>
            <div class="checkout-item-compact mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="item-mini-img">
                        <?php 
                        $demoImages = [
                            'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&q=80',
                            'https://images.unsplash.com/photo-1600705722908-bab1e61c0b4d?auto=format&fit=crop&w=400&q=80',
                            'https://images.unsplash.com/photo-1486006920555-c77dcf18193b?auto=format&fit=crop&w=400&q=80'
                        ];
                        $imgFallback = $demoImages[$id % count($demoImages)];
                        $imgUrl = (!empty($item['image']) && strpos($item['image'], 'http') === 0) ? $item['image'] : $imgFallback;
                        ?>
                        <img src="<?= $imgUrl ?>" alt="Item">
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-700 text-dark small mb-0"><?= esc($item['name']) ?></div>
                        <div class="text-muted extra-small">Qty: <?= $item['qty'] ?></div>
                    </div>
                    <div class="fw-800 text-dark">$<?= number_format($item['harga'] * $item['qty'], 2) ?></div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Detailed Calculation -->
        <div class="bg-f8 p-4 rounded-24 mt-4">
            <div class="d-flex justify-content-between mb-2">
                <div class="text-muted small">Subtotal</div>
                <div class="fw-700 small text-dark">$<?= number_format($totalPrice, 2) ?></div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div class="text-muted small">Shipping Fee</div>
                <div class="fw-700 small text-dark">$15.00</div>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <div class="text-muted small">Platform Fee</div>
                <div class="fw-700 small text-dark">$2.00</div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="section-title-drops mt-4 mb-3">Payment Method</div>
        <div class="payment-grid mb-5">
            <label class="payment-option">
                <input type="radio" name="metode_pembayaran" value="transfer" checked>
                <div class="payment-box">
                    <i class="bi bi-bank fs-4 mb-1"></i>
                    <span>Transfer</span>
                </div>
            </label>
            <label class="payment-option">
                <input type="radio" name="metode_pembayaran" value="qris">
                <div class="payment-box">
                    <i class="bi bi-qr-code-scan fs-4 mb-1"></i>
                    <span>QRIS</span>
                </div>
            </label>
            <label class="payment-option">
                <input type="radio" name="metode_pembayaran" value="cod">
                <div class="payment-box">
                    <i class="bi bi-cash-stack fs-4 mb-1"></i>
                    <span>COD</span>
                </div>
            </label>
        </div>

        <!-- Sticky Bottom Final Summary -->
        <div class="checkout-footer-drops px-4 pt-4 pb-5">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted fw-800 uppercase mb-1" style="font-size: 10px; letter-spacing: 0.8px;">TOTAL PAYMENT</div>
                    <div class="fw-900 fs-3 text-dark">$<?= number_format($totalPrice + 17, 2) ?></div>
                </div>
                <button type="submit" class="btn btn-confirm-drops px-4" onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>'; this.classList.add('disabled');">
                    Confirm Order
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.section-title-drops { font-weight: 800; font-size: 1.1rem; color: #1a1a1a; }
.bg-f8 { background: #f8f8f8; }
.rounded-24 { border-radius: 24px; }
.address-card-drops { background: #f8f8f8; border-radius: 24px; padding: 20px; border: 1.5px solid #f1f1f1; }
.btn-change-address { background: #fff; border: 1.5px solid #f1f1f1; border-radius: 100px; padding: 6px 16px; font-size: 12px; font-weight: 700; color: #000; transition: 0.3s; }
.btn-change-address:hover { background: #f1f1f1; }

.checkout-item-compact { background: #fff; border-bottom: 1.5px dashed #f1f1f1; padding-bottom: 15px; }
.item-mini-img { width: 44px; height: 44px; background: #f1f1f1; border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 5px; }
.item-mini-img img { width: 100%; height: 100%; object-fit: contain; }

.extra-small { font-size: 11px; }

.payment-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.payment-option input { display: none; }
.payment-box { 
    background: #fff; border: 1.5px solid #f1f1f1; border-radius: 20px; padding: 15px; 
    text-align: center; cursor: pointer; transition: 0.3s; 
    display: flex; flex-direction: column; align-items: center;
}
.payment-box span { font-size: 11px; font-weight: 700; color: #8e8e93; }
.payment-option input:checked + .payment-box { border-color: var(--primary); background: var(--primary-light); }
.payment-option input:checked + .payment-box i, 
.payment-option input:checked + .payment-box span { color: var(--primary); }

.checkout-footer-drops {
    position: fixed; bottom: 0; left: 0; width: 100%; background: #fff;
    border-top-left-radius: 40px; border-top-right-radius: 40px;
    box-shadow: 0 -20px 40px rgba(0,0,0,0.08); z-index: 1000;
}
.btn-confirm-drops {
    background: #00ab4f; color: #fff; border: none; border-radius: 100px;
    font-weight: 800; font-size: 0.95rem; transition: 0.4s;
    height: 52px; padding: 0 35px;
}
.btn-confirm-drops:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 171, 79, 0.2); }

.uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>
