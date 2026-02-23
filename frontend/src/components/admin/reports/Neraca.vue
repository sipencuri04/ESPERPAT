<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Neraca (Balance Sheet)</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="validation-alert" :class="data.is_balanced ? 'success' : 'error'">
        <strong>{{ data.is_balanced ? 'VALIDASI: BALANCE' : 'VALIDASI: TIDAK BALANCE (Check Input)' }}</strong>
        <p v-if="data.is_balanced">Aset sama dengan Total Hutang + Modal. Posisi keuangan anda tercatat akurat.</p>
        <p v-else>Terdapat ketidaksesuaian/selisih aset dengan ekuitas. Mohon periksa jurnal jurnal.</p>
      </div>

      <div class="balance-grid">
        <!-- ASET -->
        <div class="bs-col">
          <div class="bs-head">ASET (AKTIVA)</div>
          <table class="fin-table">
            <tbody>
              <tr><td>Kas & Bank</td><td class="text-right">{{ formatCurrency(data.aset?.kas) }}</td></tr>
              <tr><td>Piutang Usaha</td><td class="text-right">{{ formatCurrency(data.aset?.piutang) }}</td></tr>
              <tr><td>Inventory (Stok Barang)</td><td class="text-right">{{ formatCurrency(data.aset?.inventory) }}</td></tr>
              <tr><td>Aset Tetap (Peralatan)</td><td class="text-right">{{ formatCurrency(data.aset?.aset_tetap) }}</td></tr>
              <tr class="grandtotal primary"><td>TOTAL ASET</td><td class="text-right">{{ formatCurrency(data.aset?.total) }}</td></tr>
            </tbody>
          </table>
        </div>

        <!-- HUTANG & MODAL -->
        <div class="bs-col">
          <div class="bs-head red">LIABILITAS & EKUITAS (PASIVA)</div>
          <table class="fin-table">
            <tbody>
              <tr class="section"><td colspan="2">Hutang (Kewajiban)</td></tr>
              <tr><td>Hutang Supplier</td><td class="text-right">{{ formatCurrency(data.hutang?.supplier) }}</td></tr>
              <tr><td>Hutang Lain-lain</td><td class="text-right">{{ formatCurrency(data.hutang?.lain) }}</td></tr>
              <tr class="subtotal"><td class="text-xs">Subtotal Hutang</td><td class="text-right">{{ formatCurrency(data.hutang?.total) }}</td></tr>

              <tr class="section mt-2"><td colspan="2">Modal (Ekuitas)</td></tr>
              <tr><td>Modal Awal (Disetor)</td><td class="text-right">{{ formatCurrency(data.modal?.awal) }}</td></tr>
              <tr><td>Laba Ditahan</td><td class="text-right">{{ formatCurrency(data.modal?.laba_ditahan) }}</td></tr>
              <tr class="subtotal"><td class="text-xs">Subtotal Modal</td><td class="text-right">{{ formatCurrency(data.modal?.total) }}</td></tr>

              <tr class="grandtotal red-bg"><td>TOTAL LIABILITAS + EKUITAS</td><td class="text-right">{{ formatCurrency(data.hutang?.total + data.modal?.total) }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import client from '@/api/client';
import { useToast } from '@/composables/useToast';

const props = defineProps(['filter']); // although Neraca is usually a snapshot of current date, user might want past. For now use the same request.
const toast = useToast();
const loading = ref(true);
const data = ref({});

const loadData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/advanced/neraca`); // Balance sheet is till date
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Neraca');
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

.validation-alert { padding: 15px 20px; border-radius: 16px; margin-bottom: 20px; font-size: 0.9rem; border-left: 6px solid #111; }
.validation-alert.success { background: #f0fdf4; border-color: #10b981; color: #047857; }
.validation-alert.error { background: #fef2f2; border-color: #ef4444; color: #b91c1c; }
.validation-alert strong { display: block; margin-bottom: 5px; font-weight: 900; }

.balance-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.bs-col { border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
.bs-head { background: #f1f5f9; padding: 15px 20px; font-weight: 900; font-size: 1rem; color: #334155; text-align: center; border-bottom: 2px solid #cbd5e1; }
.bs-head.red { background: #fff1f2; color: #be123c; border-bottom: 2px solid #fda4af; }

.fin-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
.fin-table td { padding: 12px 20px; border-bottom: 1px dashed #f1f5f9; }
.fin-table tr.section td { background: #f8fafc; font-weight: 800; font-size: 0.75rem; color: #475569; padding: 8px 20px; }
.fin-table tr.subtotal td { font-weight: 800; background: #fff; border-top: 1px solid #cbd5e1; font-size: 0.8rem; }
.fin-table tr.grandtotal td { font-weight: 900; background: #111; color: white; font-size: 1rem; padding: 20px; }
.fin-table tr.grandtotal.primary td { background: #4c1d95; }
.fin-table tr.grandtotal.red-bg td { background: #9f1239; }

.text-right { text-align: right; }
.text-xs { font-size: 0.75rem; color: #64748b; }

.loader { padding: 40px; text-align: center; color: #94a3b8; font-weight: 600; }
</style>
