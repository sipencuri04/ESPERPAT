<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Sales Report (Penjualan)</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="summary-cards">
        <div class="card">
          <span>Total Omzet</span>
          <h3>{{ formatCurrency(data.total_omzet) }}</h3>
          <small class="green" v-if="data.growth_percent > 0">+{{ data.growth_percent }}% vs Bulan Lalu</small>
        </div>
        <div class="card">
          <span>Total Transaksi</span>
          <h3>{{ formatCurrency(data.total_transaksi).replace('Rp', '') }}</h3>
          <small class="text-muted">Transaksi Sukses (Paid / Completed)</small>
        </div>
        <div class="card primary">
          <span>Rata-Rata Order</span>
          <h3>{{ formatCurrency(data.total_transaksi > 0 ? data.total_omzet / data.total_transaksi : 0) }}</h3>
          <small class="text-purple">AOV (Average Order Value)</small>
        </div>
      </div>

      <div class="mt-4 table-container">
        <h5>Rincian Transaksi Terbaru</h5>
        <table class="data-table">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Customer</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in data.orders.slice(0, 10)" :key="order.id">
              <td><strong>{{ order.invoice_number }}</strong></td>
              <td>{{ order.customer_name || 'Pelanggan' }}</td>
              <td>{{ new Date(order.created_at).toLocaleDateString() }}</td>
              <td><span class="badge" :class="order.status">{{ order.status.toUpperCase() }}</span></td>
              <td class="text-right">{{ formatCurrency(order.total) }}</td>
            </tr>
            <tr v-if="data.orders.length === 0">
              <td colspan="5" class="text-center text-muted">Belum ada transaksi di periode ini.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import client from '@/api/client';
import { useToast } from '@/composables/useToast';

const props = defineProps(['filter']);
const toast = useToast();
const loading = ref(true);
const data = ref({ orders: [] });

const loadData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/advanced/sales?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Sales Report');
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
const exportData = (type) => { 
  const token = localStorage.getItem('token');
  const baseUrl = import.meta.env.VITE_API_BASE_URL || '/api';
  // Ensure we don't end up with /api/api/
  const cleanBaseUrl = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;
  const url = `${cleanBaseUrl}/reports/advanced/export-sales/${type}?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}&token=${token}`;
  window.open(url, '_blank');
};

watch(() => props.filter, loadData, { deep: true });
onMounted(loadData);
</script>

<style scoped>
/* ULTRA COMPACT UI FOR MOBILE APP */
.report-panel { background: white; border-radius: 12px; padding: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); overflow: hidden; }
.panel-header { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
.panel-header h4 { font-weight: 800; font-size: 0.85rem; color: #111; margin: 0; }
.actions { display: flex; gap: 5px; }
.btn-export { flex: 1; padding: 5px 10px; border-radius: 8px; font-weight: 700; font-size: 0.65rem; border: none; cursor: pointer; text-align: center; }
.btn-export.pdf { background: #fee2e2; color: #ef4444; }
.btn-export.excel { background: #dcfce7; color: #22c55e; }

/* Horizontal scrolling cards to prevent squashing */
.summary-cards { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 5px; scrollbar-width: none; -ms-overflow-style: none; }
.summary-cards::-webkit-scrollbar { display: none; }

.card { flex: 0 0 120px; padding: 10px; border-radius: 12px; background: #f8fafc; border: 1px solid #f1f5f9; }
.card.primary { background: #111; color: white; border: none; }
.card span { font-size: 0.55rem; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card.primary span { color: #94a3b8; }
.card h3 { font-size: 0.85rem; font-weight: 900; margin: 2px 0; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card.primary h3 { color: white; }
.card small { font-size: 0.55rem; display: block; line-height: 1.1; margin-top: 2px; }
.card small.green { color: #10b981; font-weight: 600; }
.card small.text-muted { color: #94a3b8; }
.card small.text-purple { color: #a78bfa; font-weight: 600; }

.mt-4 { margin-top: 15px; }
h5 { margin-bottom: 8px; font-weight: 800; color: #334155; font-size: 0.75rem; }
.table-container { border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto; background: white; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.65rem; table-layout: fixed; }
.data-table th { background: #f8fafc; text-align: left; padding: 6px 8px; font-size: 0.55rem; text-transform: uppercase; color: #475569; border-bottom: 1px solid #cbd5e1; }
.data-table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.badge { padding: 1px 4px; border-radius: 3px; font-size: 0.55rem; font-weight: 800; display: inline-block; }
.badge.paid { background: #dcfce7; color: #166534; }
.badge.shipped { background: #fdf4ff; color: #86198f; }
.badge.completed { background: #dbeafe; color: #1e40af; }

.loader { padding: 15px; font-size: 0.7rem; text-align: center; color: #94a3b8; }
</style>
