import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

import LoginView from '@/views/LoginView.vue';
import DashboardView from '@/views/DashboardView.vue';
import DataTableView from '@/views/DataTableView.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', name: 'login', component: LoginView },
        { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
        { path: '/data-table', name: 'dataTable', component: DataTableView, meta: { requiresAuth: true } },
    ],
});

router.beforeEach((to) => {
    // Skip guard during SSR
    if (typeof window === 'undefined') return true;

    const auth = useAuthStore();

    // Restore token from localStorage if needed
    if (!auth.token) {
        const storedToken = localStorage.getItem('api_token');
        if (storedToken) auth.setToken(storedToken);
    }

    // Already logged in? Avoid showing login page
    if (to.name === 'login' && auth.token) {
        let redirect = '/dashboard';
        if (to.query.redirect && typeof to.query.redirect === 'string') {
            redirect = to.query.redirect;
        }
        return { path: redirect, replace: true };
    }

    // Trying to visit a protected route without a token
    if (to.meta.requiresAuth && !auth.token) {
        return { name: 'login', query: { redirect: to.fullPath }, replace: true };
    }

    return true;
});

export default router;
