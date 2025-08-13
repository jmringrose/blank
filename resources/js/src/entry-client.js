// src/entry-client.js  (or src/main.js if that's your entry)
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';

import App from './App.vue';
import router from '@/router';
import { useAuthStore } from '@/stores/auth';

// Create app + pinia
const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Hydrate the token from localStorage BEFORE first navigation
const auth = useAuthStore(pinia);
if (!auth.token) {
    const t = typeof window !== 'undefined' ? localStorage.getItem('api_token') : null;
    if (t) auth.setToken(t);
}

// Set global axios defaults so ANY axios usage carries the token
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || '/api';
axios.defaults.headers.common['Accept'] = 'application/json';
if (auth.token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${auth.token}`;
}

// (Optional) keep axios auth header in sync if token changes later (login/logout)
const stop = auth.$subscribe((_mutation, state) => {
    if (state.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${state.token}`;
        localStorage.setItem('api_token', state.token);
    } else {
        delete axios.defaults.headers.common['Authorization'];
        localStorage.removeItem('api_token');
    }
});

// Mount only after the first route is ready
router.isReady().then(() => {
    app.mount('#app');
});
