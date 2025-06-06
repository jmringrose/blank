import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';

// Import components
import StatusButton from './components/StatusButton.vue';
import StatusPanel from './components/StatusPanel.vue';
import test from './components/test.vue';
import HeadingSmall from './components/HeadingSmall.vue';
import videoplayer from './components/VideoPlayer.vue';

// Create Vue app
const app = createApp({});
const pinia = createPinia();

// Register plugins
app.use(pinia);

// Register components
app.component('Toaster', Toaster); // Register Toaster as a component
app.component('status-button', StatusButton);
app.component('status-panel', StatusPanel);
app.component('test', test);
app.component('headingsmall', HeadingSmall);
app.component('videoplayer', videoplayer);

// Make toast globally available
app.config.globalProperties.$toast = toast;

// Mount the app
app.mount('#app');
