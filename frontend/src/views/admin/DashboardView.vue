<template>
  <div class="admin-dashboard">
    <div class="container mobile-frame">
      <!-- Blue Header Section -->
      <div class="header-section">
        <header class="top-meta">
          <div class="user-pill">
            <img src="https://ui-avatars.com/api/?name=Admin&background=fff&color=2563eb" class="avatar" />
            <div class="user-text">
              <span class="welcome">Hello, Admin ESPERPAT 👋</span>
              <p class="location">Gudang Utama, Surabaya</p>
            </div>
          </div>
          <button class="notif-btn" @click="$router.push('/admin/orders')">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            <span v-if="pendingCount > 0" class="notif-badge">{{ pendingCount }}</span>
          </button>
        </header>

        <!-- Search Bar -->
        <div class="search-container animate-fade-up">
          <div class="search-bar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" v-model="searchQuery" @keyup.enter="handleSearch" placeholder="Cari pesanan atau sparepart..." />
            <button class="filter-btn" @click="handleSearch">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="2" y1="14" x2="6" y2="14"></line><line x1="10" y1="8" x2="14" y2="8"></line><line x1="18" y1="16" x2="22" y2="16"></line></svg>
            </button>
          </div>
        </div>
      </div>

      <div class="main-body">
        <!-- Stats Summary Card (More Dense) -->
        <section class="stats-summary animate-fade-up" style="animation-delay: 0.1s">
          <div class="stat-item">
            <span class="label">Bulan Ini</span>
            <span class="val">{{ formatCurrency(stats.sales || 0) }}</span>
            <span class="trend">Sales</span>
          </div>
          <div class="divider"></div>
          <div class="stat-item">
            <span class="label">Total</span>
            <span class="val">{{ stats.orders || 0 }}</span>
            <span class="trend">Pesanan</span>
          </div>
          <div class="divider"></div>
          <div class="stat-item">
            <span class="label">Stok</span>
            <span class="val">{{ stats.lowStock || 0 }}</span>
            <span class="trend" :style="{ color: stats.lowStock > 0 ? '#ef4444' : '#22c55e' }">Menipis</span>
          </div>
        </section>

        <!-- Hero Card (Stock Check) - Made more compact -->
        <section class="hero-card animate-fade-up" style="animation-delay: 0.15s">
          <div class="card-content">
            <h3>Inventory Status</h3>
            <p v-if="stats.lowStock > 0">Ada <strong>{{ stats.lowStock }} produk</strong> yang butuh restok segera.</p>
            <p v-else>Stok gudang dalam keadaan aman.</p>
            <button class="check-btn" @click="$router.push('/admin/products')">Manage Stock</button>
          </div>
          <div class="card-img">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
          </div>
        </section>

        <!-- Management Tools Card -->
        <section class="admin-card animate-fade-up" style="animation-delay: 0.2s">
          <div class="card-header">
            <h4>Management Tools</h4>
            <a href="#" class="see-all">See all</a>
          </div>
          <div class="modules-grid">
            <router-link v-for="mod in modules" :key="mod.label" :to="mod.to" class="module-item">
              <div :class="['icon-wrap', mod.color]">{{ mod.icon }}</div>
              <span>{{ mod.label }}</span>
            </router-link>
          </div>
        </section>

        <!-- Recent Orders Card -->
        <section class="admin-card animate-fade-up" style="animation-delay: 0.25s">
          <div class="card-header">
            <h4>Recent Orders</h4>
            <router-link to="/admin/orders" class="see-all">History</router-link>
          </div>
          <div class="order-list">
            <div v-if="recentOrders.length === 0" style="text-align: center; color: #999; font-size: 0.8rem; padding: 10px 0;">
              Tidak ada pesanan terbaru
            </div>
            <div v-else v-for="order in recentOrders" :key="order.id" class="order-mini-card" @click="$router.push('/admin/orders')" style="cursor: pointer;">
              <div class="ord-info">
                 <h5>{{ order.customer_name }}</h5>
                 <p>{{ order.invoice_number }}</p>
              </div>
              <div class="ord-price">
                <span class="status" :class="order.status.toLowerCase()">{{ order.status }}</span>
                <strong>{{ formatCurrency(order.total) }}</strong>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Admin Custom Bottom Nav -->
      <nav class="admin-bottom-nav">
        <router-link to="/admin" class="nav-btn active">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
          <span>Home</span>
        </router-link>
        <router-link to="/admin/products" class="nav-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
          <span>Inventory</span>
        </router-link>
        <router-link to="/admin/orders" class="nav-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
          <span>Orders</span>
        </router-link>
        <div class="nav-btn" @click="handleLogout">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          <span>Exit</span>
        </div>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import client from '../../api/client';
