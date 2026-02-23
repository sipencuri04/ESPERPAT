<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Aging Piutang</h4>
      <div class="actions">
        <button class="btn-export pdf" @click="exportData('pdf')">Export PDF</button>
        <button class="btn-export excel" @click="exportData('excel')">Export Excel</button>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Memuat data...</div>
    <div v-else class="panel-content">
      <div class="filter-search mb-4">
        <input type="text" placeholder="Cari by nama customer..." v-model="search" class="search-input" />
      </div>

      <div class="aging-box">
        <table class="data-table">
          <thead>
            <tr>
              <th>Customer</th>
              <th class="text-right">0 - 30 Hari</th>
              <th class="text-right">31 - 60 Hari</th>
              <th class="text-right">61 - 90 Hari</th>
              <th class="text-right text-red">> 90 Hari</th>
              <th class="text-right bg-gray">Total Piutang</th>
            </tr>
          </thead>
          <tbody>
            <!-- We display raw records clustered by aging directly or grouped by customer.
                 The raw data array contains objects like {customer_name, amount, days_late}.
                 For simplicity in this tabular view, we list each invoice row in appropriate column. -->
             <tr v-for="item in filteredData" :key="item.id">
                <td><strong>{{ item.customer_name }}</strong></td>
                
                <!-- 0-30 -->
                <td class="text-right"><span v-if="item.days_late >= 0 && item.days_late <= 30">{{ formatCurrency(item.amount) }}<br><small class="text-muted">{{ item.days_late }} hari</small></span></td>
                
                <!-- 31-60 -->
                <td class="text-right"><span v-if="item.days_late > 30 && item.days_late <= 60">{{ formatCurrency(item.amount) }}<br><small class="text-muted">{{ item.days_late }} hari</small></span></td>
                
                <!-- 61-90 -->
                <td class="text-right"><span v-if="item.days_late > 60 && item.days_late <= 90">{{ formatCurrency(item.amount) }}<br><small class="text-muted">{{ item.days_late }} hari</small></span></td>
                
                <!-- >90 -->
                <td class="text-right text-red font-bold"><span v-if="item.days_late > 90">{{ formatCurrency(item.amount) }}<br><small>{{ item.days_late }} hari</small></span></td>

                <td class="text-right bg-gray font-bold">{{ formatCurrency(item.amount) }}</td>
             </tr>
             
             <tr v-if="filteredData.length === 0">
                <td colspan="6" class="text-center text-muted py-4">Tidak ada piutang aktif yang sesuai pencarian.</td>
             </tr>

             <tr class="grandtotal bg-purple" v-if="filteredData.length > 0">
                <td><strong>GRAND TOTAL</strong></td>
                <td class="text-right">{{ formatCurrency(totalAg(0, 30)) }}</td>
                <td class="text-right">{{ formatCurrency(totalAg(31, 60)) }}</td>
                <td class="text-right">{{ formatCurrency(totalAg(61, 90)) }}</td>
                <td class="text-right text-red">{{ formatCurrency(totalAg(91, 9999)) }}</td>
                <td class="text-right bg-dark">{{ formatCurrency(filteredData.reduce((acc, c) => acc + Number(c.amount), 0)) }}</td>
             </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import client from '@/api/client';
import { useToast } from '@/composables/useToast';

const props = defineProps(['filter']);
const toast = useToast();
const loading = ref(true);
const data = ref({ raw: [] });
const search = ref('');

const loadData = async () => {
  loading.value = true;
  try {
    const res = await client.get(`reports/advanced/aging-piutang`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Piutang');
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);

const filteredData = computed(() => {
    return (data.value.raw || []).filter(item => item.customer_name.toLowerCase().includes(search.value.toLowerCase()));
});

const totalAg = (min, max) => {
    return filteredData.value.filter(item => item.days_late >= min && item.days_late <= max).reduce((acc, c) => acc + Number(c.amount), 0);
};

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

.search-input { width: 100%; max-width: 400px; padding: 12px 20px; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 700; font-size: 0.85rem; outline: none;}
.mb-4 { margin-bottom: 20px; }
.py-4 { padding: 20px !important; }

.aging-box { border: 1px solid #e2e8f0; border-radius: 16px; overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.data-table th { background: #f8fafc; text-align: left; padding: 12px 20px; font-size: 0.75rem; text-transform: uppercase; color: #475569; border-bottom: 2px solid #cbd5e1; }
.data-table td { padding: 12px 20px; border-bottom: 1px dashed #f1f5f9; color: #334155; }
.text-right { text-align: right; }
.text-center { text-align: center; }

.text-red { color: #ef4444 !important; }
.text-muted { color: #94a3b8; font-size: 0.7rem;}
.font-bold { font-weight: 800; }
.bg-gray { background: #f8fafc; font-weight: 800;}
.bg-purple { background: #f5f3ff; color: #4c1d95; font-size: 0.95rem; }
.bg-purple td { border-bottom: none;}
.bg-dark { background: #4c1d95; color: white; }

.loader { padding: 40px; text-align: center; color: #94a3b8; font-weight: 600; }
</style>
