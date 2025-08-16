<template>
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
            
            <div class="flex justify-end space-x-4">
                <a href="/newsletter-sequences" class="btn btn-secondary">Cancel</a>
                <button type="submit" :disabled="saving" class="btn btn-primary">
                    {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
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
    next_send_at: ''
})

const loadSequence = async () => {
    try {
        const { data } = await axios.get(`/newsletter-sequence/${props.sequenceId}`)
        form.value = {
            first: data.first,
            last: data.last,
            email: data.email,
            current_step: data.current_step,
            next_send_at: data.next_send_at ? data.next_send_at.slice(0, 16) : ''
        }
    } catch (err) {
        console.error('Error loading sequence:', err)
        toast.error('Failed to load sequence')
    } finally {
        loading.value = false
    }
}

const updateSequence = async () => {
    saving.value = true
    try {
        await axios.put(`/newsletter-sequence/${props.sequenceId}`, form.value)
        toast.success('Newsletter sequence updated successfully!')
        setTimeout(() => {
            window.location.href = '/newsletter-sequences'
        }, 1000)
    } catch (err) {
        console.error('Error updating sequence:', err)
        toast.error('Failed to update sequence')
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    loadSequence()
})
</script>