import { useToast } from '../../composables/useToast';
const toast = useToast();

const router = useRouter();
const authStore = useAuthStore();

const stats = ref({ sales: 0, orders: 0, lowStock: 0 });
const recentOrders = ref([]);
const searchQuery = ref('');
const pendingCount = ref(0);

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/admin/products', query: { search: searchQuery.value } });
    } else {
        router.push('/admin/products');
    }
};

const modules = [
  { label: 'Products', icon: '📦', color: 'blue', to: '/admin/products' },
  { label: 'Orders', icon: '🚚', color: 'purple', to: '/admin/orders' },
  { label: 'Category', icon: '📁', color: 'orange', to: '/admin/categories' },
  { label: 'Customer', icon: '👥', color: 'red', to: '/admin/customers' },
  { label: 'Penjualan', icon: '📊', color: 'green', to: '/admin/reports/sales' },
  { label: 'Settings', icon: '⚙️', color: 'cyan', to: '/admin/settings' },
  { label: 'Promo', icon: '🏷️', color: 'yellow', to: '/admin/promo' },
  { label: 'Finance', icon: '💰', color: 'emerald', to: '/admin/finance' },
];

const fetchDashboardData = async () => {
    try {
        const month = new Date().getMonth() + 1;
        const year = new Date().getFullYear();
        
        // Fetch Sales & Orders
        const salesRes = await client.get(`reports/sales?month=${month}&year=${year}`);
        if(salesRes.data?.data) {
            stats.value.sales = salesRes.data.data.total_sales;
            stats.value.orders = salesRes.data.data.total_orders;
        }

        // Fetch Orders List
        const ordersRes = await client.get('orders');
        if(ordersRes.data?.data) {
            recentOrders.value = ordersRes.data.data.slice(0, 5);
            pendingCount.value = ordersRes.data.data.filter(o => o.status === 'pending' || o.status === 'paid').length;
        }
        
        // Fetch Stock (To count low stock)
        const stockRes = await client.get('reports/stock');
        if(stockRes.data?.data?.products) {
            stats.value.lowStock = stockRes.data.data.products.filter(p => p.stock <= 5).length;
        }
    } catch (e) {
        console.error('Failed to fetch dashboard data', e);
    }
}

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

const handleLogout = () => {
  toast.ask('Keluar dari sistem admin?', () => {
    authStore.logout();
    router.push('/login');
  }, 'Logout Admin?');
};

onMounted(fetchDashboardData);
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  background: #fdfdfd;
}

/* Header Section with Purple Gradient */
.header-section {
  background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%);
  padding: 2.5rem 1.5rem 4.5rem;
  border-bottom-left-radius: 35px;
  border-bottom-right-radius: 35px;
  position: relative;
}

.top-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  color: white;
}

.user-pill { display: flex; align-items: center; gap: 10px; }
.avatar { width: 40px; height: 40px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); }
.welcome { font-size: 0.8rem; opacity: 0.85; }
.location { font-size: 0.9rem; font-weight: 700; }

.notif-btn {
  background: rgba(255,255,255,0.2);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 12px;
  color: white;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notif-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #ef4444;
  color: white;
  font-size: 0.6rem;
  font-weight: 900;
  min-width: 18px;
  height: 18px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  border: 2px solid #8b5cf6;
  animation: pulse-badge 2s infinite;
}

@keyframes pulse-badge {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.15); }
}

