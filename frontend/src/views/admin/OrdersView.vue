<template>
  <div class="admin-orders">
    <div class="container mobile-frame">
      <!-- Header -->
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Manage Orders</span>
        <div style="width: 40px"></div>
      </header>

      <!-- Stats Row -->
      <div class="order-stats animate-fade-up">
        <div class="stat-card">
          <span class="num">{{ statistics.pending || 0 }}</span>
          <span class="lbl">Pending</span>
        </div>
        <div class="stat-card blue">
          <span class="num">{{ statistics.paid || 0 }}</span>
          <span class="lbl">Ready</span>
        </div>
        <div class="stat-card green">
          <span class="num">{{ statistics.shipped || 0 }}</span>
          <span class="lbl">Sent</span>
        </div>
      </div>

      <!-- Tabs (Dynamic based on API status types) -->
      <div class="tabs animate-fade-up">
        <button v-for="tab in tabs" :key="tab.value" 
                :class="['tab-btn', { active: currentTab === tab.value }]" 
                @click="currentTab = tab.value">
          {{ tab.label }}
        </button>
      </div>

      <!-- Order List -->
      <div class="order-list animate-fade-up" style="animation-delay: 0.1s">
        <div v-if="loading" class="loader">
           <div class="spinner"></div>
           <p>Memuat pesanan...</p>
        </div>
        
        <div v-else-if="filteredOrders.length === 0" class="empty-state">
           <p>Belum ada pesanan di kategori ini.</p>
        </div>

        <div v-else v-for="order in filteredOrders" :key="order.id" class="order-card" @click="openOrderDetail(order)">
          <div class="card-head">
            <span class="invoice">{{ order.invoice_number }}</span>
            <span class="date">{{ formatDate(order.created_at) }}</span>
          </div>
          <div class="card-body">
            <div class="cust-box">
              <h4 class="cust-name">{{ order.customer_name || 'Customer' }}</h4>
              <p class="cust-addr">{{ truncateAddress(order.alamat) }}</p>
            </div>
            <div class="price-box">
              <span class="total-label">Total Payment</span>
              <strong class="total-val">{{ formatCurrency(order.total) }}</strong>
            </div>
          </div>
          <div class="card-status-row">
             <span :class="['status-pill', order.status]">{{ order.status.toUpperCase() }}</span>
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
        </div>
      </div>

      <!-- Order Detail Drawer -->
      <Transition name="slide-up">
        <div v-if="isDetailOpen" class="modal-overlay" @click.self="closeDetail">
          <div class="detail-drawer">
            <div class="drawer-handle"></div>
            
            <div class="drawer-header">
              <div class="inv-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              </div>
              <div class="header-text">
                <h3>{{ selectedOrder.invoice_number }}</h3>
                <p>{{ formatDate(selectedOrder.created_at, true) }}</p>
              </div>
            </div>

            <div class="drawer-body">
               <!-- Items List -->
               <div class="items-section">
                  <h5 class="section-lbl">Pesanan Barang:</h5>
                  <div class="item-mini-list">
                     <div v-for="item in selectedOrder.items" :key="item.id" class="item-row">
                        <span class="qty">{{ item.qty }}x</span>
                        <span class="name">{{ item.product_name || 'Part Item' }}</span>
                        <span class="price">{{ formatCurrency(item.harga) }}</span>
                     </div>
                  </div>
                  <div class="grand-total-row">
                     <span>Total Tagihan:</span>
                     <strong>{{ formatCurrency(selectedOrder.total) }}</strong>
                  </div>
               </div>

               <!-- Customer Info -->
               <div class="info-section">
                  <div class="info-group">
                    <span class="lbl">Customer</span>
                    <p class="val">{{ selectedOrder.customer_name }} ({{ selectedOrder.customer_phone || '-' }})</p>
                  </div>
                  <div class="info-group">
                    <span class="lbl">Alamat Pengiriman</span>
                    <p class="val">{{ selectedOrder.alamat }}</p>
                  </div>
                  <div v-if="selectedOrder.nomor_resi" class="info-group">
                    <span class="lbl">Nomor Resi</span>
                    <p class="val resi-text">{{ selectedOrder.nomor_resi }}</p>
                  </div>
               </div>
            </div>

            <!-- Action Buttons based on status -->
            <div class="drawer-actions">
              <button v-if="selectedOrder.status === 'pending'" class="btn-primary" @click="updateStatus('paid')">
                Konfirmasi Pembayaran
              </button>
              
              <div v-if="selectedOrder.status === 'paid'" class="ship-action">
                <input v-model="resiInput" type="text" placeholder="Masukkan Nomor Resi..." class="resi-input" />
                <button class="btn-primary" @click="updateStatus('shipped')" :disabled="!resiInput">
                  Kirim Barang
                </button>
              </div>

              <button v-if="selectedOrder.status === 'shipped'" class="btn-outline" @click="updateStatus('completed')">
                Selesaikan Pesanan
              </button>

              <button v-if="['pending', 'paid'].includes(selectedOrder.status)" class="btn-danger" @click="updateStatus('cancelled')">
                Batalkan & Restok
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import client from '../../api/client';

