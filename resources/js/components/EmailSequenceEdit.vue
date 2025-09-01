<template>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Email Sequence</h1>


        <template>
            <div>
                <div v-if="loading">Loading…</div>
                <div v-else-if="error">Error: {{ error }}</div>
                <pre v-else>{{ me }}</pre>
            </div>
        </template>


        <div v-if="loading" class="flex justify-center my-4">
            <div class="loading loading-spinner loading-lg"></div>
        </div>

        <form v-else @submit.prevent="updateEmailSequence" class="space-y-4">
            <div>
                <label for="first" class="block text-sm font-medium text-base-content mb-1">
                    First Name
                </label>
                <input
                    id="first"
                    v-model="form.first"
                    type="text"
                    required
                    class="input input-bordered w-full"
                />
                <div v-if="errors.first" class="text-error text-sm mt-1">
                    {{ errors.first }}
                </div>
            </div>

            <div>
                <label for="last" class="block text-sm font-medium text-base-content mb-1">
                    Last Name
                </label>
                <input
                    id="last"
                    v-model="form.last"
                    type="text"
                    required
                    class="input input-bordered w-full"
                />
                <div v-if="errors.last" class="text-error text-sm mt-1">
                    {{ errors.last }}
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-base-content mb-1">
                    Email
                </label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="input input-bordered w-full"
                />
                <div v-if="errors.email" class="text-error text-sm mt-1">
                    {{ errors.email }}
                </div>
            </div>

            <div>
                <label for="current_step" class="block text-sm font-medium text-base-content mb-1">
                    Current Step
                </label>
                <input
                    id="current_step"
                    v-model.number="form.current_step"
                    type="number"
                    min="0"
                    required
                    class="input input-bordered w-full"
                />
                <div v-if="errors.current_step" class="text-error text-sm mt-1">
                    {{ errors.current_step }}
                </div>
            </div>

            <div>
                <label for="unsub_token" class="block text-sm font-medium text-base-content mb-1">
                    Unsubscribe Token
                </label>
                <input
                    id="unsub_token"
                    v-model="form.unsub_token"
                    type="text"
                    class="input input-bordered w-full"
                />
                <div v-if="errors.unsub_token" class="text-error text-sm mt-1">
                    {{ errors.unsub_token }}
                </div>
            </div>

            <div>
                <label for="next_send_at" class="block text-sm font-medium text-base-content mb-1">
                    Next Send At
                </label>
                <input
                    id="next_send_at"
                    v-model="form.next_send_at"
                    type="datetime-local"
                    class="input input-bordered w-full"
                />
                <div v-if="errors.next_send_at" class="text-error text-sm mt-1">
                    {{ errors.next_send_at }}
                </div>
            </div>

            <div>
                <label for="ip_address" class="block text-sm font-medium text-base-content mb-1">
                    IP Address
                </label>
                <input
                    id="ip_address"
                    v-model="form.ip_address"
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="e.g., 192.168.1.1"
                />
                <div v-if="errors.ip_address" class="text-error text-sm mt-1">
                    {{ errors.ip_address }}
                </div>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-base-content mb-1">
                    Location
                </label>
                <input
                    id="location"
                    v-model="form.location"
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="e.g., New York, NY, United States"
                />
                <div v-if="errors.location" class="text-error text-sm mt-1">
                    {{ errors.location }}
                </div>
            </div>

            <div class="flex gap-4">
                <button
                    type="submit"
                    :disabled="processing"
                    class="btn btn-primary"
                >
                    {{ processing ? 'Updating...' : 'Update' }}
                </button>

                <button
                    type="button"
                    @click="goBack"
                    class="btn btn-secondary"
                >
                    Return
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref, reactive, onMounted } from 'vue';
import { useToast } from "vue-toastification";

const toast = useToast();
const error = ref('');
const me = ref(null); // or your resource
const loading = ref(true);
const processing = ref(false);
const errors = ref({});

const form = reactive({
    first: '',
    last: '',
    email: '',
    current_step: 0,
    unsub_token: '',
    next_send_at: '',
    ip_address: '',
    location: ''
});

const emailSequenceId = ref(null);

const formatDateForInput = (dateString) => {
    try {
        if (!dateString) return ''
        // Simply slice the datetime string to fit datetime-local format
        return dateString.slice(0, 16)
    } catch (error) {
        console.error('Error formatting date:', error)
        return ''
    }
}

// Get ID from URL
onMounted(() => {
    const pathParts = window.location.pathname.split('/');
    emailSequenceId.value = pathParts[pathParts.length - 2]; // assuming URL like /email-sequence/{id}/edit
    loadEmailSequence();
});

const loadEmailSequence = async () => {
    try {
        const response = await axios.get(`/getsequence/${emailSequenceId.value}`);
        const data = response.data;
        form.first = data.first || '';
        form.last = data.last || '';
        form.email = data.email || '';
        form.current_step = data.current_step || 0;
        form.unsub_token = data.unsub_token || '';
        form.next_send_at = data.next_send_at ? formatDateForInput(data.next_send_at) : '';
        form.ip_address = data.ip_address || '';
        form.location = data.location || '';

        loading.value = false;
    } catch (error) {
        console.error('Error loading email sequence:', error);
        toast.error('Failed to load email sequence');
        loading.value = false;
    }
};

const updateEmailSequence = async () => {
    processing.value = true;
    errors.value = {};

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios.put(`/updatesequence/${emailSequenceId.value}`, form, {
            headers: {
                'X-CSRF-TOKEN': csrfToken || '',
                'Content-Type': 'application/json'
            }
        });
        toast.success('Email sequence updated successfully!');

        // Optionally redirect or update form with returned data
        if (response.data.data) {
            const data = response.data.data;
            form.first = data.first;
            form.last = data.last;
            form.email = data.email;
            form.current_step = data.current_step;
            form.unsub_token = data.unsub_token;
            form.next_send_at = data.next_send_at ? formatDateForInput(data.next_send_at) : '';
            form.ip_address = data.ip_address || '';
            form.location = data.location || '';
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            toast.error('Failed to update email sequence');
        }
        console.error('Update error:', error);
    } finally {
        processing.value = false;
    }
};

const goBack = () => {
    window.location.href = '/sequences';
};
</script>
