<?php
// Order Detail - DROPS 2026 Aesthetic Refined
?>

<div class="container py-4 mb-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
        <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">Order Tracking</h6>
        <button class="btn-icon-circle"><i class="bi bi-share"></i></button>
    </div>

    <!-- Tracking Header Card -->
    <div class="tracking-summary-drops mb-4">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <div class="text-white opacity-75 extra-small fw-700 uppercase mb-1">Estimated Delivery</div>
                <h4 class="fw-900 text-white mb-0">24 Feb 2026</h4>
            </div>
            <div class="status-token bg-white text-dark fw-800">
                <?= strtoupper($order['status']) ?>
            </div>
        </div>
        <div class="divider-dashed opacity-25 mb-3"></div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-truck text-white fs-5"></i>
            <span class="text-white small fw-600">Ship via <b class="opacity-100">ESPER-Logistics</b></span>
            <?php if(!empty($order['nomor_resi'])): ?>
                <span class="badge bg-white text-dark rounded-pill ms-auto px-3"><?= $order['nomor_resi'] ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="section-title-drops mb-3 px-1">Tracking History</div>
    <div class="tracking-timeline-refined mb-5">
        <div class="time-step active">
            <div class="step-indicator">
                <div class="dot"></div>
                <div class="line"></div>
            </div>
            <div class="step-content">
                <div class="fw-800 text-dark small">Order Placed</div>
                <div class="text-muted extra-small">We have received your order request.</div>
                <div class="text-muted extra-small mt-1"><?= date('d M, H:i', strtotime($order['created_at'])) ?></div>
            </div>
        </div>
        <div class="time-step <?= in_array($order['status'], ['paid', 'processing', 'shipped', 'completed']) ? 'active' : '' ?>">
            <div class="step-indicator">
                <div class="dot"></div>
                <div class="line"></div>
            </div>
            <div class="step-content">
                <div class="fw-800 text-dark small">Payment Confirmed</div>
                <div class="text-muted extra-small">Transaction validated via <?= ucfirst($order['metode_pembayaran']) ?>.</div>
            </div>
        </div>
        <div class="time-step <?= in_array($order['status'], ['shipped', 'completed']) ? 'active' : '' ?>">
            <div class="step-indicator">
                <div class="dot"></div>
                <div class="line"></div>
            </div>
            <div class="step-content">
                <div class="fw-800 text-dark small">In Transit</div>
                <div class="text-muted extra-small">Package has been scanned at the distribution center.</div>
            </div>
        </div>
        <div class="time-step <?= ($order['status'] == 'completed') ? 'active' : '' ?>">
            <div class="step-indicator">
                <div class="dot"></div>
            </div>
            <div class="step-content">
                <div class="fw-800 text-dark small">Delivered</div>
                <div class="text-muted extra-small">Package safely arrived at destination.</div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="section-title-drops mb-3 px-1">Package Contents</div>
    <div class="package-card-drops mb-5">
        <?php foreach ($order['items'] as $item): ?>
            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom-light">
                <div class="mini-package-img">
                    <i class="bi bi-box-seam text-primary fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-800 text-dark small mb-0"><?= esc($item['product_name'] ?? 'Product #' . $item['product_id']) ?></div>
                    <div class="text-muted extra-small"><?= $item['qty'] ?> x Rp<?= number_format($item['harga'], 0, ',', '.') ?></div>
                </div>
                <div class="fw-900 text-dark">Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></div>
            </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between pt-2">
            <span class="fw-700 text-muted small">Total Valuations</span>
            <span class="fw-900 text-dark fs-5">Rp<?= number_format($order['total'], 0, ',', '.') ?></span>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="d-flex gap-3 mb-5 mt-2">
        <a href="<?= base_url('/customer/orders') ?>" class="btn btn-outline-drops flex-grow-1">Back to List</a>
        <button class="btn btn-primary-drops flex-grow-1">Help Center</button>
    </div>
</div>

<style>
.tracking-summary-drops { background: #000; border-radius: 32px; padding: 24px; position: relative; overflow: hidden; }
.tracking-summary-drops::after { content: ''; position: absolute; right: -20px; top: -20px; width: 100px; height: 100px; background: var(--primary); opacity: 0.3; filter: blur(40px); border-radius: 50%; }

.tracking-timeline-refined { padding-left: 10px; }
.time-step { display: flex; gap: 20px; padding-bottom: 30px; }
.step-indicator { display: flex; flex-direction: column; align-items: center; }
.step-indicator .dot { width: 14px; height: 14px; background: #eee; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #eee; z-index: 2; transition: 0.4s; }
.step-indicator .line { width: 2px; height: 100%; background: #eee; margin-top: 4px; z-index: 1; transition: 0.4s; }
.time-step.active .dot { background: var(--primary); box-shadow: 0 0 0 2px var(--primary); }
.time-step.active .line { background: var(--primary); }
.time-step.active .fw-800 { color: #000 !important; }

.package-card-drops { background: #fff; border: 1.5px solid #f1f1f1; border-radius: 28px; padding: 24px; }
.mini-package-img { width: 44px; height: 44px; background: #f8f8f8; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.border-bottom-light { border-bottom: 1.5px solid #f8f8f8; }

.btn-outline-drops { border: 1.5px solid #eee; background: #fff; color: #000; font-weight: 800; border-radius: 100px; padding: 12px; font-size: 14px; }
</style>
