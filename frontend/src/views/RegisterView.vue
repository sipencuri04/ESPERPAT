<template>
  <div class="auth-page">
    <div class="container mobile-frame">
      <div class="auth-content animate-fade-up">

        <!-- Logo / Icon -->
        <div class="logo-box">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <line x1="12" y1="5" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="19" y2="12"></line>
            <line x1="7.05" y1="7.05" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="16.95" y2="16.95"></line>
            <line x1="7.05" y1="16.95" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="7.05" x2="16.95" y2="5.64"></line>
          </svg>
        </div>

        <h1>Sign Up</h1>
        <p class="subtitle">Please enter your details to create an account.<br>A verification code will be sent to your Gmail.</p>

        <form @submit.prevent="handleRegister" class="auth-form">
          <div class="form-group">
            <div class="input-wrap">
              <div class="icon-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
              </div>
              <input v-model="form.name" type="text" placeholder="Full Name" required />
            </div>
          </div>

          <div class="form-group">
            <div class="input-wrap">
              <div class="icon-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
              </div>
              <input v-model="form.email" type="email" placeholder="E-mail (Gmail required)" required />
            </div>
          </div>

          <div class="form-group">
            <div class="input-wrap">
              <div class="icon-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              </div>
              <input v-model="form.phone" type="text" placeholder="Phone Number" required />
            </div>
          </div>

          <div class="form-group">
            <div class="input-wrap">
              <div class="icon-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/></svg>
              </div>
              <input v-model="form.password" type="password" placeholder="Password" required />
            </div>
          </div>

          <!-- Success message -->
          <div v-show="successMsg" class="success-msg animate-pop">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ successMsg }}</span>
          </div>

          <button type="submit" class="auth-btn active-btn" :disabled="authStore.loading" style="margin-top: 20px;">
            {{ authStore.loading ? 'Creating...' : 'Create an account' }}
          </button>
        </form>

        <div v-if="authStore.error" class="error-msg animate-shake">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <span>{{ authStore.error }}</span>
        </div>

        <div class="divider">
          <span>Already have an account?</span>
        </div>

        <button @click="$router.push('/login')" class="auth-btn btn-light" type="button">
          Sign In
        </button>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const form = ref({ name: '', email: '', phone: '', password: '' });
const successMsg = ref('');

const handleRegister = async () => {
  successMsg.value = '';
  authStore.error = null;
  // Basic pre-validation for gmail
  if(!form.value.email.toLowerCase().includes('@gmail.com')) {
    authStore.error = "Harus menggunakan email Gmail asli.";
    return;
  }
  
  const result = await authStore.register(form.value);
  if (result && result.success) {
    successMsg.value = result.message || "Berhasil! Silakan cek Gmail Anda untuk verifikasi.";
    setTimeout(() => {
        router.push('/login');
    }, 4000); // Wait so they can read the toast before redirect
  }
};
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  background: #ffffff;
  display: flex;
  flex-direction: column;
}

.auth-content {
  padding: 40px 1.5rem 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.logo-box {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #6366f1, #a855f7);
  color: #fff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  box-shadow: 0 6px 15px rgba(168, 85, 247, 0.3);
}

h1 {
  font-size: 1.6rem;
  font-weight: 800;
  color: #111;
  margin-bottom: 8px;
  text-align: center;
}

.subtitle {
  color: #888;
  font-size: 0.85rem;
  line-height: 1.5;
  margin-bottom: 30px;
  text-align: center;
  font-weight: 500;
}

.auth-form {
  width: 100%;
  max-width: 400px;
}

.form-group {
  margin-bottom: 16px;
}

.input-wrap {
  background: #F4F5F7;
  border-radius: 16px;
  display: flex;
  align-items: center;
  padding: 0 12px;
  height: 54px;
}

.icon-dark {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #6366f1, #a855f7);
  color: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.input-wrap input {
  background: transparent;
  border: none;
  width: 100%;
  padding-left: 14px;
  font-size: 0.95rem;
  font-family: inherit;
  outline: none;
  font-weight: 500;
}

.input-wrap input::placeholder {
  color: #9ca3af;
}

.auth-btn {
  width: 100%;
  height: 54px;
  border-radius: 18px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
  border: none;
  max-width: 400px;
}

.active-btn {
  background: linear-gradient(90deg, #3b82f6, #9333ea);
  color: white;
}

.active-btn:active {
  transform: scale(0.98);
}

.active-btn:disabled {
  opacity: 0.6;
}

.btn-light {
  background: #F4F5F7;
  color: #333;
  font-weight: 700;
}

.btn-light:active {
  background: #e2e8f0;
}

.error-msg {
  width: 100%;
  max-width: 400px;
  background: #fef2f2;
  color: #ef4444;
  padding: 12px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  margin-top: 10px;
}

.success-msg {
  width: 100%;
  max-width: 400px;
  background: #f0fdf4;
  color: #16a34a;
  padding: 12px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  margin-top: 10px;
  border: 1px solid #dcfce7;
}

.divider {
  width: 100%;
  max-width: 400px;
  display: flex;
  align-items: center;
  text-align: center;
  margin: 20px 0;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #e2e8f0;
}

.divider span {
  padding: 0 10px;
  color: #888;
  font-size: 0.8rem;
  font-weight: 500;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.animate-shake {
  animation: shake 0.3s ease-in-out;
}

@keyframes pop {
    0% { transform: scale(0.98); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
.animate-pop { animation: pop 0.3s ease-out; }
</style>
