<template>
  <div class="profile-view">
    <div class="container mobile-frame">
      <!-- Deep Green Header Background (Extends behind cards) -->
      <div class="header-bg">
        <div class="header-curve"></div>
      </div>

      <!-- Top Nav exactly like mockup -->
      <header class="page-header">
        <button @click="$router.push('/')" class="icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <!-- No center title matching the first screen -->
        <button class="icon-btn">
          <router-link to="/cart" style="position: relative; display: flex;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          </router-link>
        </button>
      </header>
      
      <!-- Greeting and Date -->
      <div class="header-content title-white">
        <h2 class="title-main">Hello, {{ user.name }}</h2>
        <p class="title-sub">{{ currentDate }}</p>
      </div>

      <!-- Stats row matching the mockup -->
      <div class="stats-row">
        <div class="stat-item">
          <div class="stat-num">{{ String(orderCounts.unpaid).padStart(2, '0') }}</div>
          <div class="stat-text">Orders<br/>Pending</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">{{ String(orderCounts.packed).padStart(2, '0') }}</div>
          <div class="stat-text">Orders<br/>In Progress</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">{{ String(orderCounts.shipped).padStart(2, '0') }}</div>
          <div class="stat-text">Orders<br/>Completed</div>
        </div>
      </div>

      <!-- 2x2 Grid Menu Section -->
      <section class="grid-menu-section">
        <div class="menu-grid">
          
          <!-- History -->
          <button @click="$router.push('/orders')" class="grid-card">
            <div class="icon-box light-purple-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <h4 class="card-title">History</h4>
            <p class="card-desc">{{ ordersTotal }} recent orders</p>
          </button>

          <!-- Edit Profile -->
          <button @click="$router.push('/my-profile')" class="grid-card">
            <div class="icon-box light-green-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <h4 class="card-title">My Profile</h4>
            <p class="card-desc">Update user info</p>
          </button>

          <!-- Claim Voucher -->
          <button @click="openPromoModal" class="grid-card">
            <div class="icon-box light-blue-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h4 class="card-title">Klaim Voucher</h4>
            <p class="card-desc">Dapatkan diskon spesial</p>
          </button>

          <!-- Privacy Policy -->
          <button class="grid-card">
            <div class="icon-box light-purple2-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <h4 class="card-title">Privacy Policy</h4>
            <p class="card-desc">Terms & Conditions</p>
          </button>

        </div>
      </section>

      <!-- Fixed Bottom Action Area matching mockup (CREATE TICKET translated to LOG OUT) -->
      <div class="bottom-action-area">
        <button class="action-btn" @click="handleLogout">LOG OUT</button>
      </div>

    </div>

    <!-- Edit Modal (Password Only here) -->
    <div v-if="editModalOpen" class="modal-overlay" @click.self="editModalOpen = false">
      <div class="modal-card">
        <h3>Change Password</h3>
        
        <div class="fields-wrap">
          <input v-model="editValue" type="password" class="edit-input" placeholder="New Password" />
        </div>
        
        <div class="modal-actions">
          <button class="btn-cancel" @click="editModalOpen = false">Cancel</button>
          <button class="btn-save" @click="saveEdit" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Promo Modal -->
    <div v-if="promoModalOpen" class="modal-overlay" @click.self="promoModalOpen = false">
      <div class="modal-card promo-modal-card">
        <h3>Voucher Saya</h3>
        <p class="sub-text">Klaim voucher di bawah ini untuk melihat kodenya:</p>
        
        <div class="promo-scroller">
           <div v-if="availablePromos.length === 0" class="empty-promo">
              Belum ada promo yang tersedia.
           </div>
           
           <div v-for="promo in availablePromos" :key="promo.id" :class="['voucher-card', { exhausted: promo.quota <= 0 }]">
              <div class="v-left">
                 <div class="v-disc">{{ promo.discount_type === 'percent' ? promo.discount_value + '%' : 'Rp ' + promo.discount_value }}</div>
                 <div class="v-type">{{ promo.discount_type === 'percent' ? 'Diskon' : 'Potongan' }}</div>
              </div>
              <div class="v-right">
                 <!-- Tampilan Info -->
                 <div v-if="promo.quota <= 0" class="v-exhausted">
                    <h4>Voucher Habis!</h4>
                    <p>Kuota promo ini sudah habis</p>
                    <button class="claim-btn disabled-claim" disabled>Habis</button>
                 </div>
                 <div v-else-if="!promo.claimed" class="v-reveal">
                    <h4>Kode Rahasia</h4>
                    <p>********</p>
                    <button class="claim-btn" @click="doClaim(promo)">Klaim</button>
                 </div>
                 <div v-else class="v-claimed">
                    <h4>Berhasil Diklaim!</h4>
                    <div class="code-box">
                       <span>{{ promo.code }}</span>
                       <button class="copy-icon" @click="copyCode(promo.code)" title="Salin Kode">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                       </button>
                    </div>
                 </div>
              </div>
           </div>
        </div>
        
        <div class="modal-actions mt-3">
          <button class="btn-cancel full" @click="promoModalOpen = false">Tutup</button>
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
  email: '-',
});

