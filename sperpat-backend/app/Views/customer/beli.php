<!-- Modern 2025 Buy Confirmation Page -->
<div class="container py-4 pb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= base_url('/produk') ?>" class="text-decoration-none text-muted">Produk</a></li>
            <li class="breadcrumb-item active fw-600">Konfirmasi Pembelian</li>
        </ol>
    </nav>

    <form action="<?= base_url('/customer/beli/' . $product['id']) ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Product Information -->
                <div class="bg-white rounded-lg p-4 shadow-sm border mb-4">
                    <h5 class="fw-800 mb-4 h6 text-muted text-uppercase letter-spacing-1">Produk Pesanan</h5>
                    <div class="d-flex gap-4 align-items-center">
                        <div class="rounded-lg overflow-hidden border bg-light" style="width:120px; height:120px; flex-shrink:0;">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= base_url($product['image']) ?>" alt="<?= esc($product['name']) ?>" class="w-100 h-100" style="object-fit:cover;">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-image text-muted fs-2"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <span class="badge bg-light text-accent rounded-pill px-3 py-1 mb-2 small fw-600"><?= esc($product['category_name'] ?? 'Sparepart') ?></span>
                            <h5 class="fw-800 mb-2 text-dark"><?= esc($product['name']) ?></h5>
                            <div class="text-accent fw-800 fs-4 mb-1">Rp<?= number_format($product['harga_jual'], 0, ',', '.') ?></div>
                            <div class="small text-muted">Tersisa <?= $product['stok'] ?> unit</div>
                        </div>
                    </div>
                </div>

                <!-- Shipping -->
                <div class="bg-white rounded-lg p-4 shadow-sm border mb-4">
                    <h6 class="fw-700 mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt text-accent"></i> Alamat Pengiriman
                    </h6>
                    <textarea class="form-control border-0 bg-light rounded-md py-3 px-3" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap pengiriman..."><?= esc($user['address'] ?? '') ?></textarea>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-lg p-4 shadow-sm border">
                    <h6 class="fw-700 mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card text-accent"></i> Metode Pembayaran
                    </h6>
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="modern-payment-card w-100">
                                <input type="radio" name="metode_pembayaran" value="transfer" checked class="d-none">
                                <div class="payment-content text-center p-3 rounded-md bg-white border h-100 transition-all">
                                    <i class="bi bi-bank fs-3 d-block mb-1"></i>
                                    <span class="small fw-600 d-block">Transfer</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-4">
                            <label class="modern-payment-card w-100">
                                <input type="radio" name="metode_pembayaran" value="qris" class="d-none">
                                <div class="payment-content text-center p-3 rounded-md bg-white border h-100 transition-all">
                                    <i class="bi bi-qr-code-scan fs-3 d-block mb-1"></i>
                                    <span class="small fw-600 d-block">QRIS</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-4">
                            <label class="modern-payment-card w-100">
                                <input type="radio" name="metode_pembayaran" value="cod" class="d-none">
                                <div class="payment-content text-center p-3 rounded-md bg-white border h-100 transition-all">
                                    <i class="bi bi-cash-stack fs-3 d-block mb-1"></i>
                                    <span class="small fw-600 d-block">COD</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white rounded-lg p-4 shadow-sm border sticky-summary" style="top: 80px;">
                    <h6 class="fw-700 mb-4">Ringkasan Belanja</h6>
                    
                    <div class="mb-4">
                        <label class="small fw-700 text-muted mb-2 d-block">Jumlah Barang</label>
                        <div class="modern-qty-input-big">
                            <button type="button" id="minusBtn" class="btn"><i class="bi bi-dash"></i></button>
                            <input type="number" name="qty" id="buyQty" value="1" min="1" max="<?= $product['stok'] ?>" class="form-control text-center fw-800 border-0 bg-transparent">
                            <button type="button" id="plusBtn" class="btn"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Harga Satuan</span>
                        <span class="text-dark fw-600">Rp<?= number_format($product['harga_jual'], 0, ',', '.') ?> <span class="text-muted small fw-400">x <span id="qtyDisplay">1</span></span></span>
                    </div>

                    <hr class="opacity-10 mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <span class="fw-700">Total Tagihan</span>
                        <span class="text-accent fw-800 fs-4" id="totalDisplay">Rp<?= number_format($product['harga_jual'], 0, ',', '.') ?></span>
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-3 fw-800 rounded-md border-0 shadow-accent-sm">
                        BUAT PESANAN SEKARANG
                    </button>
                    
                    <div class="mt-4 p-3 bg-light rounded-md text-center">
                         <div class="small text-muted"><i class="bi bi-shield-lock-fill text-accent me-1"></i> Jaminan Belanja Aman</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.letter-spacing-1 { letter-spacing: 1px; }
.modern-payment-card input:checked + .payment-content {
    border-color: var(--primary) !important;
    background: #fff8f6 !important;
    color: var(--primary) !important;
    box-shadow: 0 5px 15px rgba(238, 77, 45, 0.1);
}
.payment-content { cursor: pointer; border: 1px solid #eee; }
.modern-qty-input-big {
    display: flex;
    align-items: center;
    background: #f8f8f8;
    border-radius: 12px;
    padding: 5px;
}
.modern-qty-input-big .btn { width: 44px; height: 44px; background: #fff; border-radius: 10px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.03); transition: all 0.2s; }
.modern-qty-input-big .btn:active { transform: scale(0.9); }
.shadow-accent-sm { box-shadow: 0 10px 20px rgba(238, 77, 45, 0.2); }
@media (min-width: 992px) { .sticky-summary { position: sticky; top: 100px; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('buyQty');
    const qtyDisplay = document.getElementById('qtyDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const unitPrice = <?= $product['harga_jual'] ?>;
    
    function updateValues() {
        let qty = parseInt(qtyInput.value) || 1;
        qtyDisplay.innerText = qty;
        let total = unitPrice * qty;
        totalDisplay.innerText = 'Rp' + total.toLocaleString('id-ID');
    }

    document.getElementById('plusBtn').addEventListener('click', () => {
        if(parseInt(qtyInput.value) < parseInt(qtyInput.max)) {
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updateValues();
        }
    });

    document.getElementById('minusBtn').addEventListener('click', () => {
        if(parseInt(qtyInput.value) > 1) {
            qtyInput.value = parseInt(qtyInput.value) - 1;
            updateValues();
        }
    });

    qtyInput.addEventListener('input', updateValues);
});
</script>
