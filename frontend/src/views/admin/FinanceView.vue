<template>
  <div class="admin-finance">
    <div class="container mobile-frame">
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Keuangan Profesional</span>
        <div class="head-actions">
           <button class="icon-btn" @click="isFilterOpen = !isFilterOpen">
             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
           </button>
        </div>
      </header>

      <!-- Advanced Filters (Expandable) -->
      <Transition name="slide-down">
        <div v-if="isFilterOpen" class="filter-drawer animate-fade-up">
          <div class="filter-grid">
            <div class="f-group">
              <label>Bulan</label>
              <select v-model="filter.month">
                <option v-for="(m, i) in months" :key="i" :value="i+1">{{ m }}</option>
              </select>
            </div>
            <div class="f-group">
              <label>Tahun</label>
              <select v-model="filter.year">
                <option v-for="y in [2024, 2025, 2026]" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>
            <div class="f-group">
              <label>Status</label>
              <select v-model="filter.status">
                <option value="all">Semua (Sukses)</option>
                <option value="paid">Lunas (Paid)</option>
                <option value="shipped">Dikirim</option>
                <option value="completed">Selesai</option>
              </select>
            </div>
          </div>
          <div class="export-actions">
             <button class="btn-export pdf" @click="handleExport('pdf')">Export PDF</button>
             <button class="btn-export excel" @click="handleExport('excel')">Export Excel</button>
          </div>
        </div>
      </Transition>

      <!-- Loading State -->
      <div v-if="loading" class="loader-container">
        <div class="spinner"></div>
        <p>Menganalisa data keuangan...</p>
      </div>

      <template v-else>
      
      <!-- Tabs Navigation -->
      <div class="finance-tabs animate-fade-up">
        <button class="tab-btn" :class="{ active: currentTab === 'summary' }" @click="currentTab = 'summary'">Ringkasan</button>
        <button class="tab-btn" :class="{ active: currentTab === 'details' }" @click="currentTab = 'details'">Riwayat Order</button>
      </div>

      <div v-show="currentTab === 'summary'">
        <!-- Main Balance & Target -->
        <div class="balance-card-v2 animate-fade-up">
        <div class="main-stats">
          <span class="lbl">Total Laba Bersih</span>
          <div class="amount-row">
            <h2 class="amount">{{ formatCurrency(financeData.total_profit || 0) }}</h2>
            <div v-if="growthData.growth_percentage" :class="['growth-badge', growthData.status]">
              {{ growthData.status === 'up' ? '↑' : '↓' }} {{ Math.abs(growthData.growth_percentage) }}%
            </div>
          </div>
        </div>

        <!-- Target Progress -->
        <div class="target-section">
           <div class="target-head">
              <span class="t-lbl">Target Bulanan ({{ formatCurrency(growthData.target) }})</span>
              <span class="t-pct">{{ growthData.achievement_percentage }}%</span>
           </div>
           <div class="progress-track">
              <div class="progress-fill" :style="{ width: Math.min(100, growthData.achievement_percentage) + '%' }"></div>
           </div>
        </div>

        <div class="bal-footer">
          <div class="f-item">
            <span class="f-lbl">Bulan Ini</span>
            <span class="f-val">{{ financeData.period || '-' }}</span>
          </div>
          <div class="f-item">
            <span class="f-lbl">Profit Margin</span>
            <span class="f-val">{{ financeData.margin || 0 }}%</span>
          </div>
        </div>
      </div>

      <!-- Charts Section (Grid) -->
      <div class="charts-container animate-fade-up" style="animation-delay: 0.1s">
         <div class="chart-card dark">
            <h4>Penjualan Harian</h4>
            <div class="chart-box">
               <canvas id="dailyChart"></canvas>
            </div>
         </div>
         <div class="chart-card">
            <h4>Trend Laba 6 Bulan</h4>
            <div class="chart-box">
               <canvas id="monthlyChart"></canvas>
            </div>
         </div>
      </div>

      <!-- Summary Row -->
      <div class="summary-grid animate-fade-up" style="animation-delay: 0.15s">
        <div class="summ-box">
          <div class="icon-up">↑</div>
          <div class="info">
            <span class="label">Pemasukan</span>
            <strong class="val">{{ formatCurrency(financeData.total_sales || 0) }}</strong>
          </div>
        </div>
        <div class="summ-box">
          <div class="icon-down">↓</div>
          <div class="info">
            <span class="label">Modal (HPP)</span>
            <strong class="val">{{ formatCurrency((financeData.total_sales || 0) - (financeData.total_profit || 0)) }}</strong>
          </div>
        </div>
        <div class="summ-box full-width">
          <div class="icon-box-orders">🛒</div>
          <div class="info">
            <span class="label">Total Order Sukses</span>
            <strong class="val">{{ financeData.orders ? financeData.orders.length : 0 }} Pesanan</strong>
          </div>
        </div>
        </div>
      </div> <!-- End Summary Tab -->

      <div v-show="currentTab === 'details'">
      <!-- Detail Transactions with Expandable -->
      <div class="transactions-section animate-fade-up" style="animation-delay: 0.2s">
        <div class="section-header">
          <h4>Rincian Laba Per Order</h4>
        </div>
        
        <div class="tx-list" v-if="filteredOrders.length > 0">
          <div v-for="(item, i) in filteredOrders" :key="i" class="tx-card-v2" @click="toggleRow(i)">
            <div class="tx-main">
              <div class="tx-info">
                <span class="tx-inv">{{ item.order.invoice_number }} <span class="tx-cust">— {{ item.order.customer_name || 'Guest' }}</span></span>
                <div class="tx-meta">
                  <span class="tx-date">{{ formatDate(item.order.created_at) }}</span>
                  <span v-if="item.order.nomor_resi" class="tx-resi"> • Resi: {{ item.order.nomor_resi }}</span>
                </div>
              </div>
              <div class="tx-amount">
                <span class="tx-val">+ {{ formatCurrency(item.order_profit) }}</span>
                <span class="tx-status" :class="item.order.status">{{ item.order.status }}</span>
              </div>
              <div class="tx-toggle" :class="{ rotated: expandedRows.includes(i) }">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
              </div>
            </div>

            <!-- Expandable Details -->
            <Transition name="expand">
              <div v-if="expandedRows.includes(i)" class="tx-details">
                 <div class="d-row">
                    <span>Harga Jual</span>
                    <span>{{ formatCurrency(item.order.total) }}</span>
                 </div>
                 <div class="d-row">
                    <span>Estimasi Modal (HPP)</span>
                    <span>- {{ formatCurrency(item.order.total - item.order_profit) }}</span>
                 </div>
                 <div v-for="subitem in item.items" :key="subitem.id" class="d-item-row">
                    <span>{{ subitem.qty }}x {{ subitem.product_name }}</span>
                    <span>{{ formatCurrency(subitem.laba) }} (Laba)</span>
                 </div>
                 <div class="d-total">
                    <span>Laba Bersih Final</span>
                    <strong>{{ formatCurrency(item.order_profit) }}</strong>
                 </div>
              </div>
            </Transition>
          </div>
          
          <div class="tx-summary-row">
             <span>Total Laba Tertampil:</span>
             <strong>{{ formatCurrency(filteredOrders.reduce((sum, item) => sum + item.order_profit, 0)) }}</strong>
          </div>
        </div>
        
        <div v-else class="empty-state">
           <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
           <p>Belum ada transaksi sukses di periode/status ini</p>
        </div>
        </div>
      </div> <!-- End Details Tab -->

      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import client from '../../api/client';
