<template>
    <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


        <!-- Sequences Dashboard -->
        <div class="bg-base-200 rounded-xl shadow-md border border-stone-500 p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100">Pre Sales
                    Marketing Sequences</h1>
                <button
                    :disabled="isLoading"
                    class="px-3 py-2 bg-blue-400 hover:bg-blue-200 rounded text-blue-700 text-sm"
                    @click="fetchSummary">
                    <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                    Refresh
                </button>
            </div>
        <div class="my-4">
            <p class="text-lg">Total Sequences: <b>{{ summary.total }}</b>
            </p>
            <div v-if="lastChecked" class="text-xs text-gray-200 mb-2 mt-2">
                Last checked: {{ lastCheckedFormatted }} ({{ lastCheckedAgo }})
            </div>
        </div>

        <div>
            <table class="min-w-96 border border-gray-600 mx-auto">
                <thead>
                <tr class="bg-base-200">
                    <th class="px-2 py-1 border w-1/2">Step</th>
                    <th class="px-2 py-1 border">Count</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(count, step) in summary.steps" :key="step" class="bg-neutral-500 text-gray-100">
                    <td class="px-2 py-1 border">{{ getStepName(step) }}</td>
                    <td class="px-2 py-1 border">{{ count }}</td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>




        <!-- Queue Status -->
        <div class="bg-base-200 rounded-xl shadow-md border border-stone-500  p-4">

        <div class="flex px-4 mb-6">

            <div class="text-sm">
            <component
                :is="queueStatus.running ? 'LucideCheckCircle' : 'LucideXCircle'"
                :class="queueStatus.running ? 'text-green-500' : 'text-red-500'"
            />
           </div>

            <div class="font-bold text-md">
                <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Queue Worker: <span :class="queueStatus.running ? 'text-green-600' : 'text-red-500'">{{ queueStatus.running ? 'Running' : 'Not Running' }}</span></h1>
            </div>

            <div class="flex-1 text-right w-32">
            <button
                class="px-3 py-2 bg-blue-400 hover:bg-blue-400 rounded text-blue-700 text-sm"
                @click="notifyAndRefreshSummary">
                <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                Refresh
            </button>
           </div>

        </div>

        <table class="min-w-full">
            <tbody>
            <tr>
                <td class="px-6 py-2 font-medium border-b border-gray-600">Status</td>
                <td class="px-6 py-2 border-b border-gray-600">
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

        <!-- WordPress Forms -->
        <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500  p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100">Pre-Trip Survey Forms</h1>
                <button
                    :disabled="isLoading"
                    class="px-3 py-2 bg-blue-400 hover:bg-blue-200 rounded text-blue-700 text-sm"
                    @click="fetchFormCount"
                >
                    <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                    Refresh
                </button>
            </div>
        <div class="my-4">
            <p class="text-lg">Total Form Entries: <b>{{ formCount.total }}</b>
            </p>
        </div>

        <div class="mt-6">
            <h3 class="text-md font-semibold mb-3">User Forms Summary</h3>
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">Forms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in userFormsSummary" :key="user.name">
                            <td class="text-sm">{{ user.name }}</td>
                            <td>
                                <span v-for="n in user.forms_completed" :key="n" class="text-green-500 mr-1">✓</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        <!-- Newsletter Sequences -->
        <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-50 border-stone-500  p-4">
            <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Customer Update Newsletter Sequences</h1>
        <div class="mt-6">
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">Step</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in newsletterSummary" :key="user.name">
                            <td class="text-sm">{{ user.name }}</td>
                            <td class="text-sm">
                                <span v-if="user.current_step === 0" class="badge badge-error badge-sm">Unsubscribed</span>
                                <span v-else>{{ user.current_step }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        <!-- Marketing Emails -->
        <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500  p-4">
            <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Marketing Emails</h1>
            <div class="space-y-2">
                <a href="/marketing-steps" class="btn btn-primary btn-sm w-full">
                    ✏️ Edit Marketing Emails
                </a>

                <a v-for="step in marketingSteps" :key="step.order" :href="`/preview/marketing/${step.order}`" target="_blank" class="btn btn-outline border-gray-500 btn-sm w-full">
                    📧 Step {{ step.order }} - {{ step.title }}
                    <span v-if="step.draft" class="badge badge-warning badge-sm ml-2">Draft</span>
                </a>

            </div>
        </div>

        <!-- Newsletter Emails -->
        <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500  p-4">
            <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Newsletter Emails</h1>
            <div class="space-y-2">
                <a href="/newsletter-steps" class="btn btn-primary btn-sm w-full">
                    ✏️ Edit Newsletters
                </a>
                <a v-for="step in newsletterSteps" :key="step.order" :href="`/preview/newsletter/${step.order}`" target="_blank" class="btn btn-outline border-gray-500 btn-sm w-full">
                    📰 Step {{ step.order }} - {{ step.title }}
                    <span v-if="step.draft" class="badge badge-warning badge-sm ml-2">Draft</span>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500 p-4">
            <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Quick Actions</h1>
            <div class="space-y-2">
                <button
                    type="button"
                    @click="sendTestEmail"
                    :disabled="testEmailLoading"
                    class="btn btn-primary btn-sm w-full"
                >
                    <LucideRefreshCw v-if="testEmailLoading" class="inline w-4 h-4 mr-1 animate-spin"/>
                    <span v-else class="material-symbols-outlined text-sm mr-1">email</span>
                    {{ testEmailLoading ? 'Sending...' : 'Send Test Email' }}
                </button>
            </div>

            <div class="space-y-2 mt-2">
                <a
                    href="/logs-viewer"
                    target="_blank"
                    class="btn btn-primary btn-sm w-full"
                >
                    <span class="material-symbols-outlined text-sm mr-1">open_in_new</span>
                    See Logs
                </a>
            </div>
        </div>



        <!-- Site Links -->
        <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500 p-4">

            <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Site Links</h1>
            <div class="space-y-2">
                <a
                    href="https://www.realcoolphototours.com"
                    target="_blank"
                    class="btn btn-primary btn-sm w-full"
                >
                    <span class="material-symbols-outlined text-sm mr-1">open_in_new</span>
                    Visit RCPT
                </a>
            </div>
        </div>


    </div>
</div>
</template>
<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { useTimeAgo } from '@vueuse/core'
import { LucideRefreshCw, LucideCheckCircle, LucideXCircle, LucidePlay, LucidePause } from 'lucide-vue-next'

// --- Auth + API instance ---
const token = typeof window !== 'undefined' ? localStorage.getItem('api_token') : null

// Set token in axios defaults
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// Use global axios instance
const api = axios

axios.interceptors.response.use(
    r => r,
    err => {
        const r = err.response
        console.log('[HTTP FAIL]', r?.status, r?.config?.url, r?.data)
        return Promise.reject(err)
    }
)

// --- State ---
const toast = useToast()
const loading = ref(true)
const error = ref('')
const summary = ref({ total: 0, steps: {} })

// Step lookup array
const stepNames = {
    0: 'Unsubscribed',
    1: 'Introduction',
    2: 'Why Costa Rica',
    3: 'TBD',
    4: 'TBD',
    5: 'TBD',
    6: 'TBD',
    7: 'TBD',
    8: 'TBD',
    9: 'TBD',
    10: 'TBD'
}

const getStepName = (step) => stepNames[step] || `Step ${step}`
const queueStatus = ref({ running: false, last_seen: null })
const formCount = ref({ total: 0 })
const userFormsSummary = ref([])
const newsletterSummary = ref([])
const newsletterSteps = ref([])
const marketingSteps = ref([])
const lastChecked = ref(null)
const isLoading = ref(false)
const testEmailLoading = ref(false)
let intervalId = null

// --- Load summary ---
async function fetchSummary() {0
    isLoading.value = true
    try {
        const { data } = await api.get('/email-sequence/summary')
        summary.value = data
        lastChecked.value = new Date()
        toast.success('Marketing sequences refreshed')
    } catch (e) {
        console.error('Error fetching summary:', e)
        toast.error('Failed to fetch summary')
    } finally {
        isLoading.value = false
    }
}

// --- Load queue status ---
async function getStatus() {
    try {
        const { data } = await api.get('/health/queue', {
            params: { ts: Date.now() }
        })
        queueStatus.value = data
    } catch (e) {
        console.error('Error fetching queue status:', e)
        toast.error('Failed to fetch queue status')
    }
}

// --- Load form count ---
async function fetchFormCount() {
    try {
        const { data } = await api.get('/forms/count')
        formCount.value = data
    } catch (e) {
        console.error('Error fetching form count:', e)
        toast.error('Failed to fetch form count')
    }
}

// --- Load user forms summary ---
async function fetchUserFormsSummary() {
    try {
        const { data } = await api.get('/forms/user-summary')
        userFormsSummary.value = data
    } catch (e) {
        console.error('Error fetching user forms summary:', e)
        toast.error('Failed to fetch user forms summary')
    }
}

// --- Load newsletter summary ---
async function fetchNewsletterSummary() {
    try {
        const { data } = await api.get('/newsletter-sequences/summary')
        newsletterSummary.value = data
    } catch (e) {
        console.error('Error fetching newsletter summary:', e)
        toast.error('Failed to fetch newsletter summary')
    }
}

// --- Load newsletter steps ---
async function fetchNewsletterSteps() {
    try {
        const { data } = await api.get(window.location.origin + '/newsletter-steps/data')
        newsletterSteps.value = data.sort((a, b) => a.order - b.order)
    } catch (e) {
        console.error('Error fetching newsletter steps:', e)
    }
}

// --- Load marketing steps ---
async function fetchMarketingSteps() {
    try {
        const { data } = await api.get(window.location.origin + '/marketing-steps/data')
        marketingSteps.value = data.sort((a, b) => a.order - b.order)
    } catch (e) {
        console.error('Error fetching marketing steps:', e)
    }
}

// --- Send test email ---
async function sendTestEmail() {
    testEmailLoading.value = true
    try {
        await api.post('/send-test-email')
        toast.success('Test email sent successfully!')
    } catch (e) {
        console.error('Error sending test email:', e)
        toast.error('Failed to send test email')
    } finally {
        testEmailLoading.value = false
    }
}

// --- Refresh button ---
async function notifyAndRefreshSummary() {
    if (isLoading.value) return
    let close
    try {
        close = toast.info('Refreshing queue status…', { timeout: 1200 })
        await getStatus()
        toast.success('Queue status refreshed')
    } catch {
        toast.error('Failed to refresh queue status')
    } finally {
        if (typeof close === 'function') close()
    }
}

// --- Dates + watchers ---
function formatHumanDate(date) {
    if (!date) return 'Never'
    const d = typeof date === 'string' ? new Date(date) : date
    return d.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

const lastSeenDate = ref(null)
watch(
    () => queueStatus.value.last_seen,
    v => { lastSeenDate.value = v ? new Date(v) : null },
    { immediate: true }
)

const lastSeenAgo = useTimeAgo(lastSeenDate)
const lastCheckedAgo = useTimeAgo(lastChecked)
const lastSeenFormatted = computed(() => formatHumanDate(queueStatus.value.last_seen))
const lastCheckedFormatted = computed(() => formatHumanDate(lastChecked.value))

// --- Initial load ---
async function initialLoad() {
    loading.value = true
    error.value = ''
    try {
        await fetchSummary()
        await getStatus()
        await fetchFormCount()
        await fetchUserFormsSummary()
        await fetchNewsletterSummary()
        await fetchNewsletterSteps()
        await fetchMarketingSteps()
    } catch (e) {
        error.value = e?.message || 'Failed to load data'
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    initialLoad()
    intervalId = setInterval(() => {
        fetchSummary()
        getStatus()
        fetchFormCount()
        fetchUserFormsSummary()
        fetchNewsletterSummary()
        fetchNewsletterSteps()
        fetchMarketingSteps()
    }, 2 * 60 * 1000)
})

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId)
})
</script>
<style scoped>
</style>
