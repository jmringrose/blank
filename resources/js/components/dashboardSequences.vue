<template>
    <div class="container mt-4 max-w-5xl mx-auto ark:text-gray-100 bg-base-300 p-1 md:p-2 rounded-lg shadow-lg border">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <!-- WordPress Forms -->
            <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500  p-4">
                <div class="flex justify-between items-center mb-2">
                    <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100">Pre-Trip Survey Forms</h1>
                    <div class="flex gap-2">
                        <button
                            :disabled="isLoading"
                            class="px-3 py-2 bg-blue-400 hover:bg-blue-200 rounded text-blue-700 text-sm"
                            @click="fetchFormCount"
                        >
                            <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                            Refresh
                        </button>
                        <button
                            v-if="pollingPaused"
                            class="px-3 py-2 bg-green-400 hover:bg-green-200 rounded text-green-700 text-sm"
                            @click="restartPolling"
                        >
                            ▶ Restart
                        </button>
                    </div>
                </div>
                <div class="my-2">
                    <p class="text-lg">Total Form Entries: <b>{{ formCount.total }}</b>
                    </p>
                </div>
                <div class="mt-2">
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
            <!-- Queue Status -->
            <div class="bg-base-200 rounded-xl shadow-md border border-stone-500 p-4">
                <div class="flex px-4 mb-2">
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
                            <div v-if="queueStatus.last_seen" class="text-sm text-gray-100">({{ lastSeenAgo }})</div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- Sequences Dashboard -->
            <div class="bg-base-200 rounded-xl shadow-md border border-stone-500 p-4">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100">Pre Sales
                        Marketing Sequences</h1>
                    <div class="flex gap-2">
                        <button
                            :disabled="isLoading"
                            class="px-3 py-2 bg-blue-400 hover:bg-blue-200 rounded text-blue-700 text-sm"
                            @click="fetchSummary">
                            <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                            Refresh
                        </button>
                        <button
                            v-if="pollingPaused"
                            class="px-3 py-2 bg-green-400 hover:bg-green-200 rounded text-green-700 text-sm"
                            @click="restartPolling"
                        >
                            ▶ Restart
                        </button>
                    </div>
                </div>
                <div class="my-4">
                    <p class="text-lg">Total Sequences: <b>{{ summary.total }}</b>
                    </p>
                    <p class="text-sm text-gray-400">Next send: <b>{{ nextMarketingSend || 'None scheduled' }}</b></p>
                    <div v-if="lastChecked" class="text-xs text-gray-200 mb-2 mt-2">
                        Last checked: {{ lastCheckedFormatted }} ({{ lastCheckedAgo }})
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                        <tr class="">
                            <th class="px-2 py-1 ">Step</th>
                            <th class="px-2 py-1 ">Count</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(count, step) in summary.steps" :key="step">
                            <td class="px-2 py-1 text-sm">{{ getStepName(step) }}</td>
                            <td class="px-2 py-1  text-sm">{{ count }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Newsletter Sequences -->
            <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-50 border-stone-500  p-4">
                <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Customer Update Newsletter Sequences</h1>
                <p class="text-sm text-gray-400 mb-4">Next send: <b>{{ nextNewsletterSend || 'None scheduled' }}</b></p>
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
                            <tr v-for="user in newsletterSummary" :key="user.id">
                                <td class="text-sm">{{ user.first }} {{ user.last }}</td>
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
                    <a class="btn btn-primary btn-sm w-full" href="/marketing-steps">
                        ✏️ Edit Marketing Emails
                    </a>
                    <a v-for="step in marketingSteps" :key="step.order" :href="`/preview/marketing/${step.order}`" class="btn btn-outline border-gray-500 btn-sm w-full flex justify-between items-center" target="_blank">
                        <span>📧 Step {{ step.order }} - {{ step.title }}</span>
                        <span v-if="step.draft" class="badge badge-warning badge-sm">Draft</span>
                    </a>
                </div>
            </div>
            <!-- Newsletter Emails -->
            <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500  p-4">
                <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Newsletter Emails</h1>
                <div class="space-y-2">
                    <a class="btn btn-primary btn-sm w-full" href="/newsletter-steps">
                        ✏️ Edit Newsletters
                    </a>
                    <a v-for="step in newsletterSteps" :key="step.order" :href="`/preview/newsletter/${step.order}`" class="btn btn-outline border-gray-500 btn-sm w-full flex justify-between items-center" target="_blank">
                        <span>📰 Step {{ step.order }} - {{ step.title }}</span>
                        <span v-if="step.draft" class="badge badge-warning badge-sm">Draft</span>
                    </a>
                </div>
            </div>
            <!-- Recent Email Activity -->
            <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500 p-4">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100">Recent Email Activity</h1>
                    <button
                        class="px-3 py-2 bg-blue-400 hover:bg-blue-200 rounded text-blue-700 text-sm"
                        @click="fetchEmailLogs">
                        <LucideRefreshCw class="inline w-4 h-4 mr-1"/>
                        Refresh
                    </button>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <div v-for="log in emailLogs" :key="log.time" :class="[
                    'text-xs p-2 rounded',
                    log.what.includes('Newsletter') ? 'bg-blue-100 dark:bg-blue-900' : 'bg-base-300'
                ]">
                        <div class="font-semibold">{{ log.who }} ({{ log.email }})</div>
                        <div class="text-gray-400">{{ log.what }} • {{ log.when }}</div>
                    </div>
                    <div v-if="emailLogs.length === 0" class="text-gray-400 text-sm">No recent email activity</div>
                </div>
            </div>
            <!-- Links -->
            <div class="bg-base-200 rounded-xl shadow-md border text-gray-700 dark:text-gray-100 border-stone-500 p-4">
                <h1 class="text-lg font-bold text-gray-700 dark:text-gray-100 mb-4">Links</h1>
                <div class="space-y-2">
                    <button
                        :disabled="testEmailLoading"
                        class="btn btn-primary btn-sm w-full"
                        type="button"
                        @click="sendTestEmail"
                    >
                        <LucideRefreshCw v-if="testEmailLoading" class="inline w-4 h-4 mr-1 animate-spin"/>
                        <span v-else class="material-symbols-outlined text-sm mr-1">email</span>
                        {{ testEmailLoading ? 'Sending...' : 'Send Test Email' }}
                    </button>
                    <a
                        class="btn btn-primary btn-sm w-full"
                        href="/log-viewer"
                        target="_blank"
                    >
                        <span class="material-symbols-outlined text-sm mr-1">open_in_new</span>
                        See Logs
                    </a>
                    <a
                        class="btn btn-primary btn-sm w-full"
                        href="https://www.realcoolphototours.com"
                        target="_blank"
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
import {computed, onMounted, onUnmounted, ref, watch} from 'vue'
import axios from 'axios'
import {useToast} from 'vue-toastification'
import {useTimeAgo} from '@vueuse/core'
import {LucideRefreshCw} from 'lucide-vue-next'
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
const summary = ref({total: 0, steps: {}})
const getStepName = (step) => {
    if (step == 0) return 'Unsubscribed'
    const marketingStep = marketingSteps.value.find(s => s.order == step)
    return marketingStep ? marketingStep.title : `Step ${step}`
}
const queueStatus = ref({running: false, last_seen: null})
const formCount = ref({total: 0})
const userFormsSummary = ref([])
const newsletterSummary = ref([])
const newsletterSteps = ref([])
const marketingSteps = ref([])
const lastChecked = ref(null)
const isLoading = ref(false)
const testEmailLoading = ref(false)
const nextMarketingSend = ref(null)
const nextNewsletterSend = ref(null)
const emailLogs = ref([])
let intervalId = null
const pollCount = ref(0)
const maxPolls = 20
const pollingPaused = ref(false)

