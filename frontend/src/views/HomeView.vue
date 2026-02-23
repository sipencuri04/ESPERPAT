<template>
  <div class="home-view">
    <div class="container pb-20">
      
      <!-- Top Nav Header -->
      <header class="home-header-pill">
        <div class="user-greeting">
          <div class="avatar-gradient">
            {{ userInitials }}
          </div>
          <div class="greeting-text">
            <h2>Halo, {{ user.name || 'Customer' }} <span class="wave">👋</span></h2>
            <p>Selamat datang! Cari produk terbaru di sini</p>
          </div>
        </div>
        
        <div class="header-actions">
           <div class="pill-actions">
              <router-link to="/cart" class="cart-pill-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span v-if="cartStore.items.length > 0" class="badge-dot">{{ cartStore.totalItems }}</span>
              </router-link>
              <router-link to="/profile" class="profile-pill-icon">
                 {{ userInitials }}
              </router-link>
           </div>
        </div>
      </header>

      <!-- Search Bar -->
      <div class="search-section">
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input type="text" v-model="searchQuery" @keyup.enter="handleSearch" placeholder="Cari oli, kampas rem, knalpot..." />
        </div>
      </div>

      <!-- Categories / Brands -->
      <section class="brand-section">
        <div class="brand-list">
          <div class="brand-item" v-for="(category, i) in categories" :key="i">
            <!-- Mockup gradient style rounded rectangle box -->
            <div class="brand-box category-icon-box" v-html="category.icon"></div>
            <span class="brand-name">{{ category.name }}</span>
          </div>
        </div>
      </section>

      <!-- Flash Sale Banner -->
      <section class="featured-banner">
        <div class="banner-card gradient-bg-horizontal">
          <div class="banner-info">
            <h2 class="italic-title">FLASH SALE!</h2>
            <p>Racing Parts up to 70%</p>
            <button class="shop-btn-black">Beli Sekarang</button>
          </div>
        </div>
        <div class="dots-indicator">
          <span class="active"></span>
          <span></span>
          <span></span>
        </div>
      </section>

      <!-- Popular Products -->
      <section class="arrival-section">
        <div class="section-header">
          <h3>Popular Products</h3>
          <router-link to="/products" class="see-all">Lihat Semua</router-link>
        </div>
        
        <div class="product-grid">
          <router-link :to="`/product/${product.id}`" class="product-card" v-for="(product, idx) in products" :key="product.id">
            
            <div class="card-image-wrapper">
              <!-- Top Badges -->
              <div class="top-badges">
                <span :class="idx % 2 === 0 ? 'badge-green' : 'badge-orange'">{{ idx % 2 === 0 ? 'Ready' : 'Hampir Habis' }}</span>
                <div class="badge-purple-box" v-if="product.voucher_id || product.discount">
                  <span class="percent">{{ product.discount || 'VOUCHER' }}</span>
                </div>
              </div>
              
              <div class="card-image">
                <img :src="baseUrl + product.image" v-if="product.image" />
                <img src="https://placehold.co/200x200/f5f5f5/999?text=SPERPAT" v-else />
              </div>
            </div>
            
            <div class="card-info">
              <h4>{{ product.name }}</h4>
              <p class="price">{{ formatCurrency(product.harga_jual) }}</p>
              
              <button class="buy-btn gradient-bg-horizontal">Beli Sekarang</button>
            </div>
          </router-link>
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import client from '../api/client';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';

const cartStore = useCartStore();

const router = useRouter();
const authStore = useAuthStore();
const products = ref([]);
const baseUrl = 'https://esperpat-api.atech.my.id/';
const user = ref({ name: 'User' });
const searchQuery = ref('');

const userInitials = computed(() => {
  if (!user.value.name) return 'US';
  return user.value.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
});

const handleSearch = () => {
  if (searchQuery.value.trim()) {
      router.push({ path: '/products', query: { search: searchQuery.value } });
  } else {
      router.push('/products');
  }
};

