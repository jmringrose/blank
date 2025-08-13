import { createSSRApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import { useAuthStore } from './stores/auth';
import { createApi } from './src/api';

export function createApp() {
    const app = createSSRApp(App);
    const pinia = createPinia();
    app.use(pinia);

    const auth = useAuthStore(); // token is null during SSR
    const api = createApi(() => auth.token); // no token on server
    app.provide('api', api);

    return { app, pinia };
}
