<template>
  <div class="report-panel animate-fade-up">
    <div class="panel-header">
      <h4>Inventory Analytics</h4>
      <div class="actions">
        <div class="badge-status bg-green">Sistem Penilaian Cerdas ⚡</div>
      </div>
    </div>
    
    <div v-if="loading" class="loader">Menganalisis perputaran barang...</div>
    <div v-else class="panel-content">
      
      <div class="analytics-grid">
        <!-- Card 1 -->
        <div class="a-card safe">
          <div class="a-head">
            <span class="a-title">1. INVENTORY TURNOVER</span>
            <span class="a-icon">🔄</span>
          </div>
          <div class="a-val">{{ data.card1_turnover }}x</div>
          <div class="a-desc">Ideal > 4x / tahun. Perputaran gudang anda tergolong cepat.</div>
        </div>

        <!-- Card 2 -->
        <div class="a-card warning">
          <div class="a-head">
            <span class="a-title">2. DAYS INVENTORY OUTSTANDING</span>
            <span class="a-icon">⏳</span>
          </div>
          <div class="a-val">{{ data.card2_dio }} Hari</div>
          <div class="a-desc">Rata-rata barang menetap di gudang sebelum terjual.</div>
        </div>

        <!-- Card 3 -->
        <div class="a-card danger">
          <div class="a-head">
            <span class="a-title">3. DEAD STOCK</span>
            <span class="a-icon">☠️</span>
          </div>
          <div class="a-val">{{ data.card3_dead_stock }} SKU</div>
          <div class="a-desc">Barang tidak terjual > 90 hari. Perlu strategi promo diskon gila-gilaan.</div>
        </div>

        <!-- Card 4 -->
        <div class="a-card safe double">
          <div class="a-head">
            <span class="a-title">4. FAST VS SLOW MOVING</span>
            <span class="a-icon">📈</span>
          </div>
          <div class="a-flex">
            <div class="flex-1">
              <span class="lbl-small">FAST MOVING</span>
              <div class="val-big text-green">{{ data.card4_fast_slow?.fast }} SKU</div>
            </div>
            <div class="flex-divider">VS</div>
            <div class="flex-1 text-right">
              <span class="lbl-small">SLOW MOVING</span>
              <div class="val-big text-orange">{{ data.card4_fast_slow?.slow }} SKU</div>
            </div>
          </div>
        </div>

        <!-- Card 5 -->
        <div class="a-card primary">
          <div class="a-head">
            <span class="a-title">5. GMROI (Return On Inventory)</span>
            <span class="a-icon">💰</span>
          </div>
          <div class="a-val">{{ data.card5_gmroi }}</div>
          <div class="a-desc">Tiap Rp 1 modal menghasilkan margin kotor sebasar angka ini. Indikator ROI Toko sehat.</div>
        </div>
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
    const res = await client.get(`reports/advanced/inventory-analytics?period=${props.filter.period}&month=${props.filter.month}&year=${props.filter.year}`);
    data.value = res.data.data;
  } catch (err) {
    toast.error('Gagal mengambil data Inventory Analytics');
  } finally {
    loading.value = false;
  }
};

watch(() => props.filter, loadData, { deep: true });
onMounted(loadData);
</script>

<style scoped>
.report-panel { background: white; border-radius: 24px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
.panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;}
.panel-header h4 { font-weight: 800; font-size: 1.2rem; color: #111; }
.badge-status { padding: 6px 16px; border-radius: 20px; font-weight: 900; font-size: 0.75rem; color: #047857; background: #dcfce7;}

.analytics-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }

.a-card { padding: 25px; border-radius: 20px; border: 2px solid #e2e8f0; position: relative; overflow: hidden; }
.a-card.double { grid-column: span 2; }
.a-card.safe { border-color: #a7f3d0; background: #f0fdf4; }
.a-card.warning { border-color: #fef08a; background: #fefce8; }
.a-card.danger { border-color: #fecaca; background: #fef2f2; }
.a-card.primary { border-color: #c4b5fd; background: #f5f3ff; }

.a-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.a-title { font-size: 0.75rem; font-weight: 800; color: #475569; letter-spacing: 0.5px; }
.a-icon { font-size: 1.5rem; }

.a-val { font-size: 2.2rem; font-weight: 900; color: #0f172a; margin-bottom: 10px; }
.safe .a-val { color: #047857; }
.danger .a-val { color: #be123c; }
.warning .a-val { color: #ca8a04; }
.primary .a-val { color: #6d28d9; }

.a-desc { font-size: 0.75rem; color: #64748b; font-weight: 600; line-height: 1.4; }

.a-flex { display: flex; align-items: center; }
.flex-1 { flex: 1; }
.flex-divider { color: #cbd5e1; font-weight: 900; font-style: italic; font-size: 1.2rem; padding: 0 20px; }
.lbl-small { font-size: 0.7rem; font-weight: 900; color: #64748b; margin-bottom: 5px; display: block;}
.val-big { font-size: 1.8rem; font-weight: 900; }
.text-green { color: #059669; }
.text-orange { color: #ea580c; }
.text-right { text-align: right; }

.loader { padding: 60px; text-align: center; color: #94a3b8; font-weight: 600; font-size: 1.1rem; }
</style>
