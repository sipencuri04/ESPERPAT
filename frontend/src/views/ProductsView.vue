<template>
  <div class="products-view">
    
    <header class="page-header">
      <h2>Popular Products</h2>
    </header>

    <!-- Category Pills -->
    <div class="category-scroll">
      <span class="pill active">All Products</span>
      <span class="pill">Engine</span>
      <span class="pill">Brakes</span>
      <span class="pill">Tires</span>
      <span class="pill">Tools</span>
    </div>

    <!-- Search and Cart in Page -->
    <div class="search-action-row">
      <div class="search-input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input v-model="search" type="text" placeholder="Search products..." @input="fetchProducts" />
      </div>
      <router-link to="/cart" class="cart-btn-square">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        <span v-if="cartStore.items.length > 0" class="badge-dot">{{ cartStore.totalItems }}</span>
      </router-link>
    </div>

    <!-- Loader -->
    <div v-if="loading" class="loader">
      <div class="spinner"></div>
    </div>

    <!-- Product Grid -->
    <div v-else class="product-grid">
      <router-link :to="`/product/${product.id}`" v-for="(product, idx) in products" :key="product.id" class="product-card">
        <div class="card-image-wrapper">
          <div class="top-badges">
            <span class="badge-green">Ready</span>
            <div class="badge-purple-box" v-if="product.voucher_id || product.discount">
              <span class="percent">{{ product.discount || 'VOUCHER' }}</span>
            </div>
          </div>
          <img :src="baseUrl + product.image" v-if="product.image" class="card-img" />
          <img src="https://placehold.co/200x200/f5f5f5/999?text=SPERPAT" v-else class="card-img" />
        </div>
        <div class="card-info">
          <span class="category-label">{{ product.category_name }}</span>
          <h4>{{ product.name }}</h4>
          <div class="price-row">
            <span class="price-val">{{ formatCurrency(product.harga_jual) }}</span>
            <div class="star-rating">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
              <span>4.8</span>
            </div>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import client from '../api/client';
import { useCartStore } from '../stores/cart';

const router = useRouter();
const cartStore = useCartStore();

const products = ref([]);
const loading = ref(true);
const search = ref('');
const baseUrl = 'https://esperpat-api.atech.my.id/';



const fetchProducts = async () => {
  loading.value = true;
  try {
    const res = await client.get('public/products', {
      params: { search: search.value }
    });
    products.value = res.data.data.products;
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
  }).format(value);
};

import { useRoute } from 'vue-router';
const route = useRoute();

onMounted(() => {
  if (route.query.search) {
    search.value = route.query.search;
  }
  fetchProducts()
});
</script>

<style scoped>
.products-view {
  min-height: 100vh;
  background: #f4f5f7;
  overflow-x: hidden;
  position: relative;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
  overscroll-behavior-y: contain; /* Prevents browser pull-to-refresh */
}



.products-view * {
  box-sizing: border-box;
}

.page-header {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 24px 16px 16px;
  text-align: center;
  width: 100%;
  box-sizing: border-box;
}

.page-header h2 {
  font-size: 1.35rem;
  font-weight: 900;
  color: #111;
  margin: 0;
  line-height: 1.2;
  letter-spacing: -0.5px;
  word-wrap: break-word;
  max-width: 100%;
}

@media (max-width: 400px) {
  .page-header h2 {
    font-size: 1.15rem;
  }
}

.header-left, .header-right {
  display: flex;
  align-items: center;
  height: 44px; /* Force same height as the cart button for perfect centering */
}

