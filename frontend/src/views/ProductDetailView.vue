<template>
  <div class="product-detail">
    <div v-if="loading" class="loader-container">
      <div class="spinner"></div>
    </div>
    
    <div v-else-if="product" class="detail-container">
      <!-- Top Bar -->
      <header class="top-bar">
        <button @click="$router.back()" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Detail Produk</span>
        <router-link to="/cart" class="back-btn cart-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span v-if="cartStore.totalItems > 0" class="cart-badge">{{ cartStore.totalItems }}</span>
        </router-link>
      </header>

      <!-- Image Showcase -->
      <section class="image-showcase">
        <div class="main-image-bg">
          <img :src="baseUrl + product.image" v-if="product.image" class="main-img" />
          <img src="https://placehold.co/600x600/f5f5f5/999?text=No+Image" v-else class="main-img" />
        </div>
      </section>

      <!-- Info Card -->
      <div class="info-card">
        <!-- Category & Rating -->
        <div class="info-top">
          <span class="category-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
            {{ product.category_name || 'Sparepart' }}
          </span>
          <div class="rating-box">
             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
             <span class="rating-num">4.9</span>
          </div>
        </div>

        <!-- Product Name -->
        <h1 class="product-name">{{ product.name }}</h1>

        <!-- Price & Stock Row -->
        <div class="price-stock-row">
          <div class="price-block">
            <span class="price-main">{{ formatCurrency(product.harga_jual) }}</span>
          </div>
          <div class="stock-chip" :class="{ 'low': product.stok < 10 }">
            <span class="dot"></span>
            <span>Stok: <strong>{{ product.stok }}</strong></span>
          </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Specs Grid -->
        <div class="specs-grid">
          <div class="spec-item">
            <div class="spec-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
            </div>
            <div class="spec-text">
              <span class="spec-label">Kategori</span>
              <span class="spec-val">{{ product.category_name || '-' }}</span>
            </div>
          </div>
          <div class="spec-item">
            <div class="spec-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
            </div>
            <div class="spec-text">
              <span class="spec-label">Pengiriman</span>
              <span class="spec-val">Siap Kirim</span>
            </div>
          </div>
          <div class="spec-item">
            <div class="spec-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <div class="spec-text">
              <span class="spec-label">Garansi</span>
              <span class="spec-val">Original</span>
            </div>
          </div>
          <div class="spec-item">
            <div class="spec-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
            </div>
            <div class="spec-text">
              <span class="spec-label">Terjual</span>
              <span class="spec-val">120+ pcs</span>
            </div>
          </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Description -->
        <section class="description">
          <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            Deskripsi Produk
          </h3>
          <p>{{ product.deskripsi || 'Produk sparepart berkualitas tinggi untuk performa mesin Anda. Dibuat dengan material pilihan yang menjamin keawetan dan performa maksimal.' }}</p>
        </section>

        <!-- Quantity Selector -->
        <div class="qty-section">
          <span class="qty-label">Jumlah</span>
          <div class="qty-control">
            <button class="qty-btn" @click="qty > 1 ? qty-- : null" :disabled="qty <= 1">−</button>
            <span class="qty-num">{{ qty }}</span>
            <button class="qty-btn" @click="qty < product.stok ? qty++ : null" :disabled="qty >= product.stok">+</button>
          </div>
        </div>
      </div>

      <!-- Sticky Bottom Action -->
      <footer class="action-footer">
        <button class="cart-btn-circle" @click="handleAddToCart">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        </button>
        <button class="buy-now-btn" @click="handleBuyNow">
          <span>BELI SEKARANG</span>
          <span class="btn-price">{{ formatCurrency(product.harga_jual * qty) }}</span>
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../api/client';
import { useCartStore } from '../stores/cart';
import { useToast } from '../composables/useToast';

const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();
const toast = useToast();

const product = ref(null);
const loading = ref(true);
const qty = ref(1);
const baseUrl = 'https://esperpat-api.atech.my.id/';

const fetchProduct = async () => {
  try {
    const res = await client.get(`public/products/${route.params.id}`);
    product.value = res.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value);
};

const handleAddToCart = () => {
  for (let i = 0; i < qty.value; i++) {
    cartStore.addItem(product.value);
  }
  toast.success(`${qty.value}x ${product.value.name} ditambahkan ke keranjang!`, 'Berhasil Ditambahkan! 🛒');
};

const handleBuyNow = () => {
  for (let i = 0; i < qty.value; i++) {
    cartStore.addItem(product.value);
  }
  router.push('/cart');
};

onMounted(fetchProduct);
</script>

<style scoped>
.product-detail {
  min-height: 100vh;
  background: transparent;
  font-family: 'Inter', sans-serif;
}

/* Top Bar */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  position: sticky;
  top: 0;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  z-index: 100;
}

.top-bar .title {
  font-weight: 800;
  font-size: 0.9rem;
  color: #1e293b;
  letter-spacing: 0.5px;
}

