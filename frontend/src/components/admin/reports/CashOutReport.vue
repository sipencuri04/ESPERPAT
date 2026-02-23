<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Kas Keluar (Restok Barang)</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="summary-cards">
        <div class="card danger">
          <span>Total Kas Keluar</span>
          <h3>{{ formatCurrency(data.total_amount) }}</h3>
          <small class="text-red">Total Dana Pembelian Barang</small>
        </div>
        <div class="card">
          <span>Jumlah Aktivitas</span>
          <h3>{{ data.count }} Kali</h3>
          <small class="text-muted">Aktivitas Restok Periode Ini</small>
        </div>
      </div>

      <div class="mt-4 table-container">
        <h5>Riwayat Kas Keluar Restok</h5>
        <table class="data-table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Qty</th>
              <th>H.Lama</th>
              <th>H.Baru</th>
              <th>Selisih</th>
              <th>Tanggal</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="exp in data.expenses" :key="exp.id">
              <td><strong>{{ exp.product_name }}</strong></td>
              <td>{{ exp.qty }}</td>
              <td>{{ formatCurrency(exp.old_price).replace('Rp', '').trim() }}</td>
              <td>{{ formatCurrency(exp.new_price).replace('Rp', '').trim() }}</td>
              <td>
                 <span :class="{'text-red': exp.diff.includes('+'), 'text-green': exp.diff.includes('-')}">
                    {{ exp.diff }}
                 </span>
              </td>
              <td>{{ new Date(exp.date).toLocaleDateString('id-ID') }}</td>
              <td class="text-right text-red">{{ formatCurrency(exp.amount) }}</td>
            </tr>
            <tr v-if="data.expenses.length === 0">
              <td colspan="3" class="text-center text-muted">Belum ada kas keluar (restok) di periode ini.</td>
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
const data = ref({ expenses: [], total_amount: 0, count: 0 });

const loadData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/advanced/cash-out?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Laporan Kas Keluar');
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);

const exportData = (type) => { 
  const token = localStorage.getItem('token');
  const baseUrl = import.meta.env.VITE_API_BASE_URL || '/api';
  const cleanBaseUrl = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;
  const url = `${cleanBaseUrl}/reports/advanced/export-cash-out/${type}?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}&token=${token}`;
  window.open(url, '_blank');
};

watch(() => props.filter, loadData, { deep: true });
onMounted(loadData);
</script>

<style scoped>
.report-panel { background: white; border-radius: 12px; padding: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); overflow: hidden; }
.panel-header { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
.panel-header h4 { font-weight: 800; font-size: 0.85rem; color: #111; margin: 0; }
.actions { display: flex; gap: 5px; }
.btn-export { flex: 1; padding: 5px 10px; border-radius: 8px; font-weight: 700; font-size: 0.65rem; border: none; cursor: pointer; text-align: center; }
.btn-export.pdf { background: #fee2e2; color: #ef4444; }
.btn-export.excel { background: #dcfce7; color: #22c55e; }

.summary-cards { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 5px; scrollbar-width: none; -ms-overflow-style: none; }
.summary-cards::-webkit-scrollbar { display: none; }

.card { flex: 0 0 140px; padding: 10px; border-radius: 12px; background: #f8fafc; border: 1px solid #f1f5f9; }
.card.danger { border-left: 4px solid #ef4444; }
.card span { font-size: 0.55rem; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card h3 { font-size: 0.85rem; font-weight: 900; margin: 2px 0; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card small { font-size: 0.55rem; display: block; line-height: 1.1; margin-top: 2px; }
.text-red { color: #ef4444; font-weight: 700; }
.text-green { color: #10b981; font-weight: 700; }
.text-muted { color: #94a3b8; }

.mt-4 { margin-top: 15px; }
h5 { margin-bottom: 8px; font-weight: 800; color: #334155; font-size: 0.75rem; }
.table-container { border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto; background: white; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.6rem; table-layout: auto; }
.data-table th { background: #f8fafc; text-align: left; padding: 6px 4px; font-size: 0.55rem; text-transform: uppercase; color: #475569; border-bottom: 1px solid #cbd5e1; white-space: nowrap; }
.data-table td { padding: 6px 4px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.loader { padding: 15px; font-size: 0.7rem; text-align: center; color: #94a3b8; }
</style>
