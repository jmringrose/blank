<template>
    <div class="container mx-auto">
        <div class="max-w-2xl mx-auto">
        <div v-if="loading" class="flex justify-center my-4">
            <div class="loading loading-spinner loading-lg"></div>
        </div>

        <form v-else @submit.prevent="updateSequence" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">First Name</label>
                    <input v-model="form.first" type="text" required
                           class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Last Name</label>
                    <input v-model="form.last" type="text" required
                           class="input input-bordered w-full" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input v-model="form.email" type="email" required
                       class="input input-bordered w-full" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Current Step</label>
                    <input v-model.number="form.current_step" type="number" min="0" required
                           class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Next Send At</label>
                    <input v-model="form.next_send_at" type="datetime-local"
                           class="input input-bordered w-full" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Unsubscribe Token</label>
                <div class="flex gap-2">
                    <input v-model="form.unsub_token" type="text"
                           class="input input-bordered flex-1" />
                    <button type="button" @click="generateToken" class="btn btn-secondary">
                        Generate
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Tour Date</label>
                    <input v-model="form.tour_date" type="date"
                           class="input input-bordered w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tour Date String</label>
                    <input v-model="form.tour_date_str" type="text"
                           placeholder="Auto-generated from tour date"
                           readonly
                           class="input input-bordered w-full bg-gray-100" />
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/newsletter-sequences" class="btn btn-secondary">Cancel</a>
                <button type="submit" :disabled="saving" class="btn btn-primary">
                    {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const props = defineProps({
    sequenceId: {
        type: Number,
        required: true
    }
})

const toast = useToast()
const loading = ref(true)
const saving = ref(false)

const form = ref({
    first: '',
    last: '',
    email: '',
    current_step: 1,
    next_send_at: '',
    unsub_token: '',
    tour_date: '',
    tour_date_str: ''
})

const loadSequence = async () => {
    try {
        const { data } = await axios.get(`/newsletter-sequence/${props.sequenceId}`)
        form.value = {
            first: data.first,
            last: data.last,
            email: data.email,
            current_step: data.current_step,
            next_send_at: data.next_send_at ? formatDateForInput(data.next_send_at) : '',
            unsub_token: data.unsub_token || '',
            tour_date: data.tour_date ? data.tour_date.split('T')[0] : '',
            tour_date_str: data.tour_date_str || ''
        }
    } catch (err) {
        console.error('Error loading sequence:', err)
        toast.error('Failed to load sequence')
    } finally {
        loading.value = false
    }
}

const formatDateForInput = (dateString) => {
    try {
        if (!dateString) return ''
        // Handle database format "2025-09-01 14:11:00" -> "2025-09-01T14:11"
        if (dateString.includes(' ')) {
            return dateString.replace(' ', 'T').slice(0, 16)
        }
        // Handle other formats
        return dateString.slice(0, 16)
    } catch (error) {
        console.error('Error formatting date:', error)
        return ''
    }
}

const generateToken = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    let token = ''
    for (let i = 0; i < 32; i++) {
        token += chars.charAt(Math.floor(Math.random() * chars.length))
    }
    form.value.unsub_token = token
    toast.success('New unsubscribe token generated')
}

const updateSequence = async () => {
    saving.value = true
    try {
        const response = await axios.put(`/newsletter-sequence/${props.sequenceId}`, form.value)
        toast.success('Newsletter sequence updated successfully!')
        setTimeout(() => {
            window.location.href = '/newsletter-sequences'
        }, 1000)
    } catch (err) {
        console.error('Error updating sequence:', err)
        console.error('Error response:', err.response?.data)
        toast.error('Failed to update sequence: ' + (err.response?.data?.message || err.message))
    } finally {
        saving.value = false
    }
}

// Watch for tour_date changes and auto-format tour_date_str
watch(() => form.value.tour_date, (newDate) => {
    if (newDate) {
        const [year, month, day] = newDate.split('-')
        const date = new Date(year, month - 1, day)
        const dayNum = date.getDate()
        const monthStr = date.toLocaleDateString('en-US', { month: 'short' })
        const yearStr = date.getFullYear()
        form.value.tour_date_str = `${dayNum} ${monthStr} ${yearStr}`
    } else {
        form.value.tour_date_str = ''
    }
})

onMounted(() => {
    loadSequence()
})
</script>