const categories = [
  { name: 'Oli', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>' },
  { name: 'Tires', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle></svg>' },
  { name: 'Brake', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><path d="M9 9v6"></path><path d="M15 9v6"></path></svg>' },
  { name: 'Battery', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="10" rx="2" ry="2"></rect><line x1="22" y1="11" x2="22" y2="13"></line><line x1="6" y1="12" x2="6" y2="12"></line><line x1="10" y1="12" x2="10" y2="12"></line><line x1="14" y1="12" x2="14" y2="12"></line></svg>' },
  { name: 'Accessory', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line><line x1="4.93" y1="19.07" x2="19.07" y2="4.93"></line></svg>' }
];

const fetchProducts = async () => {
  try {
    const res = await client.get('public/products', { params: { limit: 2 } });
    products.value = res.data.data.products;
  } catch (err) {
    console.error(err);
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value);
};


onMounted(() => {
  user.value = {
    name: authStore.user?.name || '[User]'
  };

  fetchProducts();
});
</script>

<style scoped>
.home-view {
  padding-bottom: 90px;
}
.pb-20 {
  padding-bottom: 20px;
}

/* Header Pill Neumorphic */
.home-header-pill {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 14px 12px 12px;
  background: white;
  border-radius: 999px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.05); /* Soft elevated shadow */
  margin: 1.5rem 1.25rem 1.5rem;
  border: 1px solid rgba(255,255,255,0.8);
}

.user-greeting {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar-gradient {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #a855f7, #3b82f6);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.15rem;
  box-shadow: 0 6px 15px rgba(168, 85, 247, 0.3);
  flex-shrink: 0;
}

.greeting-text h2 {
  font-size: 1.05rem;
  font-weight: 900;
  color: #1e293b;
  margin: 0 0 2px 0;
  display: flex;
  align-items: center;
  letter-spacing: -0.2px;
}

.greeting-text .wave {
  font-size: 1rem;
  margin-left: 6px;
}

.greeting-text p {
  font-size: 0.75rem;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

.header-actions {
  display: flex;
  align-items: center;
}

.pill-actions {
  display: flex;
  align-items: center;
  background: #f8fafc;
  border-radius: 999px;
  padding: 4px;
  box-shadow: inset 0 2px 5px rgba(0,0,0,0.02);
  border: 1px solid #f1f5f9;
  gap: 6px;
}

.cart-pill-icon {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  color: #111;
  text-decoration: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

.cart-pill-icon .badge-dot {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 18px;
  height: 18px;
  background: #a855f7;
  color: white;
  font-size: 0.65rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 2px solid white;
}

.profile-pill-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #1e293b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.9rem;
  text-decoration: none;
}

/* Search */
.search-section {
  padding: 0 1.5rem;
  margin-bottom: 1.5rem;
}

.search-bar {
  background: white;
  display: flex;
  align-items: center;
  padding: 0 1.25rem;
  border-radius: 9999px;
  height: 52px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
  border: 1px solid #f8fafc;
}

.search-bar input {
  border: none;
  background: transparent;
  width: 100%;
  padding-left: 12px;
  font-size: 0.95rem;
  outline: none;
  color: #111;
}

/* Brands */
.brand-section {
  margin-bottom: 2rem;
}

.brand-list {
  display: flex;
  gap: 18px;
  padding: 0 1.5rem;
  overflow-x: auto;
  scrollbar-width: none;
}
.brand-list::-webkit-scrollbar { display: none; }

.brand-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.brand-box {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  background: linear-gradient(135deg, #6366f1, #a855f7); /* Mockup gradient */
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
  flex-shrink: 0;
  box-shadow: 0 6px 15px rgba(168, 85, 247, 0.2);
}

.category-icon-box :deep(svg) {
  width: 28px;
  height: 28px;
  stroke: white; /* Forces the feather icons to be white */
}

.brand-name {
  font-size: 0.75rem;
  font-weight: 600;
  color: #111;
}

/* Banner */
.featured-banner {
  padding: 0 1.5rem;
  margin-bottom: 2rem;
}

.banner-card {
  border-radius: 20px;
  display: flex;
  padding: 24px;
  color: #fff;
  height: 160px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
}

.gradient-bg-horizontal {
  background: linear-gradient(90deg, #3b82f6, #9333ea);
}

.banner-info {
  flex: 1;
  z-index: 2;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  width: 100%;
}

.italic-title {
  font-size: 1.8rem;
  font-weight: 900;
  font-style: italic;
  margin-bottom: 4px;
  letter-spacing: 1px;
}

.banner-info p {
  font-size: 0.85rem;
  color: rgba(255,255,255,0.9);
  margin-bottom: 16px;
  font-weight: 500;
}

.shop-btn-black {
  background: #111;
  color: #fff;
  border: none;
  padding: 8px 24px;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.dots-indicator {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 12px;
}

.dots-indicator span {
  width: 20px;
  height: 4px;
  background: #e2e8f0;
  border-radius: 4px;
}

.dots-indicator span.active {
  background: #8b5cf6;
}

/* Products */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1.5rem;
  margin-bottom: 1rem;
}

.section-header h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #111;
}

.see-all {
  font-size: 0.85rem;
  color: #64748b;
  font-weight: 500;
}

.product-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px; /* Slight reduction for wider cards */
  padding: 0 1.25rem;
}

.product-card {
  background: white;
  border-radius: 20px;
  padding: 12px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.04);
  display: flex;
  flex-direction: column;
  text-decoration: none;
  border: 1px solid #f1f5f9;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card:active {
  transform: scale(0.97);
}

.card-image-wrapper {
  background: #f1f5f9;
  border-radius: 16px;
  aspect-ratio: 1 / 1;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  overflow: hidden;
}

.top-badges {
  position: absolute;
  top: 8px;
  left: 8px;
  right: 8px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  z-index: 2;
}

.badge-green {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 999px;
  backdrop-filter: blur(4px);
}

.badge-orange {
  background: rgba(249, 115, 22, 0.1);
  color: #ea580c;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 999px;
  backdrop-filter: blur(4px);
}

.badge-purple-box {
  background: linear-gradient(135deg, #a855f7, #6366f1);
  color: white;
  border-radius: 10px;
  padding: 5px 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  line-height: 1.1;
  box-shadow: 0 4px 12px rgba(168,85,247,0.25);
}

.badge-purple-box .percent {
  font-size: 0.75rem;
  font-weight: 800;
}
.badge-purple-box .off {
  font-size: 0.55rem;
  font-weight: 600;
  opacity: 0.9;
}

.card-image {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px;
}

.card-info {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-info h4 {
  font-size: 0.85rem;
  color: #111;
  margin-bottom: 4px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.price {
  font-weight: 800;
  font-size: 0.95rem;
  color: #111;
  margin-bottom: 12px;
  margin-top: auto;
}

.buy-btn {
  width: 100%;
  padding: 8px;
  border: none;
  border-radius: 999px;
  color: white;
  font-weight: 600;
  font-size: 0.8rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(99,102,241,0.2);
}
</style>
