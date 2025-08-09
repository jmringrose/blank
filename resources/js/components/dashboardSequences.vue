<template>
    <div class="max-w-md mx-auto bg-base-100 rounded-xl shadow-md overflow-hidden border text-gray-700 dark:text-gray-300 p-4 mt-12 align-middle">
        <h1 class="text-xl font-bold">Sequences Dashboard</h1>
        <div class="my-4">
            <p class="text-lg">Total Sequences: <b>{{ summary.total }}</b>
                <button
                    :disabled="isLoading"
                    class="ml-3 px-3 py-2 bg-blue-100 hover:bg-blue-200 rounded text-blue-700 text-sm float-right"
                    @click="fetchSummary"
                >
                    <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                    Refresh
                </button>
            </p>
            <div v-if="lastChecked" class="text-xs text-gray-500 mb-2 mt-2">
                Last checked: {{ lastCheckedFormatted }} ({{ lastCheckedAgo }})
            </div>
        </div>

        <div>
            <table class="min-w-96 border border-gray-200 mx-auto">
                <thead>
                <tr class="bg-base-200">
                    <th class="px-2 py-1 border w-1/2">Step</th>
                    <th class="px-2 py-1 border">Count</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(count, step) in summary.steps" :key="step" class="bg-neutral-700">
                    <td class="px-2 py-1 border">{{ step }}</td>
                    <td class="px-2 py-1 border">{{ count }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


    <div class="max-w-md mx-auto bg-base-100 rounded-xl shadow-md overflow-hidden border text-gray-200 p-4 mt-12 align-middle">

        <div class="flex px-4 mb-6">

            <div class="text-sm">
            <component
                :is="queueStatus.running ? 'LucideCheckCircle' : 'LucideXCircle'"
                :class="queueStatus.running ? 'text-green-500' : 'text-red-500'"
            />
           </div>

            <div class="font-bold text-md">
                Queue Worker: <span :class="queueStatus.running ? 'text-green-600' : 'text-red-600'">{{ queueStatus.running ? 'Running' : 'Not Running' }}</span>
            </div>

            <div class="flex-1 text-right w-32">
            <button
                class="px-3 py-2 bg-blue-100 hover:bg-blue-400 rounded text-blue-700 text-sm"
                @click="notifyAndRefreshSummary">
                <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                Refresh
            </button>
           </div>

        </div>

        <table class="min-w-full">
            <tbody>
            <tr>
                <td class="px-6 py-2 font-medium border-b border-gray-200">Status</td>
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
                <td class="px-6 py-2 ">Last Seen</td>
                <td class="px-6 py-2">
                    <div>{{ lastSeenFormatted }}</div>
                    <div class="text-sm text-gray-100" v-if="queueStatus.last_seen">({{ lastSeenAgo }})</div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import {computed, onMounted, onUnmounted, ref, watch} from 'vue'
import {LucideRefreshCw, LucideCheckCircle, LucideXCircle, LucidePlay, LucidePause} from 'lucide-vue-next'
import { useToast } from 'vue-toastification'
import { useTimeAgo } from '@vueuse/core'


const toast = useToast()


const lastChecked = ref(null)
const isLoading = ref(false)
let intervalId = null
const summary = ref({total: 0, steps: {}});
const queueStatus = ref({running: false, last_seen: null});

async function fetchSummary() {
    isLoading.value = true
    try {
        const res = await fetch('/api/email-sequence/summary')
        if (!res.ok) throw new Error('Failed to fetch summary')
        summary.value = await res.json()
        lastChecked.value = new Date()
    } catch (error) {
        console.error('Error fetching summary:', error)
        toast.error('Failed to fetch summary')
    } finally {
        isLoading.value = false
    }
}

async function notifyAndRefreshSummary() {
    if (typeof isLoading !== 'undefined' && isLoading.value) return

    let close
    try {
        close = toast.info('Refreshing summary…', { timeout: 1200 })
        await fetchSummary()
        toast.success('Summary refreshed')
    } catch (e) {
        toast.error('Failed to refresh summary')
        // optionally: console.error(e)
    } finally {
        if (typeof close === 'function') close()
    }
}

async function getStatus() {
    try {
        const res = await fetch('/api/health/queue?ts=' + Date.now())
        if (!res.ok) throw new Error('Failed to fetch queue status')
        queueStatus.value = await res.json()
    } catch (error) {
        console.error('Error fetching queue status:', error)
        toast.error('Failed to fetch queue status')
    }
}

onMounted(() => {
    fetchSummary()
    getStatus()
    intervalId = setInterval(() => {
        fetchSummary()
        getStatus()
    }, 2 * 60 * 1000) // every 2 minutes
})

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId)
})

// Unified date formatting function
function formatHumanDate(date) {
    if (!date) return 'Never'
    const dateObj = typeof date === 'string' ? new Date(date) : date
    return dateObj.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

// Keep a reactive Date for last_seen
const lastSeenDate = ref(null)

// Update when the backend value changes
watch(
    () => queueStatus.value.last_seen,
    (v) => {
        lastSeenDate.value = v ? new Date(v) : null
    },
    { immediate: true }
)

// Human-readable relative time, auto-updates
const lastSeenAgo = useTimeAgo(lastSeenDate)
const lastCheckedAgo = useTimeAgo(lastChecked)

// Formatted absolute dates
const lastSeenFormatted = computed(() => formatHumanDate(queueStatus.value.last_seen))
const lastCheckedFormatted = computed(() => formatHumanDate(lastChecked.value))

</script>
<style scoped>
</style>
