<template>
  <div class="product-detail">
    <div v-if="loading" class="loader-container">
      <div class="spinner"></div>
    </div>
    
    <div v-else-if="product" class="detail-container">
      <!-- Top Bar -->
      <header class="top-bar">
        <button @click="$router.back()" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">PRODUCT DETAIL</span>
        <div class="header-actions">
           <router-link to="/cart" class="icon-btn-circle">
             <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
           </router-link>
        </div>
      </header>

      <!-- Image Showcase -->
      <section class="image-showcase">
        <div class="main-image-bg">
          <img :src="baseUrl + product.image" v-if="product.image" class="main-img" />
          <img src="https://placehold.co/600x600/f5f5f5/999?text=Engine+Part" v-else class="main-img" />
        </div>
      </section>

      <!-- Thumbnails -->
      <div class="thumbnails">
        <div v-for="i in 3" :key="i" :class="['thumb-box', { active: i === 1 }]">
          <div class="thumb-inner">
             <img :src="baseUrl + product.image" v-if="product.image" />
             <img src="https://placehold.co/100x100/f8fafc/999?text=View" v-else />
          </div>
        </div>
      </div>

      <!-- General Info Card -->
      <div class="info-card">
        <section class="product-info">
          <div class="info-top">
            <span class="category-badge">{{ product.category_name || 'Premium Part' }}</span>
            <div class="rating">
               <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
               <span>4.9</span>
               <span class="reviews">(120 Reviews)</span>
            </div>
          </div>
          <h1 class="product-name">{{ product.name }}</h1>
          <div class="stock-status">
            <span class="dot"></span>
            <span class="stok-text">Available Stock: <strong>{{ product.stok }} pcs</strong></span>
          </div>
          <p class="product-price">{{ formatCurrency(product.harga_jual) }}</p>
        </section>

        <!-- Description -->
        <section class="description">
          <h3>Description</h3>
          <p>{{ product.deskripsi || 'Produk berkualitas tinggi khusus untuk performa mesin Anda. Dibuat dengan material pilihan yang menjamin keawetan dan performa maksimal di setiap perjalanan.' }}</p>
        </section>
      </div>

      <!-- Sticky Bottom Action -->
      <footer class="action-footer">
        <button class="cart-btn-circle" @click="handleAddToCart">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        </button>
        <button class="buy-now-btn gradient-purple" @click="handleBuyNow">BELI SEKARANG</button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import client from '../api/client';
import { useCartStore } from '../stores/cart';

const route = useRoute();
const router = useRouter();
const cartStore = useCartStore();

const product = ref(null);
const loading = ref(true);
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
  cartStore.addItem(product.value);
  alert(`Added ${product.value.name} to cart!`);
};

const handleBuyNow = () => {
  cartStore.addItem(product.value);
  router.push('/cart');
};

onMounted(fetchProduct);
</script>

<style scoped>
.product-detail {
  min-height: 100vh;
  background: #f8fafc;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  position: sticky;
  top: 0;
  background: transparent;
  z-index: 100;
}

.top-bar .title {
  font-weight: 800;
  font-size: 0.95rem;
  color: #111;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.back-btn, .icon-btn-circle {
  background: white;
  border: none;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0,0,0,0.03);
  transition: all 0.2s;
}

.back-btn:active, .icon-btn-circle:active {
  transform: scale(0.9);
}

/* Image Showcase */
.image-showcase {
  padding: 10px 1.5rem 20px;
}

.main-image-bg {
  background: white;
  border-radius: 35px;
  height: 380px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  border: none;
  box-shadow: 0 10px 35px rgba(0,0,0,0.03);
}

.main-img {
  max-width: 80%;
  max-height: 80%;
  object-fit: contain;
  filter: drop-shadow(0 15px 25px rgba(0,0,0,0.08));
}

/* Thumbnails */
.thumbnails {
  display: flex;
  justify-content: flex-start;
  gap: 12px;
  padding: 0 1.5rem;
  margin-bottom: 30px;
}

.thumb-box {
  width: 75px;
  height: 75px;
  border-radius: 20px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px solid transparent;
  padding: 2px;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
  transition: all 0.2s;
}

.thumb-inner {
  width: 100%;
  height: 100%;
  background: white;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.thumb-inner img {
  max-width: 80%;
  max-height: 80%;
}

.thumb-box.active {
  border-color: #a855f7;
  box-shadow: 0 4px 15px rgba(168, 85, 247, 0.2);
}

/* Info Card Style */
.info-card {
  background: white;
  border-top-left-radius: 35px;
  border-top-right-radius: 35px;
  padding: 30px 1.5rem 140px;
  border: none;
  box-shadow: 0 -10px 40px rgba(0,0,0,0.02);
}

.product-info {
  margin-bottom: 25px;
}

.info-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.category-badge {
  background: #f1f5f9;
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 999px;
}

.rating {
  display: flex;
  align-items: center;
  gap: 5px;
}

.rating span {
  font-weight: 800;
  font-size: 0.9rem;
}

.reviews {
  color: #94a3b8;
  font-weight: 500 !important;
  font-size: 0.8rem !important;
  margin-left: 2px;
}

.product-name {
  font-size: 1.5rem;
  font-weight: 900;
  color: #111;
  margin-bottom: 12px;
  line-height: 1.2;
}

.stock-status {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 15px;
}

.stock-status .dot {
  width: 8px;
  height: 8px;
  background: #22c55e;
  border-radius: 50%;
}

.stok-text {
  font-size: 0.85rem;
  color: #64748b;
}

.stok-text strong {
  color: #111;
}

.product-price {
  font-size: 1.75rem;
  font-weight: 900;
  color: #111;
}

/* Description */
.description h3 {
  font-size: 1.1rem;
  font-weight: 800;
  margin-bottom: 10px;
}

.description p {
  font-size: 0.95rem;
  color: #64748b;
  line-height: 1.7;
}

/* Action Footer */
.action-footer {
  position: fixed;
  bottom: 25px;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 3rem);
  max-width: calc(480px - 3rem);
  padding: 8px;
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  display: flex;
  gap: 12px;
  z-index: 1000;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08); /* Soft floating shadow */
  border-radius: 100px; /* Fully rounded pill container */
  border: 1px solid rgba(255, 255, 255, 0.4);
}

.cart-btn-circle {
  width: 58px;
  height: 58px;
  background: white;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #111;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* Slight elevation */
  transition: all 0.2s;
  flex-shrink: 0;
}

.cart-btn-circle:active {
  transform: scale(0.9);
}

.buy-now-btn {
  flex: 1;
  height: 58px;
  color: white;
  border: none;
  font-weight: 800;
  font-size: 1rem;
  border-radius: 999px; /* Pill shaped */
  cursor: pointer;
  letter-spacing: 0.5px;
  box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3); /* Soft purple glow */
  transition: all 0.3s;
}

.gradient-purple {
  background: linear-gradient(135deg, #a855f7, #6366f1);
}

.buy-now-btn:active {
  transform: scale(0.97);
}

.loader-container {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f4f5f7;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #8b5cf6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