const currentDate = computed(() => {
  return new Intl.DateTimeFormat('en-GB', { 
    weekday: 'long', 
    day: 'numeric', 
    month: 'short', 
    year: 'numeric' 
  }).format(new Date());
});

const ordersTotal = ref(0);
const orderCounts = ref({
  unpaid: 0,
  packed: 0,
  shipped: 0
});

const editModalOpen = ref(false);
const editType = ref('');
const editValue = ref('');
const saving = ref(false);

const promoModalOpen = ref(false);
const availablePromos = ref([]);

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }
  
  // Set real user data, handle missing gracefully
  user.value = {
    name: authStore.user?.name || 'Customer Demo',
    email: authStore.user?.email || 'customer@esperpat.com',
  };

  // Fetch true order status for stats
  try {
    const res = await client.get('orders/user'); 
    if (res.data?.data) {
       const orders = res.data.data;
       ordersTotal.value = orders.length;
       orderCounts.value.unpaid = orders.filter(o => o.status === 'New').length;
       orderCounts.value.packed = orders.filter(o => o.status === 'Process').length;
       orderCounts.value.shipped = orders.filter(o => o.status === 'Sent').length;
    }
  } catch (err) {
    console.error('Failed to fetch orders for counts', err);
  }
});

const openPromoModal = async () => {
   promoModalOpen.value = true;
   try {
      const res = await client.get('promos/available');
      if (res.data?.data) {
         availablePromos.value = res.data.data.map(p => ({
            ...p,
            claimed: localStorage.getItem('claimed_promo_' + p.id) === 'true'
         }));
      }
   } catch(e) {
      console.error(e);
   }
};

const doClaim = (promo) => {
   promo.claimed = true;
   localStorage.setItem('claimed_promo_' + promo.id, 'true');
};

const copyCode = (code) => {
   navigator.clipboard.writeText(code).then(() => {
      alert("Kode berhsil disalin!");
   });
};

const openEditModal = (type) => {
  editType.value = type;
  editValue.value = '';
  editModalOpen.value = true;
};

const saveEdit = async () => {
  saving.value = true;
  try {
     const res = await client.put('profile', { password: editValue.value });
     if (res.data?.status) {
         alert(res.data.message || 'Konfirmasi ganti password telah dikirim ke email Anda.');
         editModalOpen.value = false;
     } else {
         alert(res.data?.message || 'Gagal merubah password.');
     }
  } catch (err) {
     console.error(err);
     alert(err.response?.data?.message || 'Terjadi kesalahan sistem.');
  } finally {
     saving.value = false;
  }
};

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};
</script>

<style scoped>
.profile-view {
  min-height: 100vh;
  background: #f4f5f7; /* Matching Homepage Gray */
  position: relative;
  font-family: 'Inter', sans-serif;
  padding-bottom: 90px;
}