/* Search Bar overlap */
.search-container {
  position: absolute;
  bottom: -24px;
  left: 0;
  width: 100%;
  padding: 0 1.5rem;
}

.search-bar {
  background: white;
  height: 50px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  padding: 0 15px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.search-bar input {
  flex: 1;
  border: none;
  padding-left: 10px;
  font-family: inherit;
  font-size: 0.85rem;
  outline: none;
}

.filter-btn {
  background: #8b5cf6;
  color: white;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Main Body */
.main-body {
  padding: 3.5rem 1.5rem 100px;
}

/* Stats Summary */
.stats-summary {
  display: flex;
  justify-content: space-between;
  background: white;
  padding: 15px;
  border-radius: 20px;
  margin-bottom: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
  border: 1px solid #f1f1f1;
}

.stat-item { flex: 1; text-align: center; }
.stat-item .label { display: block; font-size: 0.7rem; color: #999; margin-bottom: 2px; }
.stat-item .val { display: block; font-size: 0.9rem; font-weight: 800; color: #111; }
.stat-item .trend { font-size: 0.65rem; color: #22c55e; font-weight: 700; }

.divider { width: 1px; height: 30px; background: #eee; align-self: center; }

/* Hero Card Compact */
.hero-card {
  background: #f5f3ff;
  border-radius: 24px;
  padding: 20px;
  display: flex;
  position: relative;
  margin-bottom: 25px;
}

.card-content { flex: 1.5; }
.card-content h3 { font-size: 1rem; font-weight: 800; color: #4c1d95; margin-bottom: 4px; }
.card-content p { font-size: 0.8rem; color: #5b21b6; line-height: 1.4; margin-bottom: 12px; }

.check-btn {
  background: #8b5cf6;
  color: white;
  border: none;
  padding: 6px 16px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.75rem;
  cursor: pointer;
}

.card-img { flex: 0.5; display: flex; align-items: center; justify-content: flex-end; }

/* Admin Cards */
.admin-card {
  background: white;
  border-radius: 24px;
  padding: 20px;
  margin-bottom: 20px;
  border: 1px solid #f1f1f1;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h4 { font-weight: 800; color: #111; font-size: 0.95rem; }
.see-all { font-size: 0.8rem; color: #8b5cf6; text-decoration: none; font-weight: 700; }

.modules-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.module-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  background: #f8f9fa;
  box-shadow: 0 4px 10px rgba(0,0,0,0.03);
}

.module-item span { font-size: 0.7rem; font-weight: 700; color: #666; text-align: center; }

/* Colors */
.blue { color: #8b5cf6; }
.purple { color: #8b5cf6; }
.orange { color: #f59e0b; }
.red { color: #ef4444; }
.green { color: #10b981; }
.cyan { color: #06b6d4; }
.yellow { color: #eab308; }
.emerald { color: #059669; }

/* Recent Orders inside card */
.order-list { display: flex; flex-direction: column; gap: 12px; }

.order-mini-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 12px;
  border-bottom: 1px solid #f8f9fa;
}

.order-mini-card:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.ord-info h5 { font-size: 0.85rem; font-weight: 700; color: #111; margin-bottom: 2px; }
.ord-info p { font-size: 0.7rem; color: #999; }

.ord-price { text-align: right; }
.ord-price .status { display: block; font-size: 0.6rem; font-weight: 800; margin-bottom: 2px; text-transform: uppercase; }
.status.new { color: #8b5cf6; }
.status.process { color: #f59e0b; }
.status.sent { color: #22c55e; }
.ord-price strong { font-size: 0.85rem; color: #111; font-weight: 800; }

/* Admin Bottom Nav */
.admin-bottom-nav {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 480px;
  background: white;
  height: 70px;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 0 1rem 5px;
  box-shadow: 0 -10px 30px rgba(0,0,0,0.03);
  z-index: 1000;
}

.nav-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: #999;
  text-decoration: none;
  cursor: pointer;
}

.nav-btn span { font-size: 0.65rem; font-weight: 700; }
.nav-btn.active { color: #8b5cf6; }
</style>
