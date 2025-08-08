<template>
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border text-gray-700 p-4 mt-12 align-middle">
        <h1 class="text-xl font-bold">Sequences Dashboard</h1>
        <div class="my-4">
            <p class="text-lg">Total Sequences: <b>{{ summary.total }}</b>
                <button
                    :disabled="isLoading"
                    class="ml-3 px-4 py-3 bg-blue-100 hover:bg-blue-200 rounded text-blue-700 text-sm float-right"
                    @click="fetchSummary"
                >
                    <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                    Refresh
                </button>
            </p>
            <div v-if="lastChecked" class="text-xs text-gray-500 mb-2 mt-2">
                Last checked: {{ lastCheckedAgo }}
            </div>
        </div>
        <div>
            <h2 class="text-md font-semibold mb-2">Summary by Step</h2>
            <table class="min-w-96 border border-gray-200">
                <thead>
                <tr>
                    <th class="px-2 py-1 border">Step</th>
                    <th class="px-2 py-1 border">Count</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(count, step) in summary.steps" :key="step">
                    <td class="px-2 py-1 border">{{ step }}</td>
                    <td class="px-2 py-1 border">{{ count }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border my-8 text-gray-700 mb-6 pb-6">
        <div class="flex items-center px-4 py-2 bg-gray-100">



     <span class="mr-3 text-sm">
        <component
            :is="queueStatus.running ? 'LucideCheckCircle' : 'LucideXCircle'"
            :class="queueStatus.running ? 'text-green-500' : 'text-red-500'"
        />
      </span>
            <div>
                <div class="font-bold text-lg">
                    Queue Worker:
                    <span :class="queueStatus.running ? 'text-green-600' : 'text-red-600'">{{ queueStatus.running ? 'Running' : 'Not Running' }}
          </span>
                </div>
                <div class="text-xs text-gray-500">
                    Last checked: <span v-if="queueStatus.last_seen">{{ timeAgo(queueStatus.last_seen) }}</span>
                </div>
            </div>
            <button
                class="ml-3 px-4 py-3 bg-blue-100 hover:bg-blue-200 rounded text-blue-700 text-sm float-right"
                @click="getStatus"
            >
                <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                Refresh
            </button>
        </div>
        <table class="min-w-full">
            <tbody>
            <tr>
                <td class="px-6 py-2 font-medium bg-gray-50 border-b border-gray-200">Status</td>
                <td class="px-6 py-2 border-b border-gray-200">
            <span
                :class="[
                'inline-flex items-center px-2 py-1 rounded text-xs font-semibold',
                queueStatus.running
                  ? 'bg-green-100 text-green-800'
                  : 'bg-red-100 text-red-700'
              ]"
            >
              <component
                  :is="queueStatus.running ? 'LucidePlay' : 'LucidePause'"
                  class="mr-1"
              />
              {{ queueStatus.running ? 'Running' : 'Not Running' }}
            </span>
                </td>
            </tr>
            <tr>
                <td class="px-6 py-2 bg-gray-50">Last Seen</td>
                <td class="px-6 py-2">
                    {{ queueStatus.last_seen || 'Never' }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue'
import {LucideRefreshCw,} from 'lucide-vue-next'

const lastChecked = ref(null)
const isLoading = ref(false)
let intervalId = null
const summary = ref({total: 0, steps: {}});
const queueStatus = ref({running: false, last_seen: null});
const queue = ref([]);

async function fetchSummary() {
    isLoading.value = true
    try {
        const res = await fetch('/api/email-sequence/summary')
        summary.value = await res.json()
        lastChecked.value = new Date()
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    fetchSummary()
    intervalId = setInterval(fetchSummary, 2 * 60 * 1000) // every 2 minutes
})
onUnmounted(() => {
    if (intervalId) clearInterval(intervalId)
})

async function getStatus() {
    const res = await fetch('/api/status/queue')
    queueStatus.value = await res.json()
}

function timeAgo(date) {
    if (!date) return ''
    const now = new Date()
    const then = new Date(date)
    const diff = Math.floor((now - then) / 1000)
    if (diff < 60) return `${diff}s ago`
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
    return then.toLocaleString()
}

const lastCheckedFormatted = computed(() => {
    if (!lastChecked.value) return ''
    // Example: "Aug 7, 2025, 4:30:14 PM"
    return new Intl.DateTimeFormat('en-US', {
        dateStyle: 'medium',
        timeStyle: 'medium'
    }).format(lastChecked.value)
})
const lastCheckedAgo = computed(() => {
    if (!lastChecked.value) return ''
    const now = new Date()
    const diffMs = now - lastChecked.value
    const diffSec = Math.floor(diffMs / 1000)
    const diffMin = Math.floor(diffSec / 60)
    const diffHour = Math.floor(diffMin / 60)
    if (diffMin < 1) return 'Just now.'
    if (diffMin < 60) return `${diffMin} minute${diffMin === 1 ? '' : 's'} ago.`
    if (diffHour < 24) return `${diffHour} hour${diffHour === 1 ? '' : 's'} ago.`
    return lastChecked.value.toLocaleString() // fallback to full date/time
})
onMounted(getStatus)
</script>
<style scoped>
/* Extra hover, responsive, or icon tweaks can go here if you like */
</style>