/* Deep Green Background that covers the top and curves under cards */
.header-bg {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 380px;
  background: linear-gradient(135deg, #6366f1, #a855f7); /* Purple Gradient */
  border-bottom-left-radius: 40px;
  border-bottom-right-radius: 40px;
  z-index: 0;
  overflow: hidden;
}

/* Slightly offset curved overlay like in design */
.header-curve {
  position: absolute;
  bottom: -40px;
  right: -50px;
  width: 200px;
  height: 200px;
  background: rgba(255,255,255,0.03);
  border-radius: 50%;
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

.bell-dot {
  position: absolute;
  top: -1px;
  right: -2px;
  width: 9px;
  height: 9px;
  background: #cbd5e1;
  border-radius: 50%;
  border: 2px solid #096851;
}

/* Header Text Content */
.header-content {
  position: relative;
  z-index: 10;
  padding: 0 1.5rem;
  margin-top: 10px;
}

.title-main {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 6px;
}

.title-sub {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 400;
}

/* Stats Row */
.stats-row {
  position: relative;
  z-index: 10;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1.5rem;
  margin-top: 45px;
  margin-bottom: 25px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 5px;
}

.stat-num {
  font-size: 2.2rem;
  font-weight: 800;
  color: white; /* Changed to white for better contrast with purple */
  line-height: 1;
}

.stat-text {
  font-size: 0.65rem;
  font-weight: 600;
  color: white;
  letter-spacing: 0.5px;
  text-transform: capitalize;
  line-height: 1.3;
}

.stat-divider {
  width: 1px;
  height: 45px;
  background: rgba(255,255,255,0.15);
}

/* Grid Menu Cards Area */
.grid-menu-section {
  position: relative;
  z-index: 10;
  margin: 20px 1.5rem;
}

.menu-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.grid-card {
  background: white;
  border-radius: 20px;
  padding: 20px 15px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  border: 1px solid #f1f5f9;
  text-align: left;
  box-shadow: 0 8px 20px rgba(0,0,0,0.03);
  cursor: pointer;
  transition: transform 0.2s;
  height: 140px;
}

.grid-card:active {
  transform: scale(0.97);
}

.icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
}

.light-purple-bg { background: #eef2ff; }
.light-green-bg { background: #dcfce7; }
.light-blue-bg { background: #eff6ff; }
.light-purple2-bg { background: #f3e8ff; }

.card-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
}

.card-desc {
  font-size: 0.65rem;
  color: #64748b;
  font-weight: 500;
}

/* Bottom Action Area */
.bottom-action-area {
  position: fixed;
  bottom: 0px;
  left: 0;
  width: 100%;
  max-width: 480px; /* Align with mobile container max */
  margin: 0 auto;
  padding: 20px 1.5rem;
  background: transparent;
  z-index: 20;
}

/* Matching the exact mint green button */
.action-btn {
  width: 100%;
  height: 56px;
  background: white;
  color: #ef4444;
  font-size: 1rem;
  font-weight: 800;
  letter-spacing: 1px;
  border-radius: 18px;
  border: 1px solid #fee2e2;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

/* Modal Styling */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-card {
  background: white;
  width: 85%;
  max-width: 350px;
  border-radius: 24px;
  padding: 25px;
}

.modal-card h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111;
  margin-bottom: 20px;
}

.edit-input {
  width: 100%;
  height: 50px;
  border-radius: 12px;
  border: 1px solid #cbd5e1;
  padding: 0 15px;
  font-family: inherit;
  font-size: 1rem;
  margin-bottom: 20px;
  outline: none;
}
.edit-input:focus { border-color: #059669; }

.modal-actions {
  display: flex;
  gap: 10px;
}

.btn-cancel, .btn-save {
  flex: 1;
  height: 48px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  border: none;
}
.btn-cancel {
  background: #f1f5f9;
  color: #475569;
}
.btn-save {
  background: #059669;
  color: white;
}
.btn-save:disabled { background: #94a3b8; }
.full { width: 100%; border-radius: 14px; }
.mt-3 { margin-top: 15px; }

/* Voucher Card Styling */
.promo-modal-card { width: 90%; max-width: 400px; padding: 25px 20px;}
.promo-modal-card h3 { margin-bottom: 5px; }
.sub-text { font-size: 0.8rem; color: #64748b; margin-bottom: 20px; text-align: left;}
.promo-scroller { display: flex; flex-direction: column; gap: 12px; max-height: 50vh; overflow-y: auto; padding-right: 5px;}
.empty-promo { text-align: center; color: #aaa; font-size: 0.85rem; padding: 20px;}

.voucher-card {
  display: flex;
  background: #fdfdfd;
  border: 1px solid #f1f5f9;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0,0,0,0.02);
}

.v-left {
  background: linear-gradient(135deg, #22c55e, #10b981);
  color: white;
  width: 90px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 10px;
}
.v-disc { font-weight: 900; font-size: 1.1rem; line-height: 1.1; text-align: center; }
.v-type { font-size: 0.65rem; font-weight: 600; opacity: 0.9; margin-top: 5px; }

.v-right {
  flex: 1;
  padding: 12px 15px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.v-reveal h4, .v-claimed h4 { font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.v-reveal p { font-size: 1.2rem; font-weight: 900; color: #94a3b8; letter-spacing: 2px; margin-bottom: 8px;}

/* Habis State */
.voucher-card.exhausted .v-left { background: linear-gradient(135deg, #ef4444, #f87171); opacity: 0.9; }
.v-exhausted h4 { font-size: 0.85rem; font-weight: 800; color: #ef4444; margin-bottom: 4px; }
.v-exhausted p { font-size: 0.75rem; color: #94a3b8; margin-bottom: 8px; font-weight: 600;}
.disabled-claim { background: #e2e8f0 !important; color: #94a3b8 !important; cursor: not-allowed !important; box-shadow: none !important; }

.claim-btn {
  background: #111;
  color: white;
  border: none;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
}

.code-box {
  background: #f1f5f9;
  border: 1px dashed #cbd5e1;
  padding: 8px 12px;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 5px;
}
.code-box span { font-weight: 800; font-size: 0.9rem; color: #111; letter-spacing: 1px;}
.copy-icon { background: none; border: none; cursor: pointer; color: #64748b; display: flex;}
.copy-icon:hover { color: #111; }

@media (min-width: 480px) {
  .bottom-action-area {
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>
