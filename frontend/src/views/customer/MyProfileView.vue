<template>
  <div class="profile-view">
    <div class="container mobile-frame">
      <!-- Deep Green Header Background with curve -->
      <div class="header-bg"></div>

      <!-- Top Nav -->
      <header class="page-header">
        <button @click="$router.push('/profile')" class="icon-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <h2 class="title-white">Profile</h2>
        <button class="icon-btn">
          <div style="position: relative;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0" fill="white"></path></svg>
            <span class="bell-dot"></span>
          </div>
        </button>
      </header>
      
      <!-- Profile Info Card Container -->
      <div class="profile-card-container">
        <!-- Floating Avatar -->
        <div class="avatar-wrap">
          <img :src="`https://ui-avatars.com/api/?name=${user.name}&background=fca5a5&color=111&size=120`" alt="Avatar" class="avatar-img" />
        </div>
        
        <!-- Info text -->
        <div class="user-info">
          <h3>Hello, {{ user.name }}</h3>
          <p class="phone-text">{{ user.phone }}</p>
          <span class="member-role">{{ user.address }}</span>
        </div>
      </div>

      <!-- General Menu Section -->
      <section class="menu-section">
        <h4 class="section-title">General</h4>
        
        <div class="menu-list">
          <!-- Edit Profile -->
          <button @click="openEditModal('profile')" class="menu-item border-bottom">
            <div class="menu-left">
              <div class="icon-box light-green-bg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              </div>
              <span class="menu-text">Edit Profile</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>

          <!-- Change Password -->
          <button @click="openEditModal('password')" class="menu-item border-bottom">
            <div class="menu-left">
              <div class="icon-box light-purple-bg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e879f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
              </div>
              <span class="menu-text">Change Password</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>

          <!-- Privacy Policy -->
          <button class="menu-item border-bottom">
            <div class="menu-left">
              <div class="icon-box light-orange-bg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
              </div>
              <span class="menu-text">Privacy Policy</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>

          <!-- Terms and Conditions -->
          <button class="menu-item">
            <div class="menu-left">
              <div class="icon-box light-red-bg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              </div>
              <span class="menu-text">Terms and Conditions</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
        </div>
      </section>

      <!-- Logout Button (Mint green background) -->
      <div class="bottom-action-area">
        <button class="action-btn" @click="handleLogout">LOG OUT</button>
      </div>

    </div>

    <!-- Edit Modal -->
    <div v-if="editModalOpen" class="modal-overlay" @click.self="editModalOpen = false">
      <div class="modal-card">
        <h3>{{ editType === 'profile' ? 'Edit Profile' : 'Change Password' }}</h3>
        
        <div v-if="editType === 'profile'" class="fields-wrap">
          <input v-model="profileForm.name" type="text" class="edit-input" placeholder="Full Name" />
          <input v-model="profileForm.phone" type="text" class="edit-input" placeholder="Phone e.g. 0812345678" />
          <textarea v-model="profileForm.address" class="edit-input" placeholder="Address"></textarea>
        </div>

        <div v-if="editType === 'password'" class="fields-wrap">
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const user = ref({
  name: 'User',
  email: '-',
  phone: '-',
  address: '-'
});

const editModalOpen = ref(false);
const editType = ref('');
const editValue = ref('');
const profileForm = ref({ name: '', phone: '', address: '' });
const saving = ref(false);

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }
  
  user.value = {
    name: authStore.user?.name || 'Customer Demo',
    email: authStore.user?.email || 'customer@esperpat.com',
    phone: authStore.user?.phone || '123123',
    address: authStore.user?.address || 'Magelang'
  };
});
import { useToast } from '../../composables/useToast';
const toast = useToast();

const openEditModal = (type) => {
  editType.value = type;
  if (type === 'profile') {
	  profileForm.value = { ...user.value };
  } else {
	  editValue.value = '';
  }
  editModalOpen.value = true;
};

