<template>
  <Teleport to="body">
    <Transition name="toast-fade">
      <div v-if="toast.state.show" class="toast-overlay" @click.self="toast.state.type !== 'confirm' ? toast.close() : null">
        <div class="toast-card" :class="toast.state.type">
          
          <!-- Close Button -->
          <button class="toast-close" @click="toast.close()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>

          <!-- Icon -->
          <div class="toast-icon-wrap">
            <!-- Success -->
            <div v-if="toast.state.type === 'success'" class="toast-icon success-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <!-- Error -->
            <div v-if="toast.state.type === 'error'" class="toast-icon error-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </div>
            <!-- Warning -->
            <div v-if="toast.state.type === 'warning'" class="toast-icon warning-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <!-- Confirm -->
            <div v-if="toast.state.type === 'confirm'" class="toast-icon confirm-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
          </div>

          <!-- Title -->
          <h3 class="toast-title">{{ toast.state.title }}</h3>

          <!-- Message -->
          <p class="toast-message">{{ toast.state.message }}</p>

          <!-- Buttons -->
          <div class="toast-actions">
            <template v-if="toast.state.type === 'confirm'">
              <button class="toast-btn btn-cancel" @click="toast.cancel()">{{ toast.state.cancelText }}</button>
              <button class="toast-btn btn-confirm" @click="toast.confirm()">{{ toast.state.confirmText }}</button>
            </template>
            <template v-else>
              <button class="toast-btn btn-ok" :class="toast.state.type" @click="toast.close()">OK</button>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast';
const toast = useToast();
</script>

<style scoped>
.toast-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.toast-card {
  background: white;
  border-radius: 28px;
  padding: 35px 25px 25px;
  width: 100%;
  max-width: 340px;
  text-align: center;
  position: relative;
  box-shadow: 0 25px 60px rgba(0,0,0,0.15);
  animation: toast-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes toast-pop {
  0% { transform: scale(0.7) translateY(30px); opacity: 0; }
  100% { transform: scale(1) translateY(0); opacity: 1; }
}

.toast-close {
  position: absolute;
  top: 14px;
  right: 14px;
  background: #f1f5f9;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #94a3b8;
  transition: all 0.2s;
}
.toast-close:hover { background: #e2e8f0; color: #475569; }

.toast-icon-wrap {
  margin-bottom: 16px;
}

.toast-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  animation: icon-bounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.15s both;
}

@keyframes icon-bounce {
  0% { transform: scale(0); }
  100% { transform: scale(1); }
}

.success-icon {
  background: linear-gradient(135deg, #10b981, #059669);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
}

.error-icon {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.35);
}

.warning-icon {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35);
}

.confirm-icon {
  background: linear-gradient(135deg, #6366f1, #7c3aed);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
}

.toast-title {
  font-size: 1.15rem;
  font-weight: 900;
  color: #0f172a;
  margin-bottom: 8px;
}

.toast-message {
  font-size: 0.88rem;
  color: #64748b;
  line-height: 1.6;
  font-weight: 500;
  margin-bottom: 22px;
}

.toast-actions {
  display: flex;
  gap: 10px;
}

.toast-btn {
  flex: 1;
  height: 48px;
  border-radius: 16px;
  border: none;
  font-weight: 800;
  font-size: 0.88rem;
  cursor: pointer;
  transition: all 0.2s;
}
.toast-btn:active { transform: scale(0.96); }

.btn-ok {
  color: white;
}
.btn-ok.success { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 20px rgba(16,185,129,0.3); }
.btn-ok.error { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 6px 20px rgba(239,68,68,0.3); }
.btn-ok.warning { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 6px 20px rgba(245,158,11,0.3); }

.btn-cancel {
  background: #f1f5f9;
  color: #64748b;
}
.btn-cancel:hover { background: #e2e8f0; }

.btn-confirm {
  background: linear-gradient(135deg, #6366f1, #7c3aed);
  color: white;
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
}

/* Transition */
.toast-fade-enter-active { transition: all 0.3s ease; }
.toast-fade-leave-active { transition: all 0.2s ease; }
.toast-fade-enter-from, .toast-fade-leave-to { opacity: 0; }
</style>
