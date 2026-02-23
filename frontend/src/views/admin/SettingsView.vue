<template>
  <div class="admin-settings">
    <div class="container mobile-frame">
      <header class="top-bar">
        <button @click="$router.push('/admin')" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <span class="title">Pengaturan Aplikasi</span>
        <div style="width: 40px"></div>
      </header>

      <!-- Shop Profile -->
      <div class="settings-group animate-fade-up">
        <div class="group-label">Profil Toko</div>
        
        <div class="setting-item" @click="openEdit('store_name')">
          <div class="icon-box blue">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
          </div>
          <div class="item-info">
            <span class="label">Nama Toko</span>
            <span class="val">{{ settings.store_name || 'ESPERPAT - Official Store' }}</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>

        <div class="setting-item" @click="openEdit('cs_whatsapp')">
          <div class="icon-box green">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">CS WhatsApp</span>
            <span class="val">{{ settings.cs_whatsapp || '+62 812-3456-7890' }}</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>

        <div class="setting-item" @click="openEdit('bank_info')">
          <div class="icon-box purple">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
          </div>
          <div class="item-info">
            <span class="label">Info Rekening Bank</span>
            <span class="val">{{ settings.bank_info || 'Belum diatur' }}</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <!-- System Settings -->
      <div class="settings-group animate-fade-up" style="animation-delay: 0.1s">
        <div class="group-label">Sistem & Keamanan</div>
        
        <div class="setting-item" @click="toggleNotif">
          <div class="icon-box orange">
             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">Notifikasi Order</span>
            <span class="val">{{ notifActive ? 'Aktif' : 'Nonaktif' }}</span>
          </div>
          <div class="toggle-btn" :class="{ active: notifActive }"></div>
        </div>

        <div class="setting-item" @click="openEdit('password')">
          <div class="icon-box red">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">Ubah Password Admin</span>
            <span class="val">Ganti password login Anda</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="settings-footer animate-fade-up" style="animation-delay: 0.2s">
         <p class="app-version">Version 2.5.0 (Production)</p>
         <button class="logout-btn" @click="handleLogout">Keluar Akun Admin</button>
      </div>

      <!-- Edit Modal -->
      <Transition name="slide-up">
        <div v-if="isEditOpen" class="modal-overlay" @click.self="isEditOpen = false">
          <div class="edit-drawer">
            <div class="drawer-handle"></div>
            <h3 class="edit-title">{{ editTitle }}</h3>
            
            <form @submit.prevent="saveEdit" class="edit-form">
              <div class="form-group" v-if="editField === 'password'">
                <label>Password Baru</label>
                <input type="password" v-model="editValue" placeholder="Masukkan password baru..." required minlength="6" />
              </div>
              <div class="form-group" v-else-if="editField === 'bank_info'">
                <label>Informasi Rekening</label>
                <textarea v-model="editValue" rows="3" placeholder="BCA 1234567890 a.n. Nama Pemilik"></textarea>
              </div>
              <div class="form-group" v-else>
                <label>{{ editTitle }}</label>
                <input type="text" v-model="editValue" :placeholder="editPlaceholder" required />
              </div>
              
              <button type="submit" class="save-btn" :disabled="saving">
                {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import client from '../../api/client';
import { useToast } from '../../composables/useToast';

const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const settings = ref({
  store_name: 'ESPERPAT - Official Store',
  cs_whatsapp: '+62 812-3456-7890',
  bank_info: 'BCA 1234567890 a.n. ESPERPAT'
});

const notifActive = ref(true);
const isEditOpen = ref(false);
const editField = ref('');
const editTitle = ref('');
const editValue = ref('');
const editPlaceholder = ref('');
const saving = ref(false);

const openEdit = (field) => {
  editField.value = field;
  if (field === 'store_name') {
    editTitle.value = 'Nama Toko';
    editValue.value = settings.value.store_name;
    editPlaceholder.value = 'Masukkan nama toko...';
  } else if (field === 'cs_whatsapp') {
    editTitle.value = 'CS WhatsApp';
    editValue.value = settings.value.cs_whatsapp;
    editPlaceholder.value = '+62 8xx-xxxx-xxxx';
  } else if (field === 'bank_info') {
    editTitle.value = 'Info Rekening Bank';
    editValue.value = settings.value.bank_info;
    editPlaceholder.value = 'BCA 1234567890 a.n. Nama';
  } else if (field === 'password') {
    editTitle.value = 'Ubah Password Admin';
    editValue.value = '';
    editPlaceholder.value = '';
  }
  isEditOpen.value = true;
};

const saveEdit = async () => {
  saving.value = true;
  try {
    if (editField.value === 'password') {
      await client.put('profile', { password: editValue.value });
      toast.success('Password admin berhasil diubah!', 'Password Diperbarui! 🔐');
    } else {
      // Save setting locally (can be connected to API later)
      settings.value[editField.value] = editValue.value;
      toast.success('Pengaturan berhasil disimpan!', 'Tersimpan! ✅');
    }
    isEditOpen.value = false;
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan pengaturan.');
  } finally {
    saving.value = false;
  }
};

const toggleNotif = () => {
  notifActive.value = !notifActive.value;
  toast.success(notifActive.value ? 'Notifikasi order diaktifkan.' : 'Notifikasi order dinonaktifkan.');
};

const handleLogout = () => {
  toast.ask('Keluar dari sistem admin?', () => {
    authStore.logout();
    router.push('/login');
  }, 'Logout Admin?');
};
</script>

<style scoped>
.admin-settings { min-height: 100vh; background: #fdfdfd; padding-bottom: 50px; }
.top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; }
.title { font-weight: 800; font-size: 1.2rem; }
.back-btn { background: none; border: none; cursor: pointer; }

.settings-group { padding: 0 1.5rem; margin-top: 10px; margin-bottom: 30px; }
.group-label { font-size: 0.75rem; font-weight: 800; color: #bbb; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; }

.setting-item { background: white; padding: 18px; border-radius: 20px; border: 1px solid #f3f3f3; display: flex; align-items: center; gap: 18px; margin-bottom: 10px; transition: background 0.2s; cursor: pointer; }
.setting-item:active { background: #f9f9f9; transform: scale(0.98); }

.icon-box { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.icon-box.blue { background: #f5f3ff; color: #8b5cf6; }
.icon-box.green { background: #f0fdf4; color: #22c55e; }
.icon-box.orange { background: #fff7ed; color: #f97316; }
.icon-box.red { background: #fef2f2; color: #ef4444; }
.icon-box.purple { background: #f5f3ff; color: #8b5cf6; }

.item-info { flex: 1; min-width: 0; }
.item-info .label { display: block; font-size: 0.85rem; font-weight: 800; color: #111; margin-bottom: 2px; }
.item-info .val { font-size: 0.75rem; color: #999; font-weight: 600; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.toggle-btn { width: 44px; height: 24px; background: #e2e8f0; border-radius: 999px; position: relative; transition: 0.3s; flex-shrink: 0; }
.toggle-btn::after { content: ''; position: absolute; width: 20px; height: 20px; background: white; border-radius: 50%; top: 2px; left: 2px; transition: 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.toggle-btn.active { background: #8b5cf6; }
.toggle-btn.active::after { left: 22px; }

.settings-footer { text-align: center; margin-top: 40px; padding: 0 1.5rem; }
.app-version { font-size: 0.75rem; color: #bbb; font-weight: 700; margin-bottom: 15px; }
.logout-btn { width: 100%; height: 56px; border-radius: 18px; border: 2px solid #ef4444; background: transparent; color: #ef4444; font-weight: 900; font-size: 1rem; cursor: pointer; transition: all 0.2s; }
.logout-btn:active { background: #fef2f2; transform: scale(0.98); }

/* Edit Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: flex-end; }
.edit-drawer { width: 100%; max-width: 480px; margin: 0 auto; background: white; border-top-left-radius: 35px; border-top-right-radius: 35px; padding: 1rem 1.7rem 3rem; }
.drawer-handle { width: 40px; height: 5px; background: #eee; border-radius: 5px; margin: 0 auto 1.5rem; }
.edit-title { font-size: 1.3rem; font-weight: 900; margin-bottom: 25px; }

.edit-form .form-group { margin-bottom: 1.5rem; }
.edit-form label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 10px; color: #333; }
.edit-form input, .edit-form textarea { width: 100%; padding: 16px; border-radius: 16px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-family: inherit; font-weight: 600; font-size: 0.95rem; outline: none; transition: border 0.2s; }
.edit-form input:focus, .edit-form textarea:focus { border-color: #8b5cf6; background: white; }
.save-btn { width: 100%; height: 56px; background: linear-gradient(135deg, #a855f7, #6366f1); color: white; border: none; border-radius: 18px; font-weight: 800; font-size: 1rem; cursor: pointer; box-shadow: 0 8px 25px rgba(139,92,246,0.3); }
.save-btn:active { transform: scale(0.98); }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }

.animate-fade-up { animation: fadeUp 0.4s ease-out both; }
@keyframes fadeUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>
