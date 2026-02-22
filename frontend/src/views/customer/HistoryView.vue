<template>
  <div class="history-view">
    <div class="container mobile-frame">
      <!-- Deep Green Header Background like Mockup -->
      <div class="header-bg"></div>

      <!-- Top Nav -->
      <header class="page-header">
        <button @click="$router.push('/profile')" class="icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <h2 class="title-white">History</h2>
        <div style="width: 22px;"></div> <!-- Spacer for center alignment -->
      </header>

      <!-- Tabs Pill (Inside green area matching mockup) -->
      <div class="tabs-wrapper">
        <div class="tabs-scroll">
          <button @click="activeTab = 'all'" class="tab-btn" :class="{ active: activeTab === 'all' }">All</button>
          <button @click="activeTab = 'New'" class="tab-btn" :class="{ active: activeTab === 'New' }">Pending</button>
          <button @click="activeTab = 'Process'" class="tab-btn" :class="{ active: activeTab === 'Process' }">Ongoing</button>
          <button @click="activeTab = 'Sent'" class="tab-btn" :class="{ active: activeTab === 'Sent' }">Completed</button>
        </div>
      </div>

      <!-- Main Content Overlay (White Rounded Top) -->
      <section class="main-content">
        <!-- Dynamic Header based on tab -->
        <h3 class="list-title">
          {{ activeTab === 'all' ? 'All Orders' : activeTab === 'New' ? 'Pending Orders' : activeTab === 'Process' ? 'Ongoing Orders' : 'Completed Orders' }}
        </h3>
        
        <div v-if="loading" class="text-center py-5" style="color: #666;">
          Loading your history...
        </div>
        
        <div v-else-if="filteredOrders.length === 0" class="empty-state">
           <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M21 8v13H3V8"></path><path d="M1 3h22v5H1z"></path><path d="M10 12h4"></path></svg>
           <p>No orders found.</p>
        </div>

        <div v-else class="orders-wrap">
          <!-- Order Card matching mockup -->
          <div class="order-card" v-for="order in filteredOrders" :key="order.id" @click="openOrderDetail(order.id)">
            
            <!-- Left colored bar -->
            <div class="status-bar" :style="{ background: getStatusColorHex(order.status) }"></div>
            
            <div class="card-inner">
              <div class="top-row">
                <!-- Order ID Text -->
                <div class="invoice-box">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                  <span class="invoice-text">{{ order.invoice_number || 'INV-' + order.id }}</span>
                </div>
                
                <!-- Circular Progress SVG -->
                <div class="progress-circle">
                  <svg viewBox="0 0 36 36" width="38" height="38">
                    <!-- Background Circle -->
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f1f5f9" stroke-width="3"/>
                    <!-- Progress Circle -->
                    <path class="circle" :stroke="getStatusColorHex(order.status)" :stroke-dasharray="getProgressValue(order.status) + ', 100'" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3" stroke-linecap="round"/>
                    <text x="18" y="22" class="percentage-text" fill="#1e293b">{{ getProgressValue(order.status) }}%</text>
                  </svg>
                </div>
              </div>

              <!-- Time -->
              <div class="time-row">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>{{ order.created_at }}</span>
              </div>

              <!-- Title (Replaced Task Title with Order context) -->
              <div class="order-title">
                {{ formatCurrency(order.total || 0) }}
              </div>

              <!-- Footer with Avatar and Badge -->
              <div class="card-footer">
                <div class="user-info">
                  <img :src="`https://ui-avatars.com/api/?name=${user.name}&background=f1f5f9&color=64748b&size=40`" alt="Avatar" class="avatar-small" />
                  <div class="user-text">
                    <span class="u-name">{{ user.name }}</span>
                    <span class="u-dept">{{ user.address || 'Customer' }}</span>
                  </div>
                </div>

                <!-- Outline Badge -->
                <div class="pill-badge" :style="{ color: getStatusColorHex(order.status), border: '1px solid ' + getStatusColorHex(order.status) + '40', background: getStatusColorHex(order.status) + '10' }">
                  {{ getStatusText(order.status) }}
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </section>

    <!-- Modal Overlay -->
    <div v-show="isModalOpen" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
         <button class="btn-close-modal" @click="closeModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
         </button>
         
         <div v-if="selectedOrder" class="modal-content-scroll">
            <h3 class="modal-title">Detail Pesanan</h3>
            
            <div class="info-group">
               <div class="info-row">
                  <span class="i-label">Invoice:</span>
                  <span class="i-val">{{ selectedOrder.invoice_number }}</span>
               </div>
               <div class="info-row">
                  <span class="i-label">Status:</span>
                  <span class="i-val" :style="{ color: getStatusColorHex(selectedOrder.status), fontWeight: 'bold' }">{{ getStatusText(selectedOrder.status) }}</span>
               </div>
               <div class="info-row" v-if="selectedOrder.nomor_resi">
                  <span class="i-label">Resi:</span>
                  <span class="i-val" style="color:#a855f7;">{{ selectedOrder.nomor_resi }}</span>
               </div>
               <div class="info-row">
                  <span class="i-label">Tanggal:</span>
                  <span class="i-val">{{ selectedOrder.created_at }}</span>
               </div>
            </div>

            <div class="countdown-warning" v-if="selectedOrder.status === 'pending'">
               <div class="cw-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
               </div>
               <div class="cw-text">
                  <p>Selesaikan pembayaran sebelum:</p>
                  <h4>{{ countdownText(selectedOrder.created_at) }}</h4>
               </div>
            </div>

            <div class="items-list">
               <h4 class="section-sub">Daftar Barang</h4>
               <div class="item-line" v-for="item in selectedOrder.items" :key="item.id">
                  <div class="item-line-txt">
                     <span>{{ item.qty }}x Produk (ID:{{ item.product_id }})</span>
                     <span class="sub-muted">@ {{ formatCurrency(item.harga) }}</span>
                  </div>
                  <span class="item-line-sub">{{ formatCurrency(item.subtotal) }}</span>
               </div>
            </div>

            <div class="total-bar">
               <span>Total:</span>
               <span class="t-amount">{{ formatCurrency(selectedOrder.total) }}</span>
            </div>

            <div class="modal-actions mt-4" v-if="selectedOrder.status === 'pending'">
               <button class="btn-cancel-order" @click="doCancel(selectedOrder.id)">Batalkan</button>
               <button class="btn-pay-order" @click="doPay(selectedOrder.id)">Bayar Sekarang</button>
            </div>
         </div>
         <div v-else class="text-center py-4 text-gray-500">
            Fetching order details...
         </div>
      </div>
    </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';