import { useToast } from '../../composables/useToast';
const toast = useToast();
import Chart from 'chart.js/auto';

const financeData = ref({});
const growthData = ref({});
const dailyData = ref([]);
const monthlyData = ref([]);
const loading = ref(true);
const isFilterOpen = ref(false);
const expandedRows = ref([]);
const currentTab = ref('summary');

const filter = ref({
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear(),
  status: 'all'
});

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

let dailyChart = null;
let monthlyChart = null;

const fetchAll = async () => {
    loading.value = true;
    try {
        const [prof, growth, daily, monthly] = await Promise.all([
            client.get(`reports/profit?month=${filter.value.month}&year=${filter.value.year}`),
            client.get(`finance/growth?month=${filter.value.month}&year=${filter.value.year}`),
            client.get(`finance/chart/daily?month=${filter.value.month}&year=${filter.value.year}`),
            client.get(`finance/chart/monthly`)
        ]);
        
        financeData.value = prof.data.data;
        growthData.value = growth.data.data;
        dailyData.value = daily.data.data;
        monthlyData.value = monthly.data.data;

        renderCharts();
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const renderCharts = () => {
   if (dailyChart) dailyChart.destroy();
   if (monthlyChart) monthlyChart.destroy();

   const ctxDaily = document.getElementById('dailyChart');
   if (ctxDaily) {
      dailyChart = new Chart(ctxDaily, {
         type: 'line',
         data: {
            labels: dailyData.value.map(d => d.day),
            datasets: [{
               label: 'Sales',
               data: dailyData.value.map(d => d.sales),
               borderColor: '#8b5cf6',
               backgroundColor: 'rgba(139,92,246,0.1)',
               fill: true,
               tension: 0.4,
               pointRadius: 0
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
               y: { display: false },
               x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            }
         }
      });
   }

   const ctxMonthly = document.getElementById('monthlyChart');
   if (ctxMonthly) {
      monthlyChart = new Chart(ctxMonthly, {
         type: 'bar',
         data: {
            labels: monthlyData.value.map(d => d.label),
            datasets: [{
               label: 'Profit',
               data: monthlyData.value.map(d => d.profit),
               backgroundColor: '#111',
               borderRadius: 8
            }]
         },
         options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
               y: { display: false },
               x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            }
         }
      });
   }
};

const filteredOrders = computed(() => {
   if (!financeData.value.orders) return [];
   if (filter.value.status === 'all') return financeData.value.orders;
   return financeData.value.orders.filter(it => it.order.status === filter.value.status);
});

const toggleRow = (idx) => {
   if (expandedRows.value.includes(idx)) {
      expandedRows.value = expandedRows.value.filter(i => i !== idx);
   } else {
      expandedRows.value.push(idx);
   }
};

const handleExport = async (type) => {
   try {
      const response = await client.get(`finance/export/${type}`, {
         params: { 
            month: filter.value.month, 
            year: filter.value.year 
         },
         responseType: 'blob' // Important for file downloads
      });

      // Create a temporary link to trigger download
      const blob = new Blob([response.data], { 
         type: type === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
      });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `Laporan_Keuangan_${filter.value.month}_${filter.value.year}.${type === 'pdf' ? 'pdf' : 'xlsx'}`);
      document.body.appendChild(link);
      link.click();
      
      // Cleanup
      link.remove();
      window.URL.revokeObjectURL(url);
   } catch (err) {
      console.error('Export failed:', err);
      toast.error('Gagal mengunduh laporan. Pastikan anda masih login.');
   }
};

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

