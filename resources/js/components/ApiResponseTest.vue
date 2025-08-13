<template>
    <div>
        <div v-if="loading">Loading…</div>
        <div v-else-if="error">Error: {{ error }}</div>
        <pre v-else>{{ items }}</pre>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const loading = ref(true)
const error = ref('')
const items = ref([])

async function load() {
    loading.value = true
    error.value = ''

    const auth = useAuthStore()
    const token = auth.token ?? (typeof window !== 'undefined' ? localStorage.getItem('api_token') : null)

    const api = axios.create({
        baseURL: import.meta.env.VITE_API_BASE_URL ?? '/api',
        headers: {
            Accept: 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
    })

    try {
        // these prove auth + endpoint
        await api.get('/_debug/headers')
        await api.get('/_debug/sanctum')

        const { data } = await api.get('/email-sequence/summary')
        items.value = data
    } catch (e) {
        error.value = e?.response?.data?.message || e.message || 'Request failed'
    } finally {
        loading.value = false
    }
}

onMounted(load)
</script>
