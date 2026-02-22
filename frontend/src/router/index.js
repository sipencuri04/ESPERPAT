import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import HomeView from '../views/HomeView.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
    },
    {
        path: '/products',
        name: 'products',
        component: () => import('../views/ProductsView.vue'),
    },
    {
        path: '/product/:id',
        name: 'product-detail',
        component: () => import('../views/ProductDetailView.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/LoginView.vue'),
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/RegisterView.vue'),
    },
    {
        path: '/cart',
        name: 'cart',
        component: () => import('../views/CartView.vue'),
    },
    {
        path: '/profile',
        name: 'profile',
        meta: { requiresAuth: true },
        component: () => import('../views/customer/ProfileView.vue'),
    },
    {
        path: '/orders',
        name: 'customer-orders',
        meta: { requiresAuth: true },
        component: () => import('../views/customer/HistoryView.vue'),
    },
    {
        path: '/my-profile',
        name: 'my-profile',
        meta: { requiresAuth: true },
        component: () => import('../views/customer/MyProfileView.vue'),
    },
    // ADMIN ROUTES
    {
        path: '/admin',
        meta: { requiresAuth: true, role: ['admin', 'superuser'] },
        children: [
            {
                path: '',
                name: 'admin-dashboard',
                component: () => import('../views/admin/DashboardView.vue'),
            },
            {
                path: 'products',
                name: 'admin-products',
                component: () => import('../views/admin/ProductsView.vue'),
            },
            {
                path: 'orders',
                name: 'admin-orders',
                component: () => import('../views/admin/OrdersView.vue'),
            },
            {
                path: 'categories',
                name: 'admin-categories',
                component: () => import('../views/admin/CategoriesView.vue'),
            },
            {
                path: 'customers',
                name: 'admin-customers',
                component: () => import('../views/admin/CustomersView.vue'),
            },
            {
                path: 'reports/sales', // Changed path for existing reports
                name: 'admin-reports',
                component: () => import('../views/admin/ReportsView.vue'),
            },
            {
                path: 'reports/stock', // Added new stock report route
                name: 'admin-reports-stock',
                component: () => import('../views/admin/StockReportView.vue'),
            },
            {
                path: 'finance',
                name: 'admin-finance',
                component: () => import('../views/admin/FinanceView.vue'),
            },
            {
                path: 'promo',
                name: 'admin-promo',
                component: () => import('../views/admin/PromoView.vue'),
            },
            {
                path: 'settings',
                name: 'admin-settings',
                component: () => import('../views/admin/SettingsView.vue'),
            },
        ]
    },
    // SUPERUSER ROUTES
    {
        path: '/superuser',
        name: 'superuser-dashboard',
        meta: { requiresAuth: true, role: ['superuser'] },
        component: () => import('../views/superuser/DashboardView.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const userRole = authStore.user?.role;

    // 1. Prevent Admin & Superuser from accessing "BELI" (Customer) routes
    const customerRoutes = ['home', 'products', 'product-detail', 'cart', 'profile', 'customer-orders', 'my-profile'];
    if (customerRoutes.includes(to.name)) {
        if (userRole === 'admin') return next({ name: 'admin-dashboard' });
        if (userRole === 'superuser') return next({ name: 'superuser-dashboard' });
    }

    // 2. Auth & Role Guards for Admin/Superuser pages
    if (to.meta.requiresAuth) {
        if (!authStore.isAuthenticated) {
            return next({ name: 'login' });
        }

        if (to.meta.role && !to.meta.role.includes(userRole)) {
            // Redirect based on their actual role if they try to cross-access
            if (userRole === 'admin') return next({ name: 'admin-dashboard' });
            if (userRole === 'superuser') return next({ name: 'superuser-dashboard' });
            return next({ name: 'home' }); // Customer back to home
        }
    }

    // 3. Prevent logged in users from going to Login page
    if ((to.name === 'login' || to.name === 'register') && authStore.isAuthenticated) {
        if (userRole === 'admin') return next({ name: 'admin-dashboard' });
        if (userRole === 'superuser') return next({ name: 'superuser-dashboard' });
        return next({ name: 'home' });
    }

    next();
});

export default router;