.back-btn {
  background: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: all 0.2s;
  color: #1e293b;
  position: relative;
}
.back-btn:active { transform: scale(0.9); }

.cart-link { text-decoration: none; color: #1e293b; }
.cart-badge {
  position: absolute; top: -4px; right: -4px;
  width: 18px; height: 18px; border-radius: 50%;
  background: #ef4444; color: white; font-size: 0.6rem; font-weight: 900;
  display: flex; align-items: center; justify-content: center;
}

/* Image Showcase */
.image-showcase { padding: 5px 1.25rem 15px; }

.main-image-bg {
  background: white;
  border-radius: 28px;
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0,0,0,0.04);
}

.main-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 28px;
}

/* Info Card */
.info-card {
  background: white;
  border-top-left-radius: 32px;
  border-top-right-radius: 32px;
  padding: 25px 1.25rem 140px;
  box-shadow: 0 -8px 30px rgba(0,0,0,0.03);
  margin-top: 5px;
}

.info-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.category-badge {
  background: #f0f4ff;
  color: #6366f1;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 999px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.rating-box {
  display: flex;
  align-items: center;
  gap: 4px;
  background: #fffbeb;
  padding: 4px 10px;
  border-radius: 999px;
}

.rating-num { font-weight: 800; font-size: 0.8rem; color: #92400e; }

.product-name {
  font-size: 1.35rem;
  font-weight: 900;
  color: #0f172a;
  margin-bottom: 16px;
  line-height: 1.3;
}

/* Price & Stock */
.price-stock-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.price-main {
  font-size: 1.6rem;
  font-weight: 900;
  background: linear-gradient(135deg, #7c3aed, #6366f1);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.stock-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #166534;
}
.stock-chip.low { background: #fff7ed; border-color: #fed7aa; color: #9a3412; }
.stock-chip.low .dot { background: #f97316; }

.stock-chip .dot {
  width: 7px;
  height: 7px;
  background: #22c55e;
  border-radius: 50%;
}
.stock-chip strong { font-weight: 900; }

.divider {
  height: 1px;
  background: #f1f5f9;
  margin: 0 -1.25rem 20px;
}

/* Specs Grid */
.specs-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 20px;
}

.spec-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 16px;
}

.spec-icon {
  width: 36px;
  height: 36px;
  background: white;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6366f1;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  flex-shrink: 0;
}

.spec-text { display: flex; flex-direction: column; }
.spec-label { font-size: 0.65rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
.spec-val { font-size: 0.8rem; color: #1e293b; font-weight: 800; }

/* Description */
.description { margin-bottom: 20px; }
.description h3 {
  font-size: 0.95rem;
  font-weight: 800;
  margin-bottom: 10px;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 8px;
}

.description p {
  font-size: 0.88rem;
  color: #64748b;
  line-height: 1.8;
  font-weight: 500;
}

/* Quantity Selector */
.qty-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
  padding: 14px 18px;
  border-radius: 18px;
}

.qty-label { font-weight: 800; font-size: 0.9rem; color: #0f172a; }

.qty-control {
  display: flex;
  align-items: center;
  gap: 0;
  background: white;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.qty-btn {
  width: 40px;
  height: 40px;
  border: none;
  background: white;
  font-size: 1.2rem;
  font-weight: 800;
  color: #6366f1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}
.qty-btn:disabled { color: #cbd5e1; cursor: not-allowed; }
.qty-btn:not(:disabled):active { background: #f1f5f9; }

.qty-num {
  width: 40px;
  text-align: center;
  font-size: 1rem;
  font-weight: 900;
  color: #0f172a;
  border-left: 1px solid #f1f5f9;
  border-right: 1px solid #f1f5f9;
  line-height: 40px;
}

/* Action Footer */
.action-footer {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 2.5rem);
  max-width: calc(480px - 2.5rem);
  padding: 6px;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(25px);
  -webkit-backdrop-filter: blur(25px);
  display: flex;
  gap: 10px;
  z-index: 1000;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
  border-radius: 100px;
  border: 1px solid rgba(255, 255, 255, 0.5);
}

.cart-btn-circle {
  width: 52px;
  height: 52px;
  background: white;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #1e293b;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: all 0.2s;
  flex-shrink: 0;
}
.cart-btn-circle:active { transform: scale(0.9); }

.buy-now-btn {
  flex: 1;
  height: 52px;
  color: white;
  border: none;
  font-weight: 800;
  font-size: 0.85rem;
  border-radius: 999px;
  cursor: pointer;
  letter-spacing: 0.3px;
  background: linear-gradient(135deg, #7c3aed, #6366f1);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.buy-now-btn:active { transform: scale(0.97); }

.btn-price {
  font-size: 0.75rem;
  font-weight: 700;
  opacity: 0.85;
  background: rgba(255,255,255,0.15);
  padding: 3px 10px;
  border-radius: 999px;
}

/* Loader */
.loader-container {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #7c3aed;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
