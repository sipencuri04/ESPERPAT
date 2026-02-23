<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Laporan Laba Rugi</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="summary-cards">
        <div class="card">
          <span>Total Penjualan</span>
          <h3>{{ formatCurrency(data.total_penjualan) }}</h3>
          <small class="green">Pendapatan Kotor</small>
        </div>
        <div class="card">
          <span>Harga Pokok (HPP)</span>
          <h3>- {{ formatCurrency(data.hpp) }}</h3>
          <small class="red">Modal Barang</small>
        </div>
        <div class="card primary">
          <span>Laba Kotor</span>
          <h3>{{ formatCurrency(data.laba_kotor) }}</h3>
        </div>
      </div>

      <div class="financial-table-box mt-4">
        <table class="fin-table">
          <tbody>
            <tr class="section"><td colspan="2">PENDAPATAN</td></tr>
            <tr><td>Total Penjualan (Paid & Completed)</td><td class="text-right">{{ formatCurrency(data.total_penjualan) }}</td></tr>
            <tr class="section"><td colspan="2">BEBAN POKOK</td></tr>
            <tr><td>Harga Pokok Penjualan (HPP)</td><td class="text-right text-red">- {{ formatCurrency(data.hpp) }}</td></tr>
            <tr class="subtotal"><td>LABA KOTOR</td><td class="text-right">{{ formatCurrency(data.laba_kotor) }}</td></tr>
            
            <tr class="section"><td colspan="2">BIAYA OPERASIONAL</td></tr>
            <tr v-if="!data.expenses_detail || data.expenses_detail.length === 0">
              <td colspan="2" class="text-center text-muted">Belum ada pengeluaran operasional.</td>
            </tr>
            <tr v-else v-for="(exp, i) in data.expenses_detail" :key="i">
              <td>{{ exp.description }} <span class="badge">{{ exp.category }}</span></td>
              <td class="text-right">- {{ formatCurrency(exp.amount) }}</td>
            </tr>
            <tr class="subtotal"><td>Total Biaya Operasional</td><td class="text-right text-red">- {{ formatCurrency(data.biaya_operasional) }}</td></tr>
            
            <tr class="grandtotal"><td>LABA BERSIH (NET PROFIT)</td><td class="text-right">{{ formatCurrency(data.laba_bersih) }}</td></tr>
          </tbody>
        </table>
        <div class="margin-box">Margin Keuntungan Bersih: <strong>{{ data.margin_percent }}%</strong></div>
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
const data = ref({});

const loadData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/advanced/laba-rugi?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Laba Rugi');
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);

const exportData = async (type) => {
  // Mock Export logging
  toast.success(`Exporting ${type.toUpperCase()}... Segera siap!`);
};

watch(() => props.filter, loadData, { deep: true });
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

.summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.card { padding: 20px; border-radius: 20px; background: #f8fafc; border: 1px solid #f1f5f9; }
.card.primary { background: #f5f3ff; border-color: #ede9fe; }
.card span { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
.card h3 { font-size: 1.5rem; font-weight: 900; margin: 5px 0; color: #0f172a; }
.card.primary h3 { color: #6d28d9; }
.card small.green { color: #10b981; font-weight: 600; font-size: 0.75rem;}
.card small.red { color: #ef4444; font-weight: 600; font-size: 0.75rem;}

.mt-4 { margin-top: 25px; }
.financial-table-box { border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.fin-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.fin-table td { padding: 12px 20px; border-bottom: 1px solid #f1f5f9; }
.fin-table tr.section td { background: #f8fafc; font-weight: 800; font-size: 0.75rem; color: #475569; padding: 8px 20px; }
.fin-table tr.subtotal td { font-weight: 800; background: #fff; border-top: 1px dashed #cbd5e1; }
.fin-table tr.grandtotal td { font-weight: 900; background: #111; color: white; font-size: 1rem; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.text-muted { color: #94a3b8; }
.text-red { color: #ef4444; }
.badge { background: #e2e8f0; font-size: 0.65rem; padding: 3px 8px; border-radius: 8px; color: #475569; margin-left: 10px; text-transform: uppercase; }

.margin-box { padding: 15px 20px; background: #fdf4ff; color: #a21caf; font-size: 0.9rem; border-top: 1px solid #fae8ff; display: flex; justify-content: space-between; }
.loader { padding: 40px; text-align: center; color: #94a3b8; font-weight: 600; }
</style>
