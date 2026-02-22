<template>
  <div class="cart-view">
    <div class="container mobile-frame">
      <!-- Header -->
      <header class="cart-header">
        <button @click="$router.push('/')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Cart</span>
        <button class="more-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
        </button>
      </header>

      <!-- Ship To -->
      <div class="ship-to animate-fade-up">
        <div class="user-info">
          <img src="https://ui-avatars.com/api/?name=User&background=random" class="avatar" />
          <div class="address">
            <span>Ship to <strong>Customer</strong></span>
            <p>Tegalsari, Surabaya</p>
          </div>
          <svg class="chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
      </div>

      <!-- Item List (Real Data) -->
      <div v-if="cartStore.items.length > 0" class="item-list">
        <div class="cart-item" v-for="(item, idx) in cartStore.items" :key="item.id">
          <div class="item-content">
            <div class="img-box">
              <img :src="baseUrl + item.image" v-if="item.image" alt="Product" />
              <img src="https://placehold.co/200x200/e2e8f0/64748b?text=SPERPAT" v-else alt="Product" />
            </div>
            <div class="info">
              <h4>{{ item.name }}</h4>
              <p class="brand-text">Swift Kasir / SPERPAT</p>
              <div class="price-val">{{ formatCurrency(item.harga_jual) }}</div>
              
              <div class="row-actions">
                 <div class="qty-control">
                   <button class="qty-btn" @click="cartStore.updateQty(item.id, item.qty - 1)">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                   </button>
                   <span class="qty">{{ item.qty }}</span>
                   <button class="qty-btn plus" @click="cartStore.updateQty(item.id, item.qty + 1)">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                   </button>
                 </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty Cart Message -->
      <div v-else class="empty-cart">
        <img src="https://placehold.co/200x200/fff/ccc?text=Empty+Cart" alt="Empty" />
        <p>Your cart is empty.</p>
        <router-link to="/products" class="shop-now-link">Shop Now</router-link>
      </div>

      <!-- Coupon -->
      <div v-if="cartStore.items.length > 0" class="coupon-section animate-fade-up" style="animation-delay: 0.2s">
        <p class="section-label">Punya Kupon Promo?</p>
        
        <div v-if="appliedPromo" class="coupon-box active">
          <div class="code-info">
             <span class="code">{{ appliedPromoCode }}</span>
             <span class="status">Kupon Berhasil Diterapkan <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
          </div>
          <button @click="removePromo" class="remove-coupon-btn">✕</button>
        </div>
        
        <div v-else class="coupon-input-wrap">
          <input v-model="promoInput" type="text" placeholder="Masukkan Kode Kupon" @keyup.enter="applyPromo" />
          <button @click="applyPromo" :disabled="loadingPromo" class="apply-btn">
            {{ loadingPromo ? 'Cek..' : 'Gunakan' }}
          </button>
        </div>
        
        <p v-if="promoError" class="promo-error">{{ promoError }}</p>
      </div>

      <!-- Summary -->
      <div v-if="cartStore.items.length > 0" class="summary-section">
        <div class="summary-row">
          <span>Sub Total</span>
          <strong>{{ formatCurrency(cartStore.subtotal) }}</strong>
        </div>
        <div class="summary-row discount" v-if="appliedPromo">
          <span>Diskon Kupon</span>
          <strong>-{{ formatCurrency(appliedPromo.discount) }}</strong>
        </div>
        <div class="divider"></div>
        <div class="summary-row total">
          <span>Total</span>
          <strong>{{ formatCurrency(Math.max(cartStore.subtotal - (appliedPromo ? appliedPromo.discount : 0), 0)) }}</strong>
        </div>
      </div>

      <!-- Checkout Button -->
      <footer v-if="cartStore.items.length > 0" class="cart-footer">
        <button class="checkout-btn" @click="handleCheckout" :disabled="submitting">
          {{ submitting ? 'Processing...' : 'Checkout' }}
        </button>
      </footer>

      <!-- Confirmation Modal -->
      <Transition name="slide-up">
        <div v-if="isConfirmOpen" class="modal-overlay" @click.self="isConfirmOpen = false">
          <div class="confirm-card">
            <div class="handle"></div>
            <div class="confirm-head">
              <div class="icon-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <h3>Konfirmasi Pesanan</h3>
              <p>Apakah anda yakin ingin melakukan checkout sekarang?</p>
            </div>
            <div class="confirm-actions">
              <button class="btn-no" @click="isConfirmOpen = false">Batal</button>
              <button class="btn-yes" @click="confirmOrder">Ya, Checkout</button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Success Modal -->
      <Transition name="fade">
        <div v-if="isSuccessOpen" class="success-overlay">
          <div class="success-card">
            <div class="success-icon animate-pop">
              <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h2>Berhasil!</h2>
            <p>Pesanan anda telah dibuat. Admin akan segera memproses pesanan anda.</p>
            <button class="btn-finish" @click="finishCheckout">Lihat Pesanan Saya</button>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import client from '../api/client';

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();
const baseUrl = import.meta.env.VITE_API_BASE_URL.replace('/api/', '/');

