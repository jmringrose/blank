import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from "vue-toastification";
import axios from 'axios';

import "vue-toastification/dist/index.css";
import '../css/app.css';

import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

// Import components
import StatusButton from './components/StatusButton.vue';
import StatusPanel from './components/StatusPanel.vue';
import StatusLight from './components/StatusLight.vue';
import test from './components/test.vue';
/*import tabdemo from './components/TabDemo.vue';*/
import HeadingSmall from './components/HeadingSmall.vue';
import videoplayer from './components/VideoPlayer.vue';
import Datatable from './components/datatable.vue';
import formdata from './components/datatableformdata.vue';
import storedump from './components/StoreDump.vue';
import formdemo from './components/FormDemo.vue';
import EmailSequenceEdit from './components/EmailSequenceEdit.vue';
import NewsletterSequenceEdit from './components/NewsletterSequenceEdit.vue';
import NewsletterDatatable from './components/NewsletterDatatable.vue';
import NewsletterStepsDatatable from './components/NewsletterStepsDatatable.vue';
import MarketingStepsDatatable from './components/marketingStepsDatatable.vue';
import QuestionDatatable from './components/QuestionDatatable.vue';
import QuestionStepsDatatable from './components/QuestionStepsDatatable.vue';
import dashboardSequences from './components/dashboardSequences.vue';
import QueueStatus from './components/QueueStatus.vue';
import login from './components/login.vue';
import debug from './components/debug.vue';
import ApiResponseTest from './components/ApiResponseTest.vue';

import { useAuthStore } from '@/stores/auth';

// Create app and pinia
const app = createApp({});
const pinia = createPinia();
app.use(pinia);

// Set axios defaults
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Initialize auth store after pinia is set up
const auth = useAuthStore();

// Keep axios in sync with auth changes
auth.$subscribe((_mutation, state) => {
    if (state.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${state.token}`;
        localStorage.setItem('api_token', state.token);
    } else {
        delete axios.defaults.headers.common['Authorization'];
        localStorage.removeItem('api_token');
    }
});
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
app.component('formdata', formdata);
/*app.component('tabdemo', tabdemo);*/
app.component('headingsmall', HeadingSmall);
app.component('videoplayer', videoplayer);
app.component('datatable', Datatable);
app.component('email-sequence-edit', EmailSequenceEdit);
app.component('newsletter-sequence-edit', NewsletterSequenceEdit);
app.component('newsletter-datatable', NewsletterDatatable);
app.component('newsletter-steps-datatable', NewsletterStepsDatatable);
app.component('marketing-steps-datatable', MarketingStepsDatatable);
app.component('question-datatable', QuestionDatatable);
app.component('question-steps-datatable', QuestionStepsDatatable);
app.component('dashboard-sequences', dashboardSequences);
app.component('queuestatus', QueueStatus);
app.component('login', login);
app.component('debug', debug);
app.component('apiresponsetest', ApiResponseTest);

// Mount the app
app.mount('#app');