import client from '../../api/client';

const authStore = useAuthStore();
const router = useRouter();

const user = ref({
  name: 'User',
  phone: '-',
  address: '-'
});

const orders = ref([]);
const loading = ref(true);
const activeTab = ref('all');
const isModalOpen = ref(false);
const selectedOrder = ref(null);
const currentTime = ref(Date.now());
let timerInterval = null;

// "New" = Pending, "Process" = Ongoing, "Sent" = Completed
onMounted(async () => {
  timerInterval = setInterval(() => { currentTime.value = Date.now(); }, 1000);
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }
  
  user.value = {
    name: authStore.user?.name || 'Customer Demo',
    phone: authStore.user?.phone || '123123',
    address: authStore.user?.address || 'Magelang'
  };

  await fetchOrders();
});

const fetchOrders = async () => {
  loading.value = true;
  try {
    const res = await client.get('orders/user'); 
    if (res.data?.data) {
       orders.value = res.data.data;
    }
  } catch (err) {
    console.error('Failed to fetch orders', err);
    // Fake data for UI preview
    if (orders.value.length === 0) {
       orders.value = [
         { id: 1, invoice: 'SER13312', created_at: 'Tomorrow - 12:45 pm', status: 'Process', total_harga: 155000, items: [1] },
         { id: 2, invoice: 'SER13313', created_at: 'Tomorrow - 12:45 pm', status: 'Sent', total_harga: 420000, items: [1,2] },
         { id: 3, invoice: 'SER13314', created_at: 'Tomorrow - 12:45 pm', status: 'New', total_harga: 85000, items: [1] },
       ];
    }
  } finally {
    loading.value = false;
  }
};

