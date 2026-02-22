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
        
        <div class="setting-item" @click="openDialog('Store Name')">
          <div class="icon-box blue">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">Nama Toko</span>
            <span class="val">ESPERPAT - Official Store</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>

        <div class="setting-item" @click="openDialog('Contact Support')">
          <div class="icon-box green">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">CS WhatsApp</span>
            <span class="val">+62 812-3456-7890</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <!-- System Settings -->
      <div class="settings-group animate-fade-up" style="animation-delay: 0.1s">
        <div class="group-label">Sistem & Keamanan</div>
        
        <div class="setting-item">
          <div class="icon-box orange">
             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">Notifikasi Order</span>
            <span class="val">Aktif</span>
          </div>
          <div class="toggle-btn active"></div>
        </div>

        <div class="setting-item" @click="openDialog('Security')">
          <div class="icon-box red">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          </div>
          <div class="item-info">
            <span class="label">Ubah Password Admin</span>
            <span class="val">Terakhir diubah 2 bln lalu</span>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="settings-footer animate-fade-up" style="animation-delay: 0.2s">
         <p class="app-version">Version 2.4.0 (Stable)</p>
         <button class="logout-btn" @click="handleLogout">Keluar Akun Admin</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const openDialog = (title) => {
  alert(`Menu ${title} akan segera hadir.`);
};

const handleLogout = () => {
  if (confirm('Keluar dari sistem admin?')) {
    authStore.logout();
    router.push('/login');
  }
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

.icon-box { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.icon-box.blue { background: #eff6ff; color: #3b82f6; }
.icon-box.green { background: #f0fdf4; color: #22c55e; }
.icon-box.orange { background: #fff7ed; color: #f97316; }
.icon-box.red { background: #fef2f2; color: #ef4444; }

.item-info { flex: 1; }
.item-info .label { display: block; font-size: 0.85rem; font-weight: 800; color: #111; margin-bottom: 2px; }
.item-info .val { font-size: 0.75rem; color: #999; font-weight: 600; }

.toggle-btn { width: 40px; height: 22px; background: #eee; border-radius: 999px; position: relative; }
.toggle-btn::after { content: ''; position: absolute; width: 18px; height: 18px; background: white; border-radius: 50%; top: 2px; left: 2px; transition: 0.3s; }
.toggle-btn.active { background: #3b82f6; }
.toggle-btn.active::after { left: 20px; }

.settings-footer { text-align: center; margin-top: 40px; padding: 0 1.5rem; }
.app-version { font-size: 0.75rem; color: #bbb; font-weight: 700; margin-bottom: 15px; }
.logout-btn { width: 100%; height: 56px; border-radius: 18px; border: 2px solid #ef4444; background: transparent; color: #ef4444; font-weight: 900; font-size: 1rem; cursor: pointer; }
</style>