const orders = ref([]);
const loading = ref(true);
const currentTab = ref('pending');
const isDetailOpen = ref(false);
const selectedOrder = ref(null);
const resiInput = ref('');

const tabs = [
  { label: 'Pending', value: 'pending' },
  { label: 'Paid', value: 'paid' },
  { label: 'Shipping', value: 'shipped' },
  { label: 'All', value: 'all' },
];

const statistics = computed(() => {
  return {
    pending: orders.value.filter(o => o.status === 'pending').length,
    paid: orders.value.filter(o => o.status === 'paid').length,
    shipped: orders.value.filter(o => o.status === 'shipped').length,
  };
});

const filteredOrders = computed(() => {
  if (currentTab.value === 'all') return orders.value;
  return orders.value.filter(o => o.status === currentTab.value);
});

const fetchOrders = async () => {
    loading.value = true;
    try {
        const res = await client.get('orders');
        orders.value = res.data.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openOrderDetail = async (order) => {
    loading.value = true;
    try {
        const res = await client.get(`orders/${order.id}`);
        selectedOrder.value = res.data.data;
        isDetailOpen.value = true;
        resiInput.value = selectedOrder.value.nomor_resi || '';
    } catch (err) {
        alert('Gagal memuat detail pesanan.');
    } finally {
        loading.value = false;
    }
};

const updateStatus = async (newStatus) => {
    if (!confirm(`Ubah status pesanan ke ${newStatus.toUpperCase()}?`)) return;
    
    try {
        await client.put(`orders/${selectedOrder.value.id}/status`, {
            status: newStatus,
            nomor_resi: resiInput.value
        });
        isDetailOpen.value = false;
        fetchOrders();
    } catch (err) {
        alert(err.response?.data?.message || 'Gagal update status.');
    }
};

const closeDetail = () => { isDetailOpen.value = false; };

const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

const formatDate = (dateStr, full = false) => {
    const d = new Date(dateStr);
    if (full) return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};

const truncateAddress = (addr) => addr && addr.length > 30 ? addr.substring(0, 30) + '...' : addr;

onMounted(fetchOrders);
</script>

<style scoped>
.admin-orders { min-height: 100vh; background: #fdfdfd; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: #fdfdfd; position: sticky; top: 0; z-index: 10; }
.top-bar .title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }

/* Stats */
.order-stats { display: flex; gap: 12px; padding: 0 1.5rem; margin-bottom: 25px; }
.stat-card { flex: 1; background: #fff7ed; padding: 15px; border-radius: 20px; border: 1px solid #ffedd5; text-align: center; }
.stat-card.blue { background: #eff6ff; border-color: #dbeafe; }
.stat-card.green { background: #f0fdf4; border-color: #dcfce7; }
.stat-card .num { display: block; font-size: 1.2rem; font-weight: 900; color: #f97316; }
.stat-card.blue .num { color: #3b82f6; }
.stat-card.green .num { color: #22c55e; }
.stat-card .lbl { font-size: 0.7rem; font-weight: 700; color: #999; text-transform: uppercase; }

/* Tabs */
.tabs { display: flex; padding: 0 1.5rem; gap: 8px; margin-bottom: 20px; }
.tab-btn { flex: 1; height: 44px; border-radius: 14px; border: 1px solid #f1f1f1; background: #f9f9f9; font-size: 0.8rem; font-weight: 800; color: #aaa; cursor: pointer; }
.tab-btn.active { background: #111; color: white; border-color: #111; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

/* Order List */
.order-list { padding: 0 1.5rem 100px; display: flex; flex-direction: column; gap: 12px; }
.order-card { background: white; border-radius: 24px; padding: 1.2rem; border: 1px solid #f1f1f1; box-shadow: 0 10px 20px rgba(0,0,0,0.02); cursor: pointer; }
.card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px dashed #f1f1f1; }
.invoice { font-weight: 900; font-size: 0.85rem; color: #111; }
.date { font-size: 0.7rem; color: #bbb; font-weight: 600; }

.card-body { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
.cust-name { font-size: 0.95rem; font-weight: 800; color: #111; margin-bottom: 2px; }
.cust-addr { font-size: 0.7rem; color: #999; font-weight: 500; }

.price-box { text-align: right; }
.total-label { display: block; font-size: 0.65rem; color: #bbb; font-weight: 700; text-transform: uppercase; }
.total-val { font-size: 1rem; font-weight: 900; color: #3b82f6; }

.card-status-row { display: flex; justify-content: space-between; align-items: center; }
.status-pill { font-size: 0.6rem; font-weight: 900; padding: 4px 10px; border-radius: 8px; letter-spacing: 0.5px; }
.status-pill.pending { background: #fff7ed; color: #f97316; }
.status-pill.paid { background: #eff6ff; color: #3b82f6; }
.status-pill.shipped { background: #fdf4ff; color: #a855f7; }
.status-pill.completed { background: #f0fdf4; color: #22c55e; }
.status-pill.cancelled { background: #fef2f2; color: #ef4444; }

/* Detail Drawer */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: flex-end; }
.detail-drawer { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 35px; border-top-right-radius: 35px; padding: 1rem 1.7rem 3rem; max-height: 90vh; overflow-y: auto; }
.drawer-handle { width: 40px; height: 5px; background: #eee; border-radius: 5px; margin: 0 auto 1.5rem; }

.drawer-header { display: flex; gap: 15px; align-items: center; margin-bottom: 2rem; }
.inv-circle { width: 50px; height: 50px; background: #eff6ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
.header-text h3 { font-size: 1.1rem; font-weight: 900; }
.header-text p { font-size: 0.75rem; color: #999; font-weight: 600; }

.section-lbl { font-size: 0.75rem; font-weight: 800; color: #111; margin-bottom: 12px; text-transform: uppercase; }
.item-mini-list { background: #f8f9fa; border-radius: 18px; padding: 15px; margin-bottom: 15px; }
.item-row { display: grid; grid-template-columns: 30px 1fr auto; gap: 10px; font-size: 0.85rem; margin-bottom: 8px; }
.item-row:last-child { margin-bottom: 0; }
.item-row .qty { font-weight: 800; color: #3b82f6; }
.item-row .name { color: #444; font-weight: 600; }
.item-row .price { font-weight: 700; color: #111; }

.grand-total-row { display: flex; justify-content: space-between; padding: 10px 15px; background: #111; color: white; border-radius: 14px; margin-bottom: 25px; }
.grand-total-row span { font-size: 0.8rem; font-weight: 600; opacity: 0.8; }
.grand-total-row strong { font-size: 1rem; font-weight: 900; }

.info-section { display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px; }
.info-group .lbl { font-size: 0.7rem; font-weight: 700; color: #bbb; text-transform: uppercase; display: block; margin-bottom: 4px; }
.info-group .val { font-size: 0.85rem; font-weight: 700; color: #333; line-height: 1.4; }
.resi-text { color: #3b82f6 !important; }

.drawer-actions { display: flex; flex-direction: column; gap: 10px; }
.btn-primary { width: 100%; height: 56px; border-radius: 18px; border: none; background: #3b82f6; color: white; font-weight: 900; font-size: 1rem; cursor: pointer; box-shadow: 0 10px 20px rgba(59,130,246,0.3); }
.btn-outline { width: 100%; height: 56px; border-radius: 18px; border: 2px solid #3b82f6; background: transparent; color: #3b82f6; font-weight: 900; cursor: pointer; }
.btn-danger { width: 100%; height: 50px; border-radius: 18px; border: none; background: #fff1f2; color: #ef4444; font-weight: 800; font-size: 0.9rem; cursor: pointer; }

.ship-action { display: flex; flex-direction: column; gap: 10px; }
.resi-input { width: 100%; height: 50px; border-radius: 14px; border: 1px solid #eee; padding: 0 15px; font-family: inherit; font-weight: 700; outline: none; background: #f9f9f9; }
.resi-input:focus { border-color: #3b82f6; background: white; }

.loader { padding: 50px; text-align: center; }
.spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #3b82f6; border-radius: 50%; animation: spin 1s infinite linear; margin: 0 auto 10px; }
@keyframes spin { 100% { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 60px; color: #bbb; font-weight: 700; }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }
</style>
