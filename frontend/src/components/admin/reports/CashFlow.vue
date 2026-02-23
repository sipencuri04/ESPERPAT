<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Laporan Arus Kas (Cash Flow)</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="balance-top">
        <div class="left">Saldo Awal</div>
        <div class="right">{{ formatCurrency(data.saldo_awal) }}</div>
      </div>

      <div class="financial-table-box mt-3">
        <table class="fin-table">
          <tbody>
            <tr class="section"><td colspan="2">AKTIVITAS OPERASIONAL (OPERATING CASH FLOW)</td></tr>
            <tr><td>Kas Masuk dari Penjualan</td><td class="text-right text-green">+ {{ formatCurrency(data.operating_in_penjualan) }}</td></tr>
            <tr><td>Pembayaran Biaya Operasional</td><td class="text-right text-red">- {{ formatCurrency(data.operating_out_biaya) }}</td></tr>
            <tr class="subtotal"><td>Net Kas Aktivitas Operasional</td><td class="text-right">{{ formatCurrency(data.operating_in_penjualan - data.operating_out_biaya) }}</td></tr>
            
            <tr class="section"><td colspan="2">AKTIVITAS INVESTASI (INVESTING CASH FLOW)</td></tr>
            <tr><td>Pembelian Aset / Inventaris</td><td class="text-right text-red">- {{ formatCurrency(data.investing_out_aset) }}</td></tr>
            <tr class="subtotal"><td>Net Kas Aktivitas Investasi</td><td class="text-right">- {{ formatCurrency(data.investing_out_aset) }}</td></tr>

            <tr class="section"><td colspan="2">AKTIVITAS PENDANAAN (FINANCING CASH FLOW)</td></tr>
            <tr><td class="text-muted">Tidak ada transaksi pendanaan (Pinjaman/Kredit) di periode ini.</td><td class="text-right">Rp 0</td></tr>

            <tr class="grandtotal bg-purple">
                <td>TOTAL PERUBAHAN KAS</td>
                <td class="text-right">
                    <span v-if="(data.total_kas_masuk - data.total_kas_keluar) > 0">+ </span>
                    {{ formatCurrency(data.total_kas_masuk - data.total_kas_keluar) }}
                </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="balance-bottom mt-3">
        <div class="left">Saldo Akhir</div>
        <div class="right text-purple">{{ formatCurrency(data.saldo_akhir) }}</div>
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
    const res = await client.get(`reports/advanced/cash-flow?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Cash Flow');
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
const exportData = (type) => { toast.success(`Exporting ${type.toUpperCase()}...`); };

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

.balance-top, .balance-bottom { display: flex; justify-content: space-between; padding: 20px; border-radius: 16px; font-weight: 800; font-size: 1.1rem; }
.balance-top { background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; }
.balance-bottom { background: #f5f3ff; border: 1px solid #ede9fe; color: #111; font-size: 1.3rem; }
.text-purple { color: #6d28d9; }

.mt-3 { margin-top: 15px; }
.financial-table-box { border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.fin-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.fin-table td { padding: 12px 20px; border-bottom: 1px solid #f1f5f9; }
.fin-table tr.section td { background: #f8fafc; font-weight: 800; font-size: 0.75rem; color: #475569; padding: 8px 20px; }
.fin-table tr.subtotal td { font-weight: 800; background: #fff; border-top: 1px dashed #cbd5e1; }
.fin-table tr.grandtotal td { font-weight: 900; background: #111; color: white; font-size: 1rem; }
.fin-table tr.bg-purple td { background: #4c1d95; }
.text-right { text-align: right; }
.text-muted { color: #94a3b8; font-style: italic; }
.text-red { color: #ef4444; }
.text-green { color: #10b981; }

.loader { padding: 40px; text-align: center; color: #94a3b8; font-weight: 600; }
</style>
