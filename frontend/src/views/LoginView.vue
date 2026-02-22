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

        <h1>Sign In</h1>
        <p class="subtitle">To sign in to an account in the application,<br>enter your email and password</p>

        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <div class="input-wrap">
              <div class="icon-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
              </div>
              <input v-model="form.email" type="email" placeholder="E-mail" required />
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

          <div class="forgot-pass">
            <a href="#">Forgot password?</a>
          </div>

          <button type="submit" class="auth-btn active-btn" :disabled="authStore.loading">
            {{ authStore.loading ? 'Signing In...' : 'Continue' }}
          </button>
        </form>

        <div v-if="authStore.error" class="error-msg animate-shake">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <span>{{ authStore.error }}</span>
        </div>

        <div class="divider">
          <span>Don't have an account yet?</span>
        </div>

        <button @click="$router.push('/register')" class="auth-btn btn-light" type="button">
          Create an account
        </button>

        <!-- Social Buttons -->
        <!-- Decorative only -->
        <button class="auth-btn btn-light social-btn" type="button">
          <svg style="margin-right: 8px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-1.08-.04-2.14.12-2.795.153-.656.033-1.604-.157-2.75-.157C4.664 3.864 3 5.488 3 7.828c0 2.34.802 4.679 2.193 6.307 1.391 1.628 3.013 2.105 3.513 2.155.5.05 1.58-.16 2.05-.28.47-.12 1.37-.502 1.62-.644.25-.142.12-.224 2.12-3.625M9.46 15.688c-.655.033-1.603-.157-2.75-.157C4.664 15.53 3 13.906 3 11.566c0-2.34.802-4.679 2.193-6.307 1.391-1.628 3.013-2.105 3.513-2.155.5.05 1.58-.16 2.05-.28.47-.12 1.37-.502 1.62-.644.25-.142.12-.224z" fill-rule="evenodd" clip-rule="evenodd"/></svg>
          Sign in with Apple
        </button>

        <button class="auth-btn btn-light social-btn" style="margin-bottom: 20px" type="button">
          <svg style="margin-right: 8px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.892 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" fill="#4285F4" fill-rule="evenodd" clip-rule="evenodd"/></svg>
          Sign in with Google
        </button>

        <p class="terms">By clicking "Continue", I have read and agree<br>with the <a href="#">Term Sheet, Privacy Policy</a></p>
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
const form = ref({ email: '', password: '' });

const handleLogin = async () => {
  const success = await authStore.login(form.value);
  if (success) {
    const role = authStore.user.role;
    if (role === 'superuser') {
      router.push('/superuser');
    } else if (role === 'admin') {
      router.push('/admin');
    } else {
      router.push('/');
    }
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
  background: #014A43;
  color: #fff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(1, 74, 67, 0.2);
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
  background: #014A43;
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

.forgot-pass {
  text-align: center;
  margin: 16px 0 24px;
}

.forgot-pass a {
  font-size: 0.85rem;
  color: #014A43;
  font-weight: 700;
  text-decoration: none;
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
  background: #014A43;
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
  margin-bottom: 12px;
  font-weight: 700;
}

.btn-light:active {
  background: #e2e8f0;
}

.social-btn {
  font-weight: 700;
  font-size: 0.95rem;
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

.terms {
  font-size: 0.75rem;
  color: #888;
  text-align: center;
  line-height: 1.5;
  margin-top: 20px;
}

.terms a {
  color: #111;
  font-weight: 700;
  text-decoration: underline;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

.animate-shake {
  animation: shake 0.3s ease-in-out;
}
</style>