const filteredOrders = computed(() => {
  if (activeTab.value === 'all') return orders.value;
  return orders.value.filter(o => o.status === activeTab.value);
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(value);
};

const getStatusColorHex = (status) => {
  if (status === 'Sent') return '#10b981'; // Green
  if (status === 'Process') return '#a855f7'; // Purple
  return '#f97316'; // Orange
};

const getProgressValue = (status) => {
  if (status === 'Sent') return 100;
  if (status === 'Process') return 50;
  return 0;
};

const getStatusText = (status) => {
  if (status === 'Sent' || status === 'completed') return 'COMPLETED';
  if (status === 'Process' || status === 'paid' || status === 'shipped') return 'IN PROGRESS';
  if (status === 'cancelled') return 'CANCELLED';
  return 'PENDING';
};

import { onUnmounted } from 'vue';

onUnmounted(() => {
   if (timerInterval) clearInterval(timerInterval);
});

const countdownText = (createdAt) => {
    if (!createdAt) return '00:00:00';
    const start = new Date(createdAt.replace(' ', 'T')).getTime();
    if(isNaN(start)) return '00:00:00';
    const expertion = start + (24 * 60 * 60 * 1000);
    const diff = expertion - currentTime.value;
    if (diff <= 0) return 'Kedaluwarsa';
    
    let h = Math.floor(diff / (1000 * 60 * 60));
    let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    let s = Math.floor((diff % (1000 * 60)) / 1000);
    
    return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
};

const openOrderDetail = async (id) => {
   selectedOrder.value = null;
   isModalOpen.value = true;
   try {
       const res = await client.get('orders/' + id);
       if (res.data?.status) {
           selectedOrder.value = res.data.data;
       }
   } catch(e) {
       console.error("Gagal load detail", e);
       alert("Gagal memuat detail pesanan.");
       isModalOpen.value = false;
   }
};

const closeModal = () => {
   isModalOpen.value = false;
   selectedOrder.value = null;
};

const doCancel = async (id) => {
   if (!confirm("Yakin ingin membatalkan pesanan ini?")) return;
   try {
       await client.post('orders/' + id + '/status', { status: 'cancelled' });
       alert("Pesanan berhasil dibatalkan.");
       closeModal();
       fetchOrders();
   } catch (e) {
       alert("Gagal membatalkan pesanan.");
   }
};

const doPay = async (id) => {
   // Simulasi fitur bayar, kita anggap paid
   if (!confirm("Simulasi: Tandai Pesanan sebagai telah dibayar?")) return;
   try {
       await client.post('orders/' + id + '/status', { status: 'paid' });
       alert("Simulasi pembayaran berhasil!");
       closeModal();
       fetchOrders();
   } catch (e) {
       alert("Gagal simulasi pembayaran.");
   }
};
</script>

<style scoped>
.history-view {
  min-height: 100vh;
  background: #fdfdfd;
  position: relative;
  font-family: 'Inter', sans-serif;
}

/* Deep Purple Gradient Header Background */
.header-bg {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 220px;
  background: linear-gradient(135deg, #a855f7, #6366f1);
  z-index: 0;
}

/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 1rem;
  position: relative;
  z-index: 10;
}

.icon-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.title-white {
  font-size: 1.15rem;
  font-weight: 500;
  color: white;
  letter-spacing: 0.5px;
}

/* Tabs Pill Container */
.tabs-wrapper {
  position: relative;
  z-index: 10;
  margin: 15px 1.5rem 30px;
}

.tabs-scroll {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255,255,255,0.12); /* Semi transparent white pill */
  border-radius: 20px;
  padding: 4px; border: 1px solid rgba(255,255,255,0.15);
}

.tab-btn {
  flex: 1;
  background: transparent;
  border: none;
  color: #a7f3d0; /* Light mint green matching mockup unselected text */
  padding: 8px 10px;
  font-size: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  border-radius: 16px;
  transition: all 0.3s;
}

.tab-btn.active {
  background: rgba(255,255,255,0.25);
  color: white;
  font-weight: 700;
}

/* Main Content Overlay (White BG sweeping up) */
.main-content {
  position: relative;
  z-index: 10;
  background: #f8fafc;
  border-top-left-radius: 30px;
  border-top-right-radius: 30px;
  min-height: calc(100vh - 160px);
  padding: 25px 1.5rem 40px;
}

.list-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 20px;
}

.orders-wrap {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* Individual Order Card (Like "Task" format) */
.order-card {
  display: flex;
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.03);
  position: relative;
  overflow: hidden;
}

/* Left colored bar matching mockup */
.status-bar {
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 5px;
  height: 60%; /* Doesn't stretch full way */
  border-top-right-radius: 5px;
  border-bottom-right-radius: 5px;
}

