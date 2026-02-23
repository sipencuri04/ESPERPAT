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
const exportData = (type) => { toast.success(`Exporting ${type.toUpperCase()}...`); };

onMounted(loadData);
</script>

<style scoped>
.report-panel { background: white; border-radius: 24px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
.panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.panel-header h4 { font-weight: 800; font-size: 1.2rem; color: #111; }
.actions { display: flex; gap: 10px; }
.btn-export { padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer; }
.btn-export.pdf { background: #fee2e2; color: #ef4444; }
.btn-export.excel { background: #dcfce7; color: #22c55e; }

.summary-cards { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 15px; }
.card { padding: 20px; border-radius: 20px; background: #f8fafc; border: 1px solid #f1f5f9; }
.card.primary { background: #4c1d95; margin-right: 0px;}
.card.primary span { color: #c4b5fd; }
.card.primary h3 { color: white; }
.card.warning { background: #fff1f2; border-color: #ffe4e6; }
.card.warning h3 { color: #be123c; }

.card span { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
.card h3 { font-size: 1.6rem; font-weight: 900; margin: 5px 0; color: #0f172a; }
.card small { font-size: 0.75rem;}
.card small.text-muted { color: #94a3b8; }
.card small.text-purple { color: #ddd6fe; font-weight: 600;}
.card small.text-red { color: #ef4444; font-weight: 600;}

.grids { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.mt-4 { margin-top: 25px; }
h5 { margin-bottom: 12px; font-weight: 900; }
.text-red { color: #e11d48; }
.text-green { color: #059669; }

.table-container { border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.data-table th { background: #f8fafc; text-align: left; padding: 12px 20px; font-size: 0.75rem; text-transform: uppercase; color: #475569; border-bottom: 2px solid #cbd5e1; }
.data-table td { padding: 12px 20px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.loader { padding: 40px; text-align: center; color: #94a3b8; font-weight: 600; }
</style>
