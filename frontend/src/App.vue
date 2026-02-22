<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router';
import { ref, computed } from 'vue';
import { useCartStore } from './stores/cart';

const route = useRoute();
const cartStore = useCartStore();
const isNavHidden = computed(() => {
  const hiddenNames = ['login', 'register'];
  return hiddenNames.includes(route.name) || 
         String(route.name).startsWith('admin-') || 
         String(route.name).startsWith('superuser-');
});

const showBottomNav = computed(() => {
  const hiddenNames = ['login', 'register'];
  if (hiddenNames.includes(route.name)) return false;
  if (String(route.name).startsWith('admin-') || String(route.name).startsWith('superuser-')) return false;
  return true;
});


</script>

<template>
  <div class="app-container">

    <main class="main-content">
      <RouterView v-slot="{ Component, route }">
        <transition name="fade-slide" mode="out-in">
          <Suspense>
            <template #default>
              <component :is="Component" :key="route.name" />
            </template>
            <template #fallback>
              <div class="loading-skeleton"></div>
            </template>
          </Suspense>
        </transition>
      </RouterView>
    </main>

    <!-- Global Floating Cart Button -->
    <transition name="fade-up">
      <router-link v-if="cartStore.items.length > 0 && !isNavHidden" to="/cart" class="floating-cart">
        <div class="cart-icon-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span class="cart-badge">{{ cartStore.totalItems }}</span>
        </div>
      </router-link>
    </transition>
    <!-- Bottom Mobile Navigation -->
    <div v-if="showBottomNav" class="bottom-dock animate-fade-up">
      <router-link to="/" class="dock-item" :class="{ 'router-link-active': route.name === 'home' }">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span class="dock-label">Home</span>
      </router-link>
      <router-link to="/products" class="dock-item" :class="{ 'router-link-active': ['products', 'product-detail'].includes(route.name) }">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></svg>
        <span class="dock-label">Produk</span>
      </router-link>
      <router-link to="/profile" class="dock-item" :class="{ 'router-link-active': ['profile', 'my-profile', 'customer-orders'].includes(route.name) }">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <span class="dock-label">Profil</span>
      </router-link>
    </div>

  </div>
</template>

<style>
.app-container {
  max-width: 480px;
  margin: 0 auto;
  background: #f4f5f7;
  min-height: 100vh;
  position: relative;
  box-shadow: 0 0 50px rgba(0,0,0,0.05);
  overflow-x: hidden; /* Fix horizontal scroll */
}

.mobile-top-bar {
  padding: 1rem 0;
  position: sticky;
  top: 0;
  background: #f4f5f7; /* match app-container */
  z-index: 100;
}

.bar-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1.5rem;
}

.icon-btn {
  background: none;
  border: none;
  color: #111;
  cursor: pointer;
  padding: 8px;
  position: relative;
}

.icon-btn.notification .dot {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 8px;
  height: 8px;
  background: #ef4444; /* red dot based on mockup */
  border-radius: 50%;
  border: 2px solid white;
}

.main-content {
  padding-bottom: 90px;
}

/* White Bottom Dock matching mockup */
.bottom-dock {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 480px;
  background: white;
  height: 75px;
  display: flex;
  justify-content: space-around;
  align-items: flex-end;
  padding-bottom: 15px;
  z-index: 1000;
  border-top: 1px solid #f1f5f9;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
  box-shadow: 0 -10px 40px rgba(0,0,0,0.03);
}

.dock-item {
  color: #94a3b8;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  transition: all 0.3s;
  text-decoration: none;
  width: 60px;
}

.dock-item.router-link-active {
  color: #8b5cf6; /* Vibrant purple gradient active color */
}

.dock-label {
  font-size: 0.65rem;
  font-weight: 600;
}

.cart-icon-nav {
  position: relative;
}

.icon-box {
  position: relative;
}

.badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #a855f7; /* gradient purple */
  color: white;
  font-size: 0.6rem;
  font-weight: 800;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 2px solid white;
}

/* Transitions */
/* Fade-Slide Transitions for Routes */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(10px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}

/* Base Skeleton Loader */
.loading-skeleton {
  width: 100%;
  height: 100vh;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

@media (min-width: 481px) {
    body { background: #f0f2f5; }
}

/* Global Floating Cart Styles */
.floating-cart {
  position: fixed;
  bottom: 95px;
  left: calc(50% + 150px); /* Adjust to stay inside 480px frame but to the right */
  transform: translateX(-50%);
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #a855f7, #6366f1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
  z-index: 2000;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@media (max-width: 480px) {
  .floating-cart {
    left: auto;
    right: 25px;
    transform: none;
  }
}

.floating-cart:active {
  transform: scale(0.9);
}

.cart-icon-wrap {
  position: relative;
  display: flex;
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #ef4444;
  color: white;
  font-size: 0.7rem;
  font-weight: 800;
  min-width: 20px;
  height: 20px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  border: 2px solid white;
}

/* Fade Up Transition */
.fade-up-enter-active, .fade-up-leave-active {
  transition: all 0.4s ease;
}
.fade-up-enter-from, .fade-up-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.5);
}
</style>
