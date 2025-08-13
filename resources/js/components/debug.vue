<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const loading = ref(true);
const error   = ref('');
const items   = ref([]);
const headersDump = ref(null);
const sanctumDump = ref(null);

async function load() {
    loading.value = true;
    error.value = '';

    // read the token exactly as your app uses it
    const auth = useAuthStore();
    const token = auth.token ?? (typeof window !== 'undefined' ? localStorage.getItem('api_token') : null);
    console.log('token in store/localStorage:', token);

    // create a minimal axios just for this test
    const api = axios.create({
        baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
        headers: {
            Accept: 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
    });

    try {
        // 1) what headers did Laravel receive?
        const h = await api.get('/_debug/headers');
        headersDump.value = h.data;

        // 2) can Sanctum resolve the token to a user?
        const s = await api.get('/_debug/sanctum');
        sanctumDump.value = s.data;

        // 3) now try your real endpoint
        const r = await api.get('/email-sequence/summary');
        items.value = r.data;
    } catch (e) {
        console.log('FAIL', e?.response?.status, e?.response?.data);
        error.value = e?.response?.data?.message || e.message || 'Request failed';
    } finally {
        loading.value = false;
    }
}

onMounted(load);
</script>

<template>
    <div>
        <div v-if="loading">Loading…</div>
        <div v-else>
            <div v-if="error">Error: {{ error }}</div>

            <h3>_debug/headers</h3>
            <pre>{{ headersDump }}</pre>

            <h3>_debug/sanctum</h3>
            <pre>{{ sanctumDump }}</pre>

            <h3>/email-sequence/summary</h3>
            <pre>{{ items }}</pre>
        </div>
    </div>
</template>
