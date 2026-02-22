<template>
  <div class="admin-reports">
    <div class="container mobile-frame">
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Laporan Penjualan</span>
        <div style="width: 40px"></div>
      </header>

      <!-- Period Selector -->
      <div class="period-selector animate-fade-up">
        <select v-model="selectedMonth" @change="fetchData">
          <option v-for="(m, i) in months" :key="i" :value="i+1">{{ m }}</option>
        </select>
        <select v-model="selectedYear" @change="fetchData">
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid animate-fade-up">
        <div class="stat-card">
          <span class="label">Total Omzet</span>
          <h3 class="val">{{ formatCurrency(reportData.total_sales || 0) }}</h3>
          <span class="trend up">↑ 12% vs bln lalu</span>
        </div>
        <div class="stat-card">
          <span class="label">Total Pesanan</span>
          <h3 class="val">{{ reportData.total_orders || 0 }}</h3>
          <span class="trend">Pesanan Selesai</span>
        </div>
      </div>

      <!-- Chart Mockup (Premium Styled) -->
      <div class="chart-section animate-fade-up" style="animation-delay: 0.1s">
        <div class="chart-header">
          <h4>Grafik Penjualan</h4>
          <span class="sub">Harian di bulan ini</span>
        </div>
        <div class="chart-mock">
          <div class="bar-container">
            <div v-if="chartData.length === 0" style="width: 100%; text-align: center; color: #666; font-size: 0.8rem; align-self: center;">
              Data grafik tidak tersedia
            </div>
            <div v-else v-for="(h, idx) in chartData" :key="idx" class="bar-wrap">
              <div class="bar" :style="{ height: h + '%' }"></div>
            </div>
          </div>
          <div class="chart-labels">
            <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="top-products animate-fade-up" style="animation-delay: 0.2s">
        <div class="section-header">
          <h4>Produk Terlaris</h4>
          <button class="txt-btn" v-if="topProducts.length > 0">Lihat Semua</button>
        </div>
        <div class="product-list-mini">
          <div v-if="topProducts.length === 0" style="text-align: center; color: #999; font-size: 0.8rem; padding: 20px 0;">
             Belum ada data penjualan
          </div>
          <div v-else v-for="(p, i) in topProducts" :key="i" class="prod-row">
            <div class="rank">{{ i + 1 }}</div>
            <div class="p-info">
              <span class="p-name">{{ p.name }}</span>
              <span class="p-cat">Kategori Sparepart</span>
            </div>
            <div class="p-stats">
              <span class="p-qty">{{ p.sold }} Terjual</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import client from '../../api/client';

const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(new Date().getFullYear());
const reportData = ref({});
const loading = ref(true);

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const years = [2024, 2025, 2026];

const topProducts = ref([]);
const chartData = ref([]);

const fetchData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/sales?month=${selectedMonth.value}&year=${selectedYear.value}`);
    reportData.value = res.data.data;
    topProducts.value = res.data.data.top_products || [];
    chartData.value = res.data.data.chart_data || [];
  } catch (err) {
    reportData.value = {};
    topProducts.value = [];
    chartData.value = [];
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

onMounted(fetchData);
</script>

<style scoped>
.admin-reports { min-height: 100vh; background: #fdfdfd; padding-bottom: 50px; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; position: sticky; top: 0; z-index: 10; background: #fdfdfd; }
.top-bar .title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }

.period-selector { padding: 0 1.5rem; display: flex; gap: 10px; margin-bottom: 20px; }
.period-selector select { flex: 1; height: 48px; border-radius: 14px; border: 1px solid #eee; padding: 0 15px; font-family: inherit; font-weight: 700; background: #f9f9f9; outline: none; }

.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0 1.5rem; margin-bottom: 25px; }
.stat-card { background: white; padding: 20px; border-radius: 24px; border: 1px solid #f1f1f1; box-shadow: 0 10px 20px rgba(0,0,0,0.02); }
.stat-card .label { font-size: 0.65rem; font-weight: 700; color: #999; text-transform: uppercase; display: block; margin-bottom: 8px; }
.stat-card .val { font-size: 1.1rem; font-weight: 900; color: #111; margin-bottom: 5px; }
.stat-card .trend { font-size: 0.65rem; font-weight: 700; color: #bbb; }
.stat-card .trend.up { color: #22c55e; }

.chart-section { margin: 0 1.5rem 25px; background: #111; border-radius: 28px; padding: 25px; color: white; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
.chart-header h4 { font-size: 1rem; font-weight: 800; }
.chart-header .sub { font-size: 0.7rem; opacity: 0.5; }

.chart-mock { margin-top: 30px; }
.bar-container { height: 120px; display: flex; align-items: flex-end; justify-content: space-between; gap: 10px; padding: 0 10px; }
.bar-wrap { flex: 1; height: 100%; display: flex; align-items: flex-end; }
.bar { width: 100%; border-top-left-radius: 8px; border-top-right-radius: 8px; background: linear-gradient(to top, #3b82f6, #60a5fa); min-height: 5px; opacity: 0.8; transition: opacity 0.3s; }
.bar:hover { opacity: 1; }
.chart-labels { display: flex; justify-content: space-between; margin-top: 15px; padding: 0 5px; }
.chart-labels span { font-size: 0.6rem; font-weight: 700; color: #666; }

.top-products { padding: 0 1.5rem; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.section-header h4 { font-weight: 800; }
.txt-btn { background: none; border: none; font-size: 0.75rem; font-weight: 700; color: #3b82f6; }

.product-list-mini { display: flex; flex-direction: column; gap: 10px; }
.prod-row { background: white; padding: 15px; border-radius: 20px; border: 1px solid #f1f1f1; display: flex; align-items: center; gap: 15px; }
.rank { width: 28px; height: 28px; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.8rem; color: #bbb; }
.p-info { flex: 1; }
.p-name { display: block; font-size: 0.85rem; font-weight: 800; color: #111; }
.p-cat { font-size: 0.65rem; color: #999; font-weight: 600; }
.p-stats .p-qty { font-size: 0.75rem; font-weight: 900; color: #22c55e; background: #f0fdf4; padding: 4px 10px; border-radius: 8px; }
</style>
