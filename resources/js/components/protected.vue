<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';

const api = useApi();
const loading = ref(true);
const error = ref('');
const items = ref([]);

async function load() {
    loading.value = true;
    error.value = '';
    try {
        const { data } = await api.get('/your-protected-endpoint');
        items.value = data;
    } catch (e) {
        error.value = e?.response?.data?.message || e.message || 'Request failed';
    } finally {
        loading.value = false;
    }
}

onMounted(load);
</script>

<template>
    <div>
        <div v-if="loading">Loading</div>
        <div v-else-if="error">Error: {{ error }}</div>
        <pre v-else>{{ items }}</pre>
    </div>
</template>
