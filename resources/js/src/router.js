import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from '@/router';
import App from './App.vue';
import { useAuthStore } from '@/stores/auth';

const app = createApp(App);
const pinia = createPinia();
app.use(pinia);
app.use(router);

// ensure token is in the store before the first route resolves
const auth = useAuthStore(pinia);
if (!auth.token) {
    const t = localStorage.getItem('api_token');
    if (t) auth.setToken(t);
}

router.isReady().then(() => app.mount('#app'));
