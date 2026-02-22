<template>
  <div class="admin-stock-report">
    <div class="container mobile-frame">
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Laporan Stok</span>
        <div style="width: 40px"></div>
      </header>

      <!-- Stock Overview Cards -->
      <div class="stock-overview animate-fade-up">
        <div class="o-card total">
          <span class="lbl">Total Produk</span>
          <h3 class="val">{{ stockData.total_items || 0 }}</h3>
        </div>
        <div class="o-card warning">
          <span class="lbl">Stok Menipis</span>
          <h3 class="val">{{ lowStockCount }}</h3>
        </div>
      </div>

      <!-- Search & Filter -->
      <div class="search-bar animate-fade-up" style="animation-delay: 0.1s">
        <div class="search-box">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input v-model="searchQuery" type="text" placeholder="Cari nama sparepart..." />
        </div>
      </div>

      <!-- Stock Table-like List -->
      <div class="stock-list animate-fade-up" style="animation-delay: 0.2s">
        <div v-if="loading" class="loader">Loading stock data...</div>
        <div v-else v-for="item in filteredStock" :key="item.id" class="stock-item" :class="{ 'is-low': item.stock <= 5 }">
          <div class="item-main">
            <div class="item-img">
               <img :src="baseUrl + item.image" v-if="item.image" />
               <div v-else class="img-placeholder">SP</div>
            </div>
            <div class="item-info">
              <span class="name">{{ item.name }}</span>
              <span class="sku">Code: {{ item.slug }}</span>
            </div>
            <div class="item-qty">
              <span class="qty-val">{{ item.stock }}</span>
              <span class="qty-unit">Pcs</span>
            </div>
          </div>
          <div v-if="item.stock <= 5" class="low-tag">Restock Segera!</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import client from '../../api/client';

const stockData = ref({ total_items: 0, products: [] });
const searchQuery = ref('');
const loading = ref(true);
const baseUrl = import.meta.env.VITE_API_BASE_URL.replace('/api/', '/');

const fetchStock = async () => {
  loading.value = true;
  try {
    const res = await client.get('reports/stock');
    stockData.value = res.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const filteredStock = computed(() => {
  if (!stockData.value.products) return [];
  return stockData.value.products.filter(p => 
    p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const lowStockCount = computed(() => {
  if (!stockData.value.products) return 0;
  return stockData.value.products.filter(p => p.stock <= 5).length;
});

onMounted(fetchStock);
</script>

<style scoped>
.admin-stock-report { min-height: 100vh; background: #fdfdfd; padding-bottom: 50px; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; position: sticky; top: 0; z-index: 10; background: #fdfdfd; }
.top-bar .title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }

.stock-overview { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0 1.5rem; margin-bottom: 25px; }
.o-card { padding: 20px; border-radius: 24px; border: 1px solid #f1f1f1; }
.o-card.total { background: #111; color: white; }
.o-card.warning { background: #fef2f2; border: 1px solid #fee2e2; }
.o-card .lbl { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px; opacity: 0.7; }
.o-card .val { font-size: 1.5rem; font-weight: 900; }
.o-card.warning .val { color: #ef4444; }

.search-bar { padding: 0 1.5rem; margin-bottom: 20px; }
.search-box { background: #f9f9f9; border: 1px solid #eee; border-radius: 14px; padding: 0 15px; display: flex; align-items: center; gap: 10px; height: 50px; }
.search-box input { border: none; background: transparent; flex: 1; font-weight: 700; outline: none; font-size: 0.9rem; }

.stock-list { padding: 0 1.5rem; display: flex; flex-direction: column; gap: 12px; }
.stock-item { background: white; border-radius: 20px; border: 1px solid #f1f1f1; padding: 15px; transition: 0.2s; }
.stock-item.is-low { border-color: #fee2e2; background: #fffafb; }

.item-main { display: flex; align-items: center; gap: 15px; }
.item-img { width: 45px; height: 45px; border-radius: 10px; overflow: hidden; background: #f5f5f5; }
.item-img img { width: 100%; height: 100%; object-fit: cover; }
.img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #ddd; font-size: 0.7rem; }

.item-info { flex: 1; }
.item-info .name { display: block; font-size: 0.85rem; font-weight: 800; color: #111; margin-bottom: 2px; }
.item-info .sku { font-size: 0.65rem; color: #bbb; font-weight: 700; }

.item-qty { text-align: right; }
.qty-val { display: block; font-size: 1rem; font-weight: 900; color: #111; }
.qty-unit { font-size: 0.6rem; color: #999; font-weight: 700; text-transform: uppercase; }

.stock-item.is-low .qty-val { color: #ef4444; }

.low-tag { margin-top: 10px; font-size: 0.6rem; font-weight: 900; color: #ef4444; text-transform: uppercase; background: #fee2e2; display: inline-block; padding: 3px 8px; border-radius: 6px; }

.loader { text-align: center; padding: 40px; color: #ccc; font-weight: 700; }
</style>
