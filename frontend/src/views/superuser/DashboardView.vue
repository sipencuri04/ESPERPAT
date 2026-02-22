<template>
  <div class="superuser-dashboard">
    <div class="container mobile-frame">
      <!-- Header -->
      <header class="sup-header">
        <div class="sup-title">
          <span class="badge">Superuser</span>
          <h1>Master Control</h1>
        </div>
        <button class="logout-btn" @click="handleLogout">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        </button>
      </header>

      <!-- Overall Stats -->
      <section class="mega-stats animate-fade-up">
        <div class="stat-main">
          <p>Monthly Revenue</p>
          <h2>Rp 128.450.000</h2>
          <div class="trend up">+12.5% from last month</div>
        </div>
      </section>

      <!-- User Management Sections -->
      <section class="user-mgmt animate-fade-up" style="animation-delay: 0.1s">
        <div class="mgmt-card purple">
          <div class="card-info">
            <h3>Admin Management</h3>
            <p>12 Active Admins</p>
          </div>
          <button class="go-btn">Manage</button>
        </div>

        <div class="mgmt-card green">
          <div class="card-info">
            <h3>Customer Data</h3>
            <p>1,240 Total Users</p>
          </div>
          <button class="go-btn">Views</button>
        </div>
      </section>

      <!-- Quick Actions -->
      <section class="quick-actions animate-fade-up" style="animation-delay: 0.2s">
        <h4>System Actions</h4>
        <div class="action-list">
          <div class="action-row">
            <div class="action-icon">📧</div>
            <div class="action-text">
                <strong>Broadcast Promo</strong>
                <p>Send email to all customers</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
          <div class="action-row">
            <div class="action-icon">🛠️</div>
            <div class="action-text">
                <strong>System Maintenance</strong>
                <p>Maintenance mode & backup</p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ddd" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};
</script>

<style scoped>
.superuser-dashboard {
  min-height: 100vh;
  background: #000;
  color: #fff;
}

.sup-header {
  padding: 3rem 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.badge {
  background: #22c55e;
  color: #000;
  font-size: 0.65rem;
  font-weight: 900;
  padding: 4px 10px;
  border-radius: 999px;
  text-transform: uppercase;
  margin-bottom: 8px;
  display: inline-block;
}

.sup-title h1 { font-size: 1.8rem; font-weight: 800; }

.logout-btn {
  background: rgba(255,255,255,0.1);
  color: #fff;
  border: none;
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.mega-stats {
  padding: 0 1.5rem;
  margin-bottom: 30px;
}

.stat-main {
  background: linear-gradient(135deg, #1a1a1a 0%, #000 100%);
  padding: 30px;
  border-radius: 30px;
  border: 1px solid #333;
}

.stat-main p { color: #999; font-size: 0.9rem; margin-bottom: 8px; }
.stat-main h2 { font-size: 1.6rem; font-weight: 900; margin-bottom: 8px; }
.trend { font-size: 0.8rem; font-weight: 700; }
.trend.up { color: #22c55e; }

/* User Mgmt */
.user-mgmt {
  padding: 0 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 40px;
}

.mgmt-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-radius: 24px;
}

.mgmt-card.purple { background: #7c3aed; }
.mgmt-card.green { background: #22c55e; color: #000; }

.card-info h3 { font-size: 1rem; font-weight: 800; margin-bottom: 4px; }
.card-info p { font-size: 0.8rem; opacity: 0.8; }

.go-btn {
  background: rgba(255,255,255,0.2);
  color: inherit;
  border: none;
  padding: 10px 18px;
  border-radius: 12px;
  font-weight: 800;
  font-size: 0.8rem;
  cursor: pointer;
}

/* Quick Actions */
.quick-actions {
  padding: 0 1.5rem 100px;
}

.quick-actions h4 { font-size: 1rem; margin-bottom: 16px; color: #666; }

.action-list {
  background: #111;
  border-radius: 24px;
  padding: 8px;
}

.action-row {
  display: flex;
  align-items: center;
  padding: 16px;
  gap: 15px;
}

.action-icon {
  width: 44px;
  height: 44px;
  background: #222;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.action-text { flex: 1; }
.action-text strong { display: block; font-size: 0.9rem; margin-bottom: 2px; }
.action-text p { font-size: 0.75rem; color: #666; }
</style>
