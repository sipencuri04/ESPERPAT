<script setup>
import { RouterLink, RouterView, useRoute } from 'vue-router';
import { ref, computed } from 'vue';
import { useCartStore } from './stores/cart';

const route = useRoute();
const cartStore = useCartStore();
const isNavHidden = computed(() => {
  const hiddenNames = ['products', 'product-detail', 'cart', 'login', 'profile', 'customer-orders', 'my-profile'];
  return hiddenNames.includes(route.name) || 
         String(route.name).startsWith('admin-') || 
         String(route.name).startsWith('superuser-');
});


</script>

<template>
  <div class="app-container">

    <main class="main-content">
      <RouterView v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
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
  padding-bottom: 20px;
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
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

@media (min-width: 481px) {
    body { background: #f0f2f5; }
}

/* Global Floating Cart Styles */
.floating-cart {
  position: fixed;
  bottom: 30px;
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
