<template>
  <div class="admin-reports">
    <div class="container mobile-frame reports-layout" :class="isMobileMenuOpen ? 'mobile-nav-mode' : 'mobile-content-mode'">
      
      <!-- SIDEBAR -->
      <aside class="reports-sidebar animate-fade-right">
        <div class="sidebar-header">
          <button @click="$router.push('/admin')" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
          </button>
          <span class="title">ERP Reports</span>
        </div>

        <nav class="report-nav">
          <div class="nav-group">
            <span class="group-title">LAPORAN DASAR</span>
            <button class="nav-btn" :class="{ active: currentTab === 'SalesReport' }" @click="selectTab('SalesReport')">Laporan Penjualan</button>
            <button class="nav-btn" :class="{ active: currentTab === 'InventoryReport' }" @click="selectTab('InventoryReport')">Stok Barang</button>
          </div>
        </nav>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="reports-content">
        <!-- Back to Menu Button on Mobile -->
        <div class="mobile-nav-back animate-fade-up">
            <button class="back-menu-btn" @click="isMobileMenuOpen = true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Kembali ke Menu Pilihan Laporan</span>
            </button>
        </div>

        <!-- Persistent Filter Bar -->
        <div class="top-filter-bar animate-fade-up">
          <div class="filter-group">
            <label>Periode Waktu</label>
            <div class="f-controls">
              <select v-model="filter.period">
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
              </select>
              <select v-model="filter.month" v-if="filter.period === 'monthly'">
                <option v-for="(m, i) in months" :key="i" :value="i+1">{{ m }}</option>
              </select>
              <select v-model="filter.year">
                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>
          </div>
          <div class="info-badge">
            <span class="dot"></span> Real-time Sync
          </div>
        </div>

        <!-- DYNAMIC REPORT COMPONENT -->
        <KeepAlive>
          <component :is="activeComponent" :filter="filter" />
        </KeepAlive>
      </main>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Import basic sub-components
import SalesReport from '@/components/admin/reports/SalesReport.vue';
import InventoryReport from '@/components/admin/reports/InventoryReport.vue';

const componentsMap = {
  SalesReport, InventoryReport
};

const currentTab = ref('SalesReport');
const activeComponent = computed(() => componentsMap[currentTab.value]);

const filter = ref({
  period: 'monthly',
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear(),
});

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const years = [2024, 2025, 2026];

// MOBILE UX LOGIC
const isMobileMenuOpen = ref(true);

const selectTab = (tabName) => {
    currentTab.value = tabName;
    isMobileMenuOpen.value = false; // Hide menu, show content layout
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<style scoped>
/* COMPACT UI FOR MOBILE APP */
.admin-reports { min-height: 100vh; background: #fdfdfd; padding-bottom: 20px; }
.reports-layout { display: flex; gap: 10px; padding: 10px; max-width: 1200px; margin: 0 auto; align-items: flex-start; }

/* SIDEBAR */
.reports-sidebar { width: 260px; background: white; border-radius: 16px; padding: 15px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.02); position: sticky; top: 10px; }
.sidebar-header { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.back-btn { background: #f1f5f9; width: 32px; height: 32px; border-radius: 10px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #475569; transition: 0.2s;}
.title { font-weight: 800; font-size: 0.95rem; color: #0f172a; }

.nav-group { margin-bottom: 15px; }
.group-title { display: block; font-size: 0.65rem; font-weight: 800; color: #94a3b8; margin-bottom: 8px; padding-left: 10px; letter-spacing: 0.5px; }
.nav-btn { width: 100%; display: block; text-align: left; padding: 10px 12px; background: transparent; border: none; border-radius: 10px; font-size: 0.8rem; font-weight: 700; color: #64748b; cursor: pointer; margin-bottom: 2px; }
.nav-btn.active { background: #8b5cf6; color: white; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2); }

/* MAIN CONTENT */
.reports-content { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 10px; }

/* MOBILE NAV BACK */
.mobile-nav-back { display: none; margin-bottom: 2px; }
.back-menu-btn { display: flex; align-items: center; gap: 8px; padding: 10px 15px; border-radius: 12px; background: #f1f5f9; border: none; color: #334155; font-weight: 800; font-size: 0.75rem; width: 100%; cursor: pointer; }

/* FILTER BAR */
.top-filter-bar { display: flex; justify-content: space-between; align-items: center; background: white; padding: 12px 15px; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
.filter-group label { display: block; font-size: 0.6rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
.f-controls { display: flex; gap: 8px; flex-wrap: wrap; }
.f-controls select { height: 34px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 0 10px; font-size: 0.75rem; font-weight: 700; background: #f8fafc; color: #334155; }

.info-badge { display: flex; align-items: center; gap: 6px; font-size: 0.65rem; font-weight: 700; color: #047857; background: #dcfce7; padding: 4px 10px; border-radius: 15px; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; animation: pulse 1.5s infinite; }

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* OVERRIDE FOR MOBILE FRAME CONTAINER */
.reports-layout { flex-direction: column; padding: 10px; }
.reports-sidebar { width: 100%; position: static; }
.top-filter-bar { flex-direction: column; align-items: flex-start; gap: 10px; width: 100%; }
.info-badge { align-self: flex-start; }
  
/* UX Toggling Mobile Pages */
.mobile-nav-mode .reports-sidebar { display: block; }
.mobile-nav-mode .reports-content { display: none; }
  
.mobile-content-mode .reports-sidebar { display: none; }
.mobile-content-mode .reports-content { display: flex; }
.mobile-content-mode .mobile-nav-back { display: block; }
</style>