.cart-btn-circle {
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

.category-scroll {
  display: flex;
  gap: 12px;
  padding: 0 16px;
  overflow-x: auto;
  scrollbar-width: none;
  margin-bottom: 24px;
  justify-content: flex-start;
  width: 100%;
  -webkit-overflow-scrolling: touch;
}

.category-scroll::-webkit-scrollbar { display: none; }

.pill {
  white-space: nowrap;
  padding: 14px 28px;
  background: white;
  border-radius: 16px; /* Squircle/Rounded corner consistent with mockup */
  font-size: 0.9rem;
  font-weight: 800;
  color: #64748b;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
  cursor: pointer;
  border: 1px solid #f1f5f9;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.pill.active {
  background: linear-gradient(135deg, #6366f1, #a855f7);
  color: #fff;
  border-color: transparent;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
}

.search-action-row {
  display: flex;
  gap: 12px;
  margin: 0 16px 24px;
}

.search-input-wrap {
  flex: 1;
  background: white;
  display: flex;
  align-items: center;
  padding: 0 20px;
  border-radius: 20px;
  height: 56px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 8px 30px rgba(0,0,0,0.03);
  box-sizing: border-box;
}

.cart-btn-square {
  width: 56px;
  height: 56px;
  background: white;
  border: 1px solid #f1f5f9;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 30px rgba(0,0,0,0.03);
  position: relative;
  flex-shrink: 0;
  transition: all 0.2s;
}

.cart-btn-square:active { transform: scale(0.95); }

.cart-btn-square .badge-dot {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #ef4444;
  color: white;
  font-size: 0.6rem;
  font-weight: 800;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 2px solid white;
}

@media (max-width: 400px) {
  .search-action-row {
    margin: 0 12px 20px;
    gap: 10px;
  }
  .search-input-wrap, .cart-btn-square {
    height: 52px;
  }
  .cart-btn-square {
    width: 52px;
  }
}

.search-input-wrap input {
  background: transparent;
  border: none;
  width: 100%;
  padding-left: 12px;
  font-size: 0.95rem;
  outline: none;
  font-family: inherit;
  color: #111;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
  padding: 0 16px 40px;
  width: 100%;
  box-sizing: border-box;
}

@media (max-width: 400px) {
  .product-grid {
    gap: 12px;
    padding: 0 12px 40px;
  }
}

.product-card {
  background: white;
  border-radius: 24px;
  padding: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.04);
  display: flex;
  flex-direction: column;
  text-decoration: none;
  border: 1px solid #f1f5f9;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
}

@media (max-width: 400px) {
  .product-card {
    padding: 10px; /* Reduced to fit content without overflow */
    border-radius: 18px;
  }
}

.product-card:active {
  transform: scale(0.97);
}

.card-image-wrapper {
  background: #f8fafc;
  border-radius: 20px;
  aspect-ratio: 1 / 1;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  overflow: hidden;
}

.card-img {
  max-width: 85%;
  max-height: 85%;
  object-fit: contain;
}

.top-badges {
  position: absolute;
  top: 12px;
  left: 12px;
  right: 12px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  z-index: 2;
}

.badge-green {
  background: rgba(34, 197, 94, 0.1);
  color: #16a34a;
  font-size: 0.6rem;
  font-weight: 700;
  padding: 3px 8px;
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
  box-shadow: 0 8px 16px rgba(168,85,247,0.25);
}

.badge-purple-box .percent { font-size: 0.7rem; font-weight: 800; }
.badge-purple-box .off { font-size: 0.5rem; font-weight: 600; opacity: 0.9; }

.card-info { flex: 1; display: flex; flex-direction: column; }
.category-label { font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; }

@media (max-width: 400px) {
  .category-label {
    font-size: 0.6rem;
  }
}

.card-info h4 {
  font-size: 0.95rem;
  font-weight: 800;
  color: #111;
  margin: 6px 0 12px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
  height: 2.8em;
}

@media (max-width: 400px) {
  .card-info h4 {
    font-size: 0.8rem;
    margin: 4px 0 8px;
    height: 2.8em;
  }
}

.price-row { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
.price-val { font-weight: 900; font-size: 1rem; color: #111; letter-spacing: -0.2px; }

@media (max-width: 400px) {
  .price-val {
    font-size: 0.85rem;
  }
}
.star-rating { display: flex; align-items: center; gap: 4px; font-size: 0.8rem; font-weight: 700; color: #94a3b8; }

.loader { display: flex; justify-content: center; padding: 40px; }
.spinner {
  width: 32px;
  height: 32px;
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