.card-inner {
  flex: 1;
  padding-left: 10px; /* Space from colored bar */
}

.top-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 5px;
}

.invoice-box {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 5px;
}

.invoice-text {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.progress-circle {
  margin-top: -5px;
  margin-right: -5px;
}

.percentage-text {
  font-size: 11px;
  font-weight: 800;
  font-family: inherit;
  text-anchor: middle;
}

.time-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
  color: #64748b;
  margin-bottom: 12px;
}

.order-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 15px;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px dashed #e2e8f0; /* Subtle dashed separator */
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.avatar-small {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.user-text {
  display: flex;
  flex-direction: column;
}

.u-name {
  font-size: 0.75rem;
  font-weight: 700;
  color: #0f172a;
}

.u-dept {
  font-size: 0.65rem;
  color: #64748b;
}

.pill-badge {
  font-size: 0.65rem;
  font-weight: 800;
  padding: 6px 10px;
  border-radius: 8px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

/* Modal Styes */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(5px);
  z-index: 2000;
  display: flex;
  align-items: flex-end; /* Slide up from bottom naturally */
  justify-content: center;
}

.modal-card {
  background: white;
  width: 100%;
  max-width: 480px;
  max-height: 85vh;
  border-top-left-radius: 30px;
  border-top-right-radius: 30px;
  padding: 25px 20px;
  position: relative;
  display: flex;
  flex-direction: column;
}

.btn-close-modal {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #f1f5f9;
  border: none;
  border-radius: 50%;
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  z-index: 10;
}

.modal-content-scroll {
  overflow-y: auto;
  padding-right: 5px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 20px;
  text-align: center;
}

.info-group {
  background: #f8fafc;
  border-radius: 16px;
  padding: 15px;
  display: flex; flex-direction: column; gap: 8px;
  margin-bottom: 20px;
  border: 1px dashed #cbd5e1;
}

.info-row {
  display: flex; justify-content: space-between; font-size: 0.85rem;
}

.info-row .i-label { color: #64748b; font-weight: 500; }
.info-row .i-val { color: #1e293b; font-weight: 700; }

.countdown-warning {
  background: linear-gradient(135deg, #fff1f2, #ffe4e6);
  border: 1px solid #fecdd3;
  border-radius: 16px;
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
  color: #e11d48;
}

.cw-icon {
  background: white;
  min-width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 10px rgba(225, 29, 72, 0.1);
}

.cw-text p { margin: 0; font-size: 0.75rem; font-weight: 600; color: #f43f5e; }
.cw-text h4 { margin: 2px 0 0; font-size: 1.2rem; font-weight: 900; letter-spacing: 1px; }

.section-sub {
  font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 10px;
}

.items-list {
  background: #f8fafc;
  border-radius: 16px;
  padding: 15px;
  margin-bottom: 20px;
}

.item-line {
  display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 10px;
  padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;
}
.item-line:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
.item-line-txt { display: flex; flex-direction: column; color: #334155; font-weight: 600; }
.item-line-txt .sub-muted { color: #94a3b8; font-size: 0.7rem; font-weight: 500; }
.item-line-sub { font-weight: 800; color: #0f172a; }

.total-bar {
  display: flex; justify-content: space-between; align-items: center;
  background: #1e293b; color: white; padding: 15px 20px; border-radius: 16px;
  font-weight: 700; font-size: 1rem;
}

.total-bar .t-amount { font-size: 1.15rem; font-weight: 900; color: #a855f7; text-shadow: 0 2px 10px rgba(168,85,247,0.4); }

.modal-actions {
  display: flex; gap: 10px; margin-top: 20px; padding-bottom: 10px;
}

.btn-cancel-order {
  flex: 1; padding: 15px; border-radius: 14px; border: 1px solid #e2e8f0; background: white;
  color: #64748b; font-weight: 800; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
}
.btn-cancel-order:active { transform: scale(0.95); background: #f8fafc; }

.btn-pay-order {
  flex: 2; padding: 15px; border-radius: 14px; border: none; 
  background: linear-gradient(135deg, #a855f7, #6366f1);
  color: white; font-weight: 800; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
  box-shadow: 0 10px 20px rgba(168, 85, 247, 0.3);
}
.btn-pay-order:active { transform: scale(0.95); box-shadow: 0 5px 10px rgba(168, 85, 247, 0.3); }

</style>
