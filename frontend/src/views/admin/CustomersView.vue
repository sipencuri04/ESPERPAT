<template>
  <div class="admin-customers">
    <div class="container mobile-frame">
      <!-- Header -->
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Manage Customers</span>
        <div style="width: 40px"></div>
      </header>

      <!-- Search Box -->
      <div class="search-box animate-fade-up">
        <div class="input-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input type="text" placeholder="Cari nama atau email..." v-model="searchQuery" />
        </div>
      </div>

      <!-- Customer List -->
      <div class="customer-list animate-fade-up">
        <div v-if="loading" class="loader">
           <div class="spinner"></div>
           <p>Memuat data pelanggan...</p>
        </div>
        
        <div v-else-if="filteredCustomers.length === 0" class="empty-state">
           <p>Pelanggan tidak ditemukan.</p>
        </div>

        <div v-else v-for="user in filteredCustomers" :key="user.id" class="minimal-card" @click="openDetail(user)">
          <div class="card-inner">
            <div class="user-avatar" :class="{ inactive: !user.is_active }">
              {{ getInitials(user.name) }}
            </div>
            <div class="user-info">
              <h4 class="user-name">{{ user.name }}</h4>
              <p class="user-email">{{ user.email }}</p>
            </div>
            <div class="status-indicator" :class="{ active: user.is_active }"></div>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
        </div>
      </div>

      <!-- Detail Drawer -->
      <Transition name="slide-up">
        <div v-if="isDrawerOpen" class="modal-overlay" @click.self="closeDrawer">
          <div class="detail-drawer">
            <div class="drawer-handle"></div>
            
            <div class="drawer-header">
              <div class="avatar-large" :class="{ inactive: !selectedUser?.is_active }">
                {{ getInitials(selectedUser?.name) }}
              </div>
              <div class="header-info">
                <h3>{{ selectedUser?.name }}</h3>
                <span class="role-tag">{{ selectedUser?.role }}</span>
              </div>
            </div>

            <div class="drawer-body">
              <div class="info-grid">
                <div class="info-box">
                  <span class="label">Email</span>
                  <span class="val">{{ selectedUser?.email }}</span>
                </div>
                <div class="info-box">
                  <span class="label">No. WhatsApp</span>
                  <span class="val">{{ selectedUser?.phone || '-' }}</span>
                </div>
                <div class="info-box full">
                  <span class="label">Alamat Lengkap</span>
                  <p class="val">{{ selectedUser?.address || 'Belum mengisi alamat.' }}</p>
                </div>
                <div class="info-box full">
                  <span class="label">Status Akun</span>
                  <span :class="['status-val', { active: selectedUser?.is_active }]">
                    {{ selectedUser?.is_active ? 'Aktif' : 'Dinonaktifkan' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="drawer-actions">
              <button class="btn-action toggle" :class="{ deactivate: selectedUser?.is_active }" @click="handleToggleStatus">
                <svg v-if="selectedUser?.is_active" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                {{ selectedUser?.is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
              </button>
              <a :href="'https://wa.me/' + selectedUser?.phone" target="_blank" class="btn-action wa" v-if="selectedUser?.phone">
                Hubungi WA
              </a>
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
import { useToast } from '../../composables/useToast';
const toast = useToast();

const users = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const isDrawerOpen = ref(false);
const selectedUser = ref(null);

const fetchCustomers = async () => {
    loading.value = true;
    try {
        const res = await client.get('customers?role=customer');
        users.value = res.data.data;
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const filteredCustomers = computed(() => {
    return users.value.filter(u => 
        u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        u.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const openDetail = (user) => {
    selectedUser.value = user;
    isDrawerOpen.value = true;
};

const closeDrawer = () => { isDrawerOpen.value = false; };

const handleToggleStatus = async () => {
    const newStatus = selectedUser.value.is_active ? 0 : 1;
    const msg = newStatus ? 'Aktifkan pelanggan ini?' : 'Nonaktifkan pelanggan ini?';
    toast.ask(msg, async () => {
      try {
          await client.put(`customers/${selectedUser.value.id}/toggle`, {
              is_active: newStatus
          });
          selectedUser.value.is_active = newStatus;
          toast.success(newStatus ? 'Pelanggan berhasil diaktifkan.' : 'Pelanggan berhasil dinonaktifkan.');
          fetchCustomers();
      } catch (err) {
          toast.error('Gagal mengubah status user.');
      }
    });
};

const getInitials = (name) => {
    if (!name) return 'U';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

onMounted(fetchCustomers);
</script>

<style scoped>
.admin-customers { min-height: 100vh; background: #fdfdfd; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; position: sticky; top: 0; z-index: 10; background: #fdfdfd; }
.top-bar .title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }

.search-box { padding: 0 1.5rem; margin-bottom: 20px; }
.input-wrap { background: #f1f1f1; border-radius: 16px; display: flex; align-items: center; padding: 0 15px; height: 50px; }
.input-wrap input { flex: 1; border: none; background: transparent; padding-left: 10px; font-family: inherit; outline: none; font-weight: 600; }

/* List */
.customer-list { padding: 0 1.5rem 100px; display: flex; flex-direction: column; gap: 10px; }
.minimal-card { background: white; border-radius: 20px; padding: 15px 18px; border: 1px solid #f3f3f3; transition: transform 0.2s; cursor: pointer; }
.minimal-card:active { transform: scale(0.98); background: #f9f9f9; }
.card-inner { display: flex; align-items: center; gap: 15px; }

.user-avatar { width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.9rem; flex-shrink: 0; }
.user-avatar.inactive { background: #f1f1f1; color: #aaa; }

.user-info { flex: 1; }
.user-name { font-size: 0.95rem; font-weight: 800; color: #111; margin-bottom: 2px; }
.user-email { font-size: 0.75rem; color: #999; font-weight: 500; }

.status-indicator { width: 8px; height: 8px; background: #ef4444; border-radius: 50%; }
.status-indicator.active { background: #22c55e; box-shadow: 0 0 8px rgba(34,197,94,0.4); }

/* Drawer */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: flex-end; }
.detail-drawer { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 35px; border-top-right-radius: 35px; padding: 1rem 1.7rem 3rem; animation: slideUp 0.3s ease-out; }
.drawer-handle { width: 40px; height: 5px; background: #eee; border-radius: 5px; margin: 0 auto 1.5rem; }

.drawer-header { display: flex; gap: 20px; align-items: center; margin-bottom: 2rem; }
.avatar-large { width: 70px; height: 70px; background: #eff6ff; color: #3b82f6; border-radius: 24px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.5rem; }
.avatar-large.inactive { background: #f1f1f1; color: #aaa; }
.header-info h3 { font-size: 1.3rem; font-weight: 900; line-height: 1.2; }
.role-tag { font-size: 0.65rem; font-weight: 900; color: #3b82f6; background: #eff6ff; padding: 4px 10px; border-radius: 8px; text-transform: uppercase; }

.info-grid { display: grid; grid-template-columns: 1fr; gap: 15px; }
.info-box { padding: 15px; background: #f8f9fa; border-radius: 18px; }
.info-box .label { display: block; font-size: 0.7rem; color: #bbb; margin-bottom: 4px; font-weight: 700; text-transform: uppercase; }
.info-box .val { font-size: 0.9rem; font-weight: 700; color: #333; }
.status-val { font-weight: 800; color: #ef4444; }
.status-val.active { color: #22c55e; }

.drawer-actions { display: flex; flex-direction: column; gap: 10px; margin-top: 2rem; }
.btn-action { height: 56px; border-radius: 18px; border: none; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; text-decoration: none; font-size: 0.95rem; }
.btn-action.toggle { background: #f0fdf4; color: #22c55e; }
.btn-action.toggle.deactivate { background: #fff1f2; color: #ef4444; }
.btn-action.wa { background: #111; color: white; }

.loader { padding: 50px; text-align: center; }
.spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #3b82f6; border-radius: 50%; animation: spin 1s infinite linear; margin: 0 auto 10px; }
@keyframes spin { 100% { transform: rotate(360deg); } }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }
</style>