const formatDate = (dateStr) => {
   const d = new Date(dateStr);
   return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

watch(() => [filter.value.month, filter.value.year], () => {
   fetchAll();
});

onMounted(fetchAll);
</script>

<style scoped>
.admin-finance { min-height: 100vh; background: #fdfdfd; padding-bottom: 50px; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; position: sticky; top: 0; background: #fdfdfd; z-index: 10; }
.title { font-weight: 800; font-size: 1.1rem; }
.back-btn, .icon-btn { background: none; border: none; cursor: pointer; color: #111; }

.filter-drawer { padding: 0 1.5rem 20px; border-bottom: 1px solid #f1f1f1; margin-bottom: 20px; }
.filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 15px; }
.f-group label { display: block; font-size: 0.65rem; font-weight: 800; color: #bbb; text-transform: uppercase; margin-bottom: 5px; }
.f-group select { width: 100%; height: 40px; border-radius: 12px; border: 1px solid #eee; padding: 0 10px; font-weight: 700; font-size: 0.8rem; background: #f9f9f9; outline: none; }

.export-actions { display: flex; gap: 10px; }
.btn-export { flex: 1; height: 44px; border-radius: 12px; border: none; font-weight: 800; font-size: 0.8rem; cursor: pointer; }
.btn-export.pdf { background: #fee2e2; color: #ef4444; }
.btn-export.excel { background: #dcfce7; color: #22c55e; }

/* Tabs */
.finance-tabs { display: flex; gap: 10px; padding: 0 1.5rem; margin-bottom: 20px; }
.tab-btn { flex: 1; height: 44px; border-radius: 14px; background: #f1f5f9; border: 1px solid #e2e8f0; color: #64748b; font-weight: 800; font-size: 0.85rem; cursor: pointer; transition: 0.3s; }
.tab-btn.active { background: #8b5cf6; color: white; border-color: #8b5cf6; box-shadow: 0 5px 15px rgba(139,92,246,0.3); }

/* Balance V2 */
.balance-card-v2 { 
  margin: 0 1.5rem 25px; 
  background: white; 
  border-radius: 32px; 
  padding: 25px; 
  border: 1px solid #f1f1f1;
  box-shadow: 0 20px 40px rgba(0,0,0,0.03);
}
.amount-row { display: flex; align-items: baseline; gap: 12px; margin-bottom: 25px; }
.balance-card-v2 .amount { font-size: 1.6rem; font-weight: 900; color: #111; }
.growth-badge { font-size: 0.7rem; font-weight: 900; padding: 4px 10px; border-radius: 30px; }
.growth-badge.up { background: #f0fdf4; color: #22c55e; }
.growth-badge.down { background: #fef2f2; color: #ef4444; }

/* Target Section */
.target-section { margin-bottom: 25px; }
.target-head { display: flex; justify-content: space-between; margin-bottom: 8px; }
.t-lbl { font-size: 0.65rem; font-weight: 800; color: #999; text-transform: uppercase; }
.t-pct { font-size: 0.7rem; font-weight: 900; color: #8b5cf6; }
.progress-track { width: 100%; height: 8px; background: #f1f1f1; border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; background: #8b5cf6; border-radius: 4px; transition: width 0.5s ease; }

.bal-footer { display: flex; gap: 30px; border-top: 1px solid #f9f9f9; padding-top: 15px; }
.f-item .f-lbl { display: block; font-size: 0.6rem; color: #bbb; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
.f-item .f-val { font-size: 0.85rem; font-weight: 800; color: #111; }

/* Charts */
.charts-container { display: flex; flex-direction: column; gap: 15px; padding: 0 1.5rem; margin-bottom: 25px; }
.chart-card { background: white; border-radius: 24px; padding: 18px; border: 1px solid #f1f1f1; }
.chart-card.dark { background: #111; color: white; border: none; }
.chart-card h4 { font-size: 0.85rem; font-weight: 800; margin-bottom: 15px; }
.chart-box { height: 120px; position: relative; }

.summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0 1.5rem; margin-bottom: 30px; }
.summ-box { background: white; padding: 15px; border-radius: 20px; border: 1px solid #f1f1f1; display: flex; align-items: center; gap: 10px; }
.summ-box.full-width { grid-column: span 2; justify-content: center; }
.icon-up { width: 28px; height: 28px; background: #f0fdf4; color: #22c55e; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 900; }
.icon-down { width: 28px; height: 28px; background: #fef2f2; color: #ef4444; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 900; }
.icon-box-orders { width: 28px; height: 28px; background: #f5f3ff; color: #8b5cf6; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
.summ-box .label { display: block; font-size: 0.6rem; color: #999; font-weight: 700; text-transform: uppercase; }
.summ-box .val { font-size: 0.8rem; font-weight: 800; color: #111; }

/* Transaction Cards V2 */
.transactions-section { padding: 0 1.5rem; }
.section-header h4 { font-weight: 800; color: #111; margin-bottom: 15px; }
.tx-list { display: flex; flex-direction: column; gap: 10px; }
.tx-card-v2 { background: white; border-radius: 20px; border: 1px solid #f3f3f3; overflow: hidden; cursor: pointer; transition: 0.2s; }
.tx-main { padding: 15px; display: flex; align-items: center; gap: 12px; }
.tx-info { flex: 1; }
.tx-inv { display: block; font-size: 0.85rem; font-weight: 800; color: #111; }
.tx-cust { font-weight: 600; color: #64748b; font-size: 0.75rem; }
.tx-meta { display: flex; align-items: center; gap: 4px; }
.tx-date { font-size: 0.65rem; color: #bbb; font-weight: 600; }
.tx-resi { font-size: 0.65rem; color: #8b5cf6; font-weight: 700; }

.tx-amount { text-align: right; margin-right: 10px; }
.tx-val { display: block; font-size: 0.9rem; font-weight: 900; color: #22c55e; }
.tx-status { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; padding: 2px 6px; border-radius: 4px; }
.tx-status.paid { background: #f5f3ff; color: #8b5cf6; }
.tx-status.shipped { background: #fdf4ff; color: #a855f7; }
.tx-status.completed { background: #f0fdf4; color: #22c55e; }

.tx-toggle { transition: 0.3s; color: #ccc; }
.tx-toggle.rotated { transform: rotate(180deg); color: #8b5cf6; }

/* Expanded Details */
.tx-details { padding: 15px; background: #f9f9f9; border-top: 1px solid #f1f1f1; animation: slideDown 0.3s ease-out; }
.d-row { display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 8px; font-weight: 700; color: #666; }
.d-item-row { display: flex; justify-content: space-between; font-size: 0.7rem; color: #999; margin-left: 10px; margin-bottom: 4px; font-style: italic; }
.d-total { display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px dashed #ddd; margin-top: 10px; }
.d-total span { font-size: 0.8rem; font-weight: 800; color: #111; }
.d-total strong { font-size: 0.95rem; font-weight: 900; color: #22c55e; }

.tx-summary-row { background: #111; color: white; display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-radius: 20px; margin-top: 10px; }
.tx-summary-row span { font-size: 0.8rem; font-weight: 600; color: #9ca3af; }
.tx-summary-row strong { font-size: 1.1rem; font-weight: 900; color: #22c55e; }

.empty-state { text-align: center; padding: 40px 20px; background: white; border-radius: 24px; border: 1px dashed #e2e8f0; }
.empty-state p { margin-top: 10px; font-size: 0.8rem; color: #94a3b8; font-weight: 600; }

.loader-container { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 100px 20px; }
.spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #8b5cf6; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 15px; }
.loader-container p { font-size: 0.85rem; font-weight: 700; color: #8b5cf6; }
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Animations */
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }

.expand-enter-active { transition: max-height 0.3s ease-out, opacity 0.3s ease-out; max-height: 500px; overflow: hidden; }
.expand-leave-active { transition: max-height 0.3s ease-in, opacity 0.3s ease-in; max-height: 500px; overflow: hidden; }
.expand-enter-from, .expand-leave-to { max-height: 0; opacity: 0; }


@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