const isConfirmOpen = ref(false);
const isSuccessOpen = ref(false);
const submitting = ref(false);

const promoInput = ref('');
const appliedPromoCode = ref('');
const appliedPromo = ref(null);
const promoError = ref('');
const loadingPromo = ref(false);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value);
};

const applyPromo = async () => {
    promoError.value = '';
    if (!promoInput.value.trim()) return;

    if (!authStore.isAuthenticated) {
        alert('Silakan login terlebih dahulu untuk menggunakan kupon');
        router.push('/login');
        return;
    }

    loadingPromo.value = true;
    try {
        const payload = {
            code: promoInput.value.trim(),
            items: cartStore.items.map(it => ({
                product_id: it.id,
                qty: it.qty
            }))
        };
        const res = await client.post('promos/validate', payload);
        appliedPromo.value = res.data.data;
        appliedPromoCode.value = payload.code;
        promoInput.value = '';
    } catch (err) {
        promoError.value = err.response?.data?.message || 'Kode kupon tidak valid';
    } finally {
        loadingPromo.value = false;
    }
};

const removePromo = () => {
    appliedPromo.value = null;
    appliedPromoCode.value = '';
    promoError.value = '';
};

const handleCheckout = () => {
    if (!authStore.isAuthenticated) {
        alert('Silakan login terlebih dahulu');
        router.push('/login');
        return;
    }
    isConfirmOpen.value = true;
};

const confirmOrder = async () => {
    isConfirmOpen.value = false;
    submitting.value = true;
    
    try {
        const orderData = {
            items: cartStore.items.map(it => ({
                product_id: it.id,
                qty: it.qty
            })),
            promo_code: appliedPromoCode.value || null,
            alamat: 'Tegalsari, Surabaya', // Dummy for now, can be expanded
            metode_pembayaran: 'transfer'
        };

        await client.post('orders', orderData);
        
        // Success
        cartStore.items = []; // Clear cart locally
        isSuccessOpen.value = true;
    } catch (err) {
        alert(err.response?.data?.message || 'Gagal membuat pesanan');
    } finally {
        submitting.value = false;
    }
};

const finishCheckout = () => {
    isSuccessOpen.value = false;
    router.push('/admin/orders'); // Assuming customers go to orders list (fix if there is a separate customer view)
};
</script>

<style scoped>
.cart-view {
  min-height: 100vh;
  background: #f4f5f7;
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  position: sticky;
  top: 0;
  background: #f4f5f7;
  z-index: 10;
}

