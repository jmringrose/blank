import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { Toaster, toast } from 'vue-sonner';
import 'vue-sonner/style.css';

import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

// Import components
import StatusButton from './components/StatusButton.vue';
import StatusPanel from './components/StatusPanel.vue';
import StatusLight from './components/StatusLight.vue';
import test from './components/test.vue';
import HeadingSmall from './components/HeadingSmall.vue';
import videoplayer from './components/VideoPlayer.vue';
import datatabledemo from './components/datatabledemo.vue';

// Create Vue app
const app = createApp({});
const pinia = createPinia();

// Register plugins
app.use(pinia);

// Register components
app.component('EasyDataTable', Vue3EasyDataTable);
app.component('Toaster', Toaster);
app.component('status-button', StatusButton);
app.component('status-panel', StatusPanel);
app.component('status-light', StatusLight);
app.component('test', test);
app.component('headingsmall', HeadingSmall);
app.component('videoplayer', videoplayer);
app.component('datatabledemo', datatabledemo);

// Make toast globally available
app.config.globalProperties.$toast = toast;

// Mount the app
app.mount('#app');
