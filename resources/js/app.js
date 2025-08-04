import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from "vue-toastification";

import "vue-toastification/dist/index.css";
import '../css/app.css';

import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

// Import components
import StatusButton from './components/StatusButton.vue';
import StatusPanel from './components/StatusPanel.vue';
import StatusLight from './components/StatusLight.vue';
import test from './components/test.vue';
import tabdemo from './components/TabDemo.vue';
import HeadingSmall from './components/HeadingSmall.vue';
import videoplayer from './components/VideoPlayer.vue';
import datatabledemo from './components/datatabledemo.vue';
import storedump from './components/StoreDump.vue';
import formdemo from './components/FormDemo.vue';
import EmailSequenceEdit from './components/EmailSequenceEdit.vue';

// Create Vue app
const app = createApp({});
const pinia = createPinia();

// Register plugins
app.use(pinia);
app.use(Toast,{
    transition: "Vue-Toastification__fade",
    maxToasts: 20,
    timeout: 2000,
    newestOnTop: true
});

// Register components
app.component('EasyDataTable', Vue3EasyDataTable);
app.component('status-button', StatusButton);
app.component('status-panel', StatusPanel);
app.component('status-light', StatusLight);
app.component('storedump', storedump);
app.component('test', test);
app.component('formdemo', formdemo);
app.component('tabdemo', tabdemo);
app.component('headingsmall', HeadingSmall);
app.component('videoplayer', videoplayer);
app.component('datatabledemo', datatabledemo);
app.component('email-sequence-edit', EmailSequenceEdit);

// Mount the app
app.mount('#app');