.cart-header .title {
  font-weight: 800;
  font-size: 1rem;
  color: #111;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.back-btn, .more-btn {
  background: white;
  border: 1px solid #f1f5f9;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

/* Ship To */
.ship-to {
  padding: 0 1.5rem;
  margin-bottom: 1.5rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 12px 16px;
  border-radius: 16px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 12px rgba(0,0,0,0.02);
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}

.address { flex: 1; }
.address span { font-size: 0.8rem; color: #94a3b8; }
.address p { font-size: 0.85rem; color: #111; font-weight: 700; margin-top: 1px; }

/* Items */
.item-list {
  padding: 0 1.5rem;
  margin-bottom: 24px;
}

.cart-item {
  margin-bottom: 20px;
  background: white;
  border-radius: 20px;
  padding: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.03);
  border: 1px solid #f1f5f9;
}

.item-content {
  display: flex;
  gap: 16px;
}

.img-box {
  width: 100px;
  height: 100px;
  background: #e2e8f0;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  flex-shrink: 0;
}

.img-box img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.info { 
  flex: 1; 
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding-top: 4px;
}
.info h4 { font-size: 0.95rem; line-height: 1.3; color: #111; font-weight: 800; margin-bottom: 4px; }

.brand-text { font-size: 0.8rem; color: #0ea5e9; font-weight: 600; margin-bottom: 4px; }

.price-val { font-weight: 800; font-size: 1.05rem; color: #111; margin-bottom: 12px; }

.row-actions {
  display: flex;
  align-items: center;
}

.qty-control {
  display: flex;
  align-items: center;
  background: white;
  border-radius: 999px;
  padding: 2px 6px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  width: fit-content;
}

.qty-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: none;
  background: transparent;
  color: #111;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.plus { background: #ff5e3a; color: white; }
.qty { width: 30px; text-align: center; font-weight: 800; font-size: 0.95rem; color: #111;}


/* Empty State */
.empty-cart {
  text-align: center;
  padding: 60px 20px;
}

.empty-cart img {
  margin-bottom: 20px;
  opacity: 0.5;
}

.empty-cart p {
  color: #999;
  font-weight: 600;
  margin-bottom: 20px;
}

.shop-now-link {
  color: #22c55e;
  font-weight: 800;
  text-decoration: none;
}

/* Coupon */
.coupon-section {
  padding: 0 1.5rem;
  margin-bottom: 30px;
}

.section-label { font-size: 0.85rem; color: #999; margin-bottom: 12px; font-weight: 700; }

.coupon-input-wrap {
  display: flex;
  gap: 10px;
}

.coupon-input-wrap input {
  flex: 1;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  padding: 0 16px;
  font-family: inherit;
  font-size: 0.9rem;
  font-weight: 700;
  text-transform: uppercase;
  outline: none;
}

.apply-btn {
  background: #111;
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0 16px;
  font-weight: 700;
  cursor: pointer;
  height: 48px;
}
.apply-btn:disabled { opacity: 0.5; }

.promo-error { color: #ef4444; font-size: 0.8rem; margin-top: 8px; font-weight: 600;}

.coupon-box {
  background: #fdfdfd;
  border: 1px dashed #22c55e;
  padding: 14px 20px;
  border-radius: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.coupon-box.active { background: #f0fdf4; }

.code-info { display: flex; flex-direction: column; }
.coupon-box .code { font-weight: 800; font-size: 0.95rem; color: #111;}
.coupon-box .status { 
  background: transparent;
  color: #22c55e;
  font-size: 0.8rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}

.remove-coupon-btn { background: none; border: none; font-size: 1.2rem; color: #ef4444; cursor: pointer; }

/* Summary */
.summary-section {
  padding: 0 1.5rem 100px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 0.95rem;
}

.summary-row span { color: #666; font-weight: 500; }
.discount strong { color: #22c55e; }

.divider {
  height: 1px;
  border-top: 1px dashed #eee;
  margin: 16px 0;
}

.total { font-size: 1.1rem; }
.total strong { font-size: 1.25rem; font-weight: 800; color: #000; }

/* Footer */
.cart-footer {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 480px;
  padding: 20px 1.5rem 40px;
  background: white;
  z-index: 100;
}

.checkout-btn {
  width: 100%;
  height: 58px;
  background: linear-gradient(90deg, #6366f1, #a855f7);
  color: white;
  border: none;
  border-radius: 18px;
  font-weight: 900;
  font-size: 1rem;
  cursor: pointer;
  letter-spacing: 1px;
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.25);
}

.checkout-btn:disabled {
  background: #ccc;
  box-shadow: none;
  cursor: not-allowed;
}

/* Modal Overlays */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
  display: flex;
  align-items: flex-end;
}

.confirm-card {
  width: 100%;
  max-width: 480px;
  background: white;
  border-top-left-radius: 35px;
  border-top-right-radius: 35px;
  padding: 1rem 1.7rem 3.5rem;
  margin: 0 auto;
}

.handle { width: 40px; height: 5px; background: #eee; border-radius: 5px; margin: 0 auto 2rem; }

.confirm-head { text-align: center; margin-bottom: 2rem; }
.icon-circle {
  width: 65px; height: 65px; background: #f0fdf4; 
  border-radius: 50%; display: flex; align-items: center; 
  justify-content: center; margin: 0 auto 1.5rem;
}
.confirm-head h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; }
.confirm-head p { font-size: 0.85rem; color: #999; line-height: 1.5; }

.confirm-actions { display: grid; grid-template-columns: 1fr 2fr; gap: 12px; }
.btn-no {
  height: 56px; border-radius: 18px; border: 1px solid #eee;
  background: white; font-weight: 700; cursor: pointer;
}
.btn-yes {
  height: 56px; border-radius: 18px; border: none;
  background: #22c55e; color: white; font-weight: 800;
  font-size: 1rem; cursor: pointer;
  box-shadow: 0 10px 20px rgba(34,197,94,0.2);
}

/* Success Overlay */
.success-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: white;
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.success-card { text-align: center; }
.success-icon {
  width: 100px; height: 100px; background: #22c55e;
  border-radius: 50%; display: flex; align-items: center;
  justify-content: center; margin: 0 auto 2rem;
  box-shadow: 0 15px 35px rgba(34,197,94,0.3);
}

.success-card h2 { font-size: 1.75rem; font-weight: 900; margin-bottom: 12px; }
.success-card p { font-size: 0.95rem; color: #777; line-height: 1.6; margin-bottom: 2.5rem; }

.btn-finish {
  width: 100%;
  height: 60px;
  background: #111;
  color: white;
  border: none;
  border-radius: 20px;
  font-weight: 800;
  font-size: 1rem;
  cursor: pointer;
}

.animate-pop { animation: pop 0.5s cubic-bezier(0.26, 0.53, 0.74, 1.48); }
@keyframes pop {
  0% { transform: scale(0); }
  80% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

.animate-fade-up { animation: fadeUp 0.6s ease-out both; }
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.4s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