// --- Load summary ---
async function fetchSummary(silent = false) {
    isLoading.value = true
    try {
        const {data} = await api.get('/sequences/data')
        // Transform data to summary format
        const total = data.length
        const steps = {}
        data.forEach(seq => {
            steps[seq.current_step] = (steps[seq.current_step] || 0) + 1
        })
        // Find next send date
        const nextSends = data.filter(seq => seq.next_send_at && seq.current_step > 0)
            .map(seq => new Date(seq.next_send_at))
            .sort((a, b) => a - b)
        nextMarketingSend.value = nextSends.length > 0 ? nextSends[0].toLocaleString() : null
        summary.value = {total, steps}
        lastChecked.value = new Date()
        if (!silent) {
            toast.success('Marketing sequences refreshed')
        }
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
        const {data} = await api.get('/health/queue', {
            params: {ts: Date.now()}
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
        const {data} = await api.get('/forms/count')
        formCount.value = data
    } catch (e) {
        console.error('Error fetching form count:', e)
        toast.error('Failed to fetch form count')
    }
}

// --- Load user forms summary ---
async function fetchUserFormsSummary() {
    try {
        const {data} = await api.get('/forms/user-summary')
        userFormsSummary.value = data
    } catch (e) {
        console.error('Error fetching user forms summary:', e)
        toast.error('Failed to fetch user forms summary')
    }
}

// --- Load newsletter summary ---
async function fetchNewsletterSummary() {
    try {
        const {data} = await api.get('/newsletter-sequences/data')
        newsletterSummary.value = data
        // Find next newsletter send date
        const nextSends = data.filter(seq => seq.next_send_at && seq.current_step > 0)
            .map(seq => new Date(seq.next_send_at))
            .sort((a, b) => a - b)
        nextNewsletterSend.value = nextSends.length > 0 ? nextSends[0].toLocaleString() : null
    } catch (e) {
        console.error('Error fetching newsletter summary:', e)
        toast.error('Failed to fetch newsletter summary')
    }
}

// --- Load newsletter steps ---
async function fetchNewsletterSteps() {
    try {
        const {data} = await api.get(window.location.origin + '/newsletter-steps/data')
        newsletterSteps.value = data.sort((a, b) => a.order - b.order)
    } catch (e) {
        console.error('Error fetching newsletter steps:', e)
    }
}

// --- Load marketing steps ---
async function fetchMarketingSteps() {
    try {
        const {data} = await api.get(window.location.origin + '/marketing-steps/data')
        marketingSteps.value = data.sort((a, b) => a.order - b.order)
    } catch (e) {
        console.error('Error fetching marketing steps:', e)
    }
}

// --- Load email logs ---
async function fetchEmailLogs() {
    try {
        const {data} = await api.get('/email-logs')
        emailLogs.value = data
    } catch (e) {
        console.error('Error fetching email logs:', e)
    }
}

// --- Send test email ---
async function sendTestEmail() {
    testEmailLoading.value = true
    try {
        await api.post('/send-simple-test-email')
        toast.success('Test email sent successfully!')
        // Refresh logs after sending
        setTimeout(fetchEmailLogs, 1000)
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
        close = toast.info('Refreshing queue status…', {timeout: 1200})
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
    v => {
        lastSeenDate.value = v ? new Date(v) : null
    },
    {immediate: true}
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
        await fetchSummary(true) // Silent on initial load
        await getStatus()
        await fetchFormCount()
        await fetchUserFormsSummary()
        await fetchNewsletterSummary()
        await fetchNewsletterSteps()
        await fetchMarketingSteps()
        await fetchEmailLogs()
    } catch (e) {
        error.value = e?.message || 'Failed to load data'
    } finally {
        loading.value = false
    }
}

function startPolling() {
    if (intervalId) clearInterval(intervalId)
    pollCount.value = 0
    pollingPaused.value = false
    
    intervalId = setInterval(() => {
        pollCount.value++
        
        if (pollCount.value >= maxPolls) {
            clearInterval(intervalId)
            pollingPaused.value = true
            toast.warning(`Auto-refresh paused after ${maxPolls} polls. Click restart to continue.`)
            return
        }
        
        fetchSummary(true) // Silent to avoid toast spam
        getStatus()
        fetchNewsletterSummary()
        fetchNewsletterSteps()
        fetchMarketingSteps()
        fetchEmailLogs()
    }, 2 * 60 * 1000)
}

function restartPolling() {
    startPolling()
    toast.success('Auto-refresh restarted')
}

onMounted(() => {
    initialLoad()
    startPolling()
})
onUnmounted(() => {
    if (intervalId) clearInterval(intervalId)
})
</script>
<style scoped>
th {
    border: none; /* remove all borders */
    border-bottom: 1px solid #625f5f; /* add only bottom border */
    padding: 8px;
    text-align: left;
}
</style>