const saveEdit = async () => {
  saving.value = true;
  try {
     const payload = {};
     if (editType.value === 'password') {
         payload.password = editValue.value;
     } else if (editType.value === 'profile') {
         payload.name = profileForm.value.name;
         payload.phone = profileForm.value.phone;
         payload.address = profileForm.value.address;
     }

     const res = await client.put('profile', payload);
     if (res.data?.status) {
         if (editType.value === 'profile') {
             user.value.name = payload.name;
             user.value.phone = payload.phone;
             user.value.address = payload.address;
         }
         toast.success(res.data.message || 'Profile berhasil diperbarui!', 'Update Berhasil! ✅');
         editModalOpen.value = false;
     } else {
         toast.error(res.data?.message || 'Update gagal.');
     }
  } catch (err) {
     console.error(err);
     toast.error(err.response?.data?.message || 'Terjadi kesalahan sistem.');
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
  background: #f4f5f7;
  position: relative;
  font-family: 'Inter', sans-serif;
  padding-bottom: 90px;
}

/* Deep Green Header Background */
.header-bg {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 250px;
  background: linear-gradient(135deg, #6366f1, #a855f7);
  border-bottom-left-radius: 40px;
  border-bottom-right-radius: 40px;
  z-index: 0;
  overflow: hidden;
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
  background: #ef4444;
  border-radius: 50%;
  border: 2px solid #8b5cf6;
}

.title-white {
  font-size: 1.15rem;
  font-weight: 500;
  color: white;
  letter-spacing: 0.5px;
}

/* Profile Card Container */
.profile-card-container {
  position: relative;
  z-index: 10;
  margin: 20px 1.5rem 20px;
  background: white;
  border-radius: 20px;
  padding: 0 20px 25px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.06);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.avatar-wrap {
  margin-top: -45px;
  margin-bottom: 12px;
  background: transparent;
}

.avatar-img {
  width: 85px;
  height: 85px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid white; 
  background: #fca5a5;
  box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}

.user-info h3 {
  font-size: 1.4rem;
  font-weight: 700;
  color: #111;
  margin-bottom: 5px;
}

.user-info .phone-text {
  font-size: 0.95rem;
  font-weight: 400;
  color: #64748b;
  margin-bottom: 8px;
}

.member-role {
  font-size: 0.9rem;
  font-weight: 600;
  color: #8b5cf6;
}

/* Menu Section */
.menu-section {
  position: relative;
  z-index: 10;
  margin: 20px 1.5rem 30px;
}

.section-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 15px;
  margin-left: 5px;
}

.menu-list {
  background: white;
  border-radius: 20px;
  padding: 5px 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
}

.menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  background: transparent;
  width: 100%;
  cursor: pointer;
  border: none;
  text-decoration: none;
}

.border-bottom {
  border-bottom: 1px solid #f1f5f9;
}

.menu-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Specific pastel icon boxes matching design */
.light-green-bg { background: #dcfce7; }
.light-purple-bg { background: #fae8ff; } 
.light-orange-bg { background: #ffedd5; }
.light-red-bg { background: #fee2e2; }

.menu-text {
  font-size: 0.95rem;
  font-weight: 600;
  color: #334155;
}

/* Bottom Action Area */
.bottom-action-area {
  position: fixed;
  bottom: 0px;
  left: 0;
  width: 100%;
  max-width: 480px; 
  margin: 0 auto;
  padding: 20px 1.5rem;
  background: transparent;
  z-index: 20;
}

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
.edit-input:focus { border-color: #8b5cf6; }

textarea.edit-input {
  height: 80px;
  padding-top: 15px;
  resize: none;
}

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
  background: linear-gradient(90deg, #6366f1, #a855f7);
  color: white;
}
.btn-save:disabled { background: #94a3b8; }

@media (min-width: 480px) {
  .bottom-action-area {
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>
