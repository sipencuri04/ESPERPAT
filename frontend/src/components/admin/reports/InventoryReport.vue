<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Inventory Overview</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="summary-cards">
        <div class="card primary">
          <span>Nilai Asset Gudang</span>
          <h3>{{ formatCurrency(data.total_value) }}</h3>
          <small class="text-purple">Berdasarkan Harga Beli (HPP)</small>
        </div>
        <div class="card">
          <span>Total SKU Aktif</span>
          <h3>{{ data.total_sku }} Macam</h3>
          <small class="text-muted">Produk Terdaftar</small>
        </div>
        <div class="card warning">
          <span>Restock Warning</span>
          <h3>{{ data.low_stock?.length || 0 }} Produk</h3>
          <small class="text-red">Stok &le; 5 Pcs</small>
        </div>
      </div>

      <div class="grids mt-4">
        <div class="col-half">
          <h5 class="text-red">⚠️ STOK MENIPIS (RESTOCK SEGERA)</h5>
          <div class="table-container">
            <table class="data-table">
              <thead><tr><th>Produk</th><th class="text-right">Sisa Stok</th></tr></thead>
              <tbody>
                <tr v-for="item in data.low_stock" :key="item.id">
                  <td><strong>{{ item.name }}</strong></td>
                  <td class="text-right text-red font-bold"><strong>{{ item.stok }} Pcs</strong></td>
                </tr>
                <tr v-if="!data.low_stock || data.low_stock.length === 0"><td colspan="2" class="text-center text-muted">Aman! Tidak ada stok sekarat.</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="col-half">
          <h5 class="text-green">💡 STOK TERTINGGI (AMPUH)</h5>
          <div class="table-container">
            <table class="data-table">
              <thead><tr><th>Produk</th><th class="text-right">Sisa Stok</th></tr></thead>
              <tbody>
                <tr v-for="item in data.high_stock" :key="item.id">
                  <td><strong>{{ item.name }}</strong></td>
                  <td class="text-right text-green"><strong>{{ item.stok }} Pcs</strong></td>
                </tr>
                <tr v-if="!data.high_stock || data.high_stock.length === 0"><td colspan="2" class="text-center text-muted">Belum ada barang overstock (>50).</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import client from '@/api/client';
import { useToast } from '@/composables/useToast';

const toast = useToast();
const loading = ref(true);
const data = ref({});

const loadData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/advanced/inventory`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Inventory');
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
const exportData = (type) => { 
  const token = localStorage.getItem('token');
  const baseUrl = import.meta.env.VITE_API_BASE_URL || '/api';
  const cleanBaseUrl = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;
  const url = `${cleanBaseUrl}/reports/advanced/export-inventory/${type}?token=${token}`;
  window.open(url, '_blank');
};

onMounted(loadData);
</script>

<style scoped>
/* COMPACT UI FOR MOBILE APP */
.report-panel { background: white; border-radius: 16px; padding: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
.panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.panel-header h4 { font-weight: 800; font-size: 0.95rem; color: #111; }
.actions { display: flex; gap: 8px; }
.btn-export { padding: 6px 12px; border-radius: 10px; font-weight: 700; font-size: 0.75rem; border: none; cursor: pointer; }
.btn-export.pdf { background: #fee2e2; color: #ef4444; }
.btn-export.excel { background: #dcfce7; color: #22c55e; }

.summary-cards { display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 8px; }
.card { padding: 12px; border-radius: 15px; background: #f8fafc; border: 1px solid #f1f5f9; }
.card.primary { background: #4c1d95; }
.card.primary span { color: #c4b5fd; }
.card.primary h3 { color: white; }
.card.warning { background: #fff1f2; border-color: #ffe4e6; }
.card.warning h3 { color: #be123c; }

.card span { font-size: 0.6rem; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; }
.card h3 { font-size: 1rem; font-weight: 900; margin: 4px 0; color: #0f172a; word-break: break-all; }
.card small { font-size: 0.6rem; display: block; }
.card small.text-muted { color: #94a3b8; }
.card small.text-purple { color: #ddd6fe; font-weight: 600;}
.card small.text-red { color: #ef4444; font-weight: 600;}

.grids { display: grid; grid-template-columns: 1fr; gap: 15px; }
.mt-4 { margin-top: 20px; }
h5 { margin-bottom: 8px; font-weight: 900; font-size: 0.8rem; }
.text-red { color: #e11d48; }
.text-green { color: #059669; }

.table-container { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
.data-table th { background: #f8fafc; text-align: left; padding: 8px 12px; font-size: 0.65rem; text-transform: uppercase; color: #475569; border-bottom: 1px solid #cbd5e1; }
.data-table td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.loader { padding: 20px; font-size: 0.8rem; text-align: center; color: #94a3b8; font-weight: 600; }
</style>
