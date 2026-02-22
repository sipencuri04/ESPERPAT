<?php
// Orders List - DROPS 2026 Aesthetic Refined
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
        <button class="btn-icon-circle" onclick="history.back()"><i class="bi bi-arrow-left"></i></button>
        <h6 class="fw-800 mb-0">My Orders</h6>
        <button class="btn-icon-circle" onclick="toggleSystemMenu(true)"><i class="bi bi-list"></i></button>
    </div>

    <!-- Status Tabs -->
    <div class="d-flex gap-2 overflow-auto hide-scrollbar mb-4 px-1">
        <div class="tab-pill-drops active">All</div>
        <div class="tab-pill-drops">Unpaid</div>
        <div class="tab-pill-drops">Process</div>
        <div class="tab-pill-drops">Shipped</div>
        <div class="tab-pill-drops">Done</div>
    </div>

    <?php if (!empty($orders)): ?>
        <div class="order-list-drops">
            <?php foreach ($orders as $order): ?>
                <div class="order-card-refined mb-4" onclick="window.location.href='<?= base_url('/customer/orders/' . $order['id']) ?>'">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam text-primary fs-5"></i>
                            <div>
                                <div class="fw-800 text-dark small">Order #<?= esc($order['invoice_number']) ?></div>
                                <div class="text-muted extra-small"><?= date('d M Y', strtotime($order['created_at'])) ?></div>
                            </div>
                        </div>
                        <?php 
                            $statusLabel = strtoupper($order['status']);
                            $statusColor = 'var(--primary)';
                            if($order['status'] == 'pending') $statusColor = '#ff8f00';
                        ?>
                        <div class="status-token" style="background: <?= $statusColor ?>15; color: <?= $statusColor ?>;">
                            <?= $statusLabel ?>
                        </div>
                    </div>
                    
                    <div class="divider-dashed mb-3"></div>
                    
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="text-muted extra-small fw-700 uppercase">Total Amount</div>
                        <div class="fw-900 fs-5 text-dark">Rp<?= number_format($order['total'], 0, ',', '.') ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 mt-5">
            <div class="icon-circle-lg mx-auto mb-4">
                <i class="bi bi-receipt text-muted"></i>
            </div>
            <h5 class="fw-900 text-dark">No orders yet</h5>
            <p class="text-muted small px-5">Your order history will appear here once you've made a purchase.</p>
            <a href="<?= base_url('/produk') ?>" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-800">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<style>
.tab-pill-drops {
    background: #f8f8f8; border: 1.5px solid #f1f1f1; border-radius: 100px;
    padding: 8px 20px; font-size: 13px; font-weight: 700; color: #8e8e93;
    white-space: nowrap; cursor: pointer; transition: 0.3s;
}
.tab-pill-drops.active { background: #000; color: #fff; border-color: #000; }

.order-card-refined { background: #fff; border: 1.5px solid #f1f1f1; border-radius: 24px; padding: 20px; transition: 0.3s; cursor: pointer; }
.order-card-refined:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

.status-token { font-size: 10px; font-weight: 800; padding: 4px 12px; border-radius: 100px; letter-spacing: 0.5px; }
.icon-circle-lg { width: 80px; height: 80px; background: #f8f8f8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
</style>
