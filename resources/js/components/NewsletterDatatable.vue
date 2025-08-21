<template>

 <div class="container max-w-6xl">
    <div class="flex mb-4 text-base-content">
        <span class="mr-2 mt-2 text-sm">Search specific field:</span>
        <select v-model="searchField" class="ml-2 mr-4 select bg-secondary select-sm w-36">
            <option selected value="">Pick Something</option>
            <option v-for="header in headers" :key="header.value" :value="header.value">
                {{ header.text }}
            </option>
        </select>
        <div class="mt-2 text-sm">Search value:</div>
        <div><input v-model="searchValue" class="ml-2 w-32 input bg-base-200 input-sm" type="text"></div>
        <div class="mr-2 ml-2">
            <button class="btn btn-secondary btn-sm w-32" @click="clearSearch">
                Clear Search
            </button>
        </div>
    </div>

    <EasyDataTable
        :headers="headers"
        :items="items"
        :rows-per-page="15"
        :search-field="searchField"
        :search-value="searchValue"
        alternating
        body-text-direction="left"
        header-text-direction="left"
        sort-by="first"
        sort-type="asc"
        table-class-name="customize-table"
        theme-color="#1d90ff"
        hide-rows-per-page
    >
        <template #item-next_send_at="{ next_send_at }">
            <div class="text-center">
                <span :title="next_send_at ? new Date(next_send_at).toLocaleString() : 'Not scheduled'">
                    {{ formatDate(next_send_at) }}
                </span>
            </div>
        </template>

        <template #item-tour_date="{ tour_date }">
            <div class="text-center">
                {{ tour_date || 'Not Set' }}
            </div>
        </template>

        <template #item-unsub_token="{ unsub_token }">
            <span
                :class="(unsub_token && unsub_token.length > 0) ? 'text-green-600' : 'text-red-600'"
                :title="(unsub_token && unsub_token.length > 0) ? 'Unsubscribe token exists' : 'No unsubscribe token'"
                class="font-bold text-lg"
            >
                {{ (unsub_token && unsub_token.length > 0) ? '✓' : '✗' }}
            </span>
        </template>

        <template #item-actions="item">
            <div class="flex justify-center">
                <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="confirmDeleteItem(item)">
                    <span class="!text-base material-symbols-outlined">delete</span>
                </button>
                <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="editItem(item)">
                    <span class="!text-base material-symbols-outlined">edit</span>
                </button>
            </div>
        </template>
        <template #pagination="{ prevPage, nextPage, isFirstPage, isLastPage }">
            <div class="custom-pagination">
                <button
                    :disabled="isFirstPage"
                    class="rounded-pagination-button"
                    @click="prevPage"
                >
                    Prev
                </button>
                <button
                    :disabled="isLastPage"
                    class="rounded-pagination-button"
                    @click="nextPage"
                >
                    Next
                </button>
            </div>
        </template>
    </EasyDataTable>

    <!-- Add User Modal -->
    <div v-if="showAddUserModal" class="modal modal-open">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-4">Add Newsletter User</h3>

            <form @submit.prevent="addUser" class="space-y-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">First Name</span>
                    </label>
                    <input
                        v-model="userFormData.first"
                        type="text"
                        class="input input-bordered w-full"
                        placeholder="Enter first name"
                        required
                    />
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Last Name</span>
                    </label>
                    <input
                        v-model="userFormData.last"
                        type="text"
                        class="input input-bordered w-full"
                        placeholder="Enter last name"
                        required
                    />
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Email</span>
                    </label>
                    <input
                        v-model="userFormData.email"
                        type="email"
                        class="input input-bordered w-full"
                        placeholder="Enter email address"
                        required
                    />
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-medium">Tour Date (optional)</span>
                    </label>
                    <input
                        v-model="userFormData.tour_date"
                        type="date"
                        class="input input-bordered w-full"
                    />
                </div>
            </form>

            <div class="modal-action mt-6">
                <button @click="addUser" class="btn btn-primary">
                    <span class="material-symbols-outlined mr-2">person_add</span>
                    Add User
                </button>
                <button @click="closeAddUserModal" class="btn btn-ghost">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">{{ deleteModalMessage }}</p>
            <div class="modal-action">
                <button class="btn btn-error" @click="proceedWithDelete">Yes, Delete</button>
                <button class="btn" @click="cancelDelete">Cancel</button>
            </div>
        </div>
    </div>
 </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

const headers = [
    {text: "First", value: "first", sortable: true, width: 100},
    {text: "Last", value: "last", sortable: true, width: 100},
    {text: "Email", value: "email", sortable: true, width: 200},
    {text: "Step", value: "current_step", sortable: true, width: 120},
    {text: "Next Send", value: "next_send_at", sortable: true, width: 150},
    {text: "Tour", value: "tour_date_str", sortable: true, width: 150},
    {text: "Unsub", value: "unsub_token", sortable: true, width: 80},
    {text: "Actions", value: "actions", width: 120}
]

const searchField = ref("")
const searchValue = ref("")
const itemsSelected = ref([])
const items = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const showAddUserModal = ref(false)
const deleteModalMessage = ref("")
const pendingDeleteAction = ref(null)

const userFormData = ref({
    first: '',
    last: '',
    email: '',
    tour_date: ''
})

const formatDate = (dateString) => {
    if (!dateString) return 'Not scheduled'
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

const getHistory = () => {
    loading.value = true
    axios.get('/newsletter-sequences/data')
        .then((res) => {
            items.value = res.data
        })
        .catch((err) => {
            console.error('Error fetching data:', err)
            toast.error("Failed to load data")
        })
        .finally(() => {
            loading.value = false
        })
}

const editItem = (item) => {
    window.location.href = `/newsletter-sequence/${item.id}/edit`
}

const confirmDeleteItem = (item) => {
    deleteModalMessage.value = `Are you sure you want to delete the record for "${item.email}"?`
    pendingDeleteAction.value = () => deleteItem(item)
    showDeleteModal.value = true
}

const confirmDeleteSelected = () => {
    if (itemsSelected.value.length === 0) return
    const count = itemsSelected.value.length
    deleteModalMessage.value = `Are you sure you want to delete ${count} selected record${count > 1 ? 's' : ''}?`
    pendingDeleteAction.value = () => deleteSelected()
    showDeleteModal.value = true
}

const deleteItem = async (item) => {
    try {
        await axios.delete(`/newsletter-sequence/${item.id}`)
        items.value = items.value.filter(i => i.id !== item.id)
        toast.success(`Deleted: ${item.email}`)
    } catch (err) {
        console.error('Delete failed:', err)
        toast.error('Failed to delete item')
    }
}

const deleteSelected = async () => {
    if (itemsSelected.value.length > 0) {
        const selectedIds = itemsSelected.value.map(item => item.id)
        try {
            await axios.post('/newsletter-sequence/bulk-delete', { ids: selectedIds })
            items.value = items.value.filter(item => !selectedIds.includes(item.id))
            itemsSelected.value = []
            toast.success(`${selectedIds.length} items deleted`)
        } catch (err) {
            console.error('Bulk delete failed:', err)
            toast.error('Failed to delete selected items')
        }
    }
}

const proceedWithDelete = async () => {
    if (pendingDeleteAction.value) {
        await pendingDeleteAction.value()
    }
    cancelDelete()
}

const cancelDelete = () => {
    showDeleteModal.value = false
    deleteModalMessage.value = ""
    pendingDeleteAction.value = null
}

const addUser = async () => {
    try {
        const userData = {
            ...userFormData.value,
            current_step: 1
        }
        console.log('Sending user data:', userData)
        const response = await axios.post('/newsletter-sequences', userData)
        console.log('Response:', response.data)
        toast.success('User added to newsletter successfully')
        closeAddUserModal()
        getHistory() // Refresh the list
    } catch (error) {
        console.error('Error adding user:', error.response?.data || error.message)
        toast.error('Failed to add user: ' + (error.response?.data?.message || error.message))
    }
}

const closeAddUserModal = () => {
    showAddUserModal.value = false
    userFormData.value = { first: '', last: '', email: '', tour_date: '' }
}

const clearSearch = () => {
    searchField.value = ""
    searchValue.value = ""
}

onMounted(() => {
    getHistory()
    
    // Add click handler for the Add User button
    document.getElementById('addUserBtn')?.addEventListener('click', () => {
        showAddUserModal.value = true
    })
})
</script>

<style scoped>
.custom-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px 0;
}

.rounded-pagination-button {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    margin: 0 5px;
    cursor: pointer;
    background-color: #4c5d7a;
    color: white;
    border: none;
    font-weight: bold;
}

.rounded-pagination-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.rounded-pagination-button:not(:disabled):hover {
    background-color: #66a0b7;
}

.customize-table {
    --easy-table-border: 1px solid #445269;
    --easy-table-row-border: 1px solid #445269;

    --easy-table-header-font-size: 14px;
    --easy-table-header-height: 40px;
    --easy-table-header-font-color: #c1cad4;
    --easy-table-header-background-color: #2a3444;

    --easy-table-header-item-padding: 10px 10px;

    --easy-table-body-even-row-font-color: #ffffff;
    --easy-table-body-even-row-background-color: #33435d;

    --easy-table-body-row-font-color: #c0c7d2;
    --easy-table-body-row-background-color: #2d3a4f;
    --easy-table-body-row-height: 20px;
    --easy-table-body-row-font-size: 14px;

    --easy-table-body-row-hover-font-color: #c9cfd9 !important;
    --easy-table-body-row-hover-background-color: #626d80 !important;
    --easy-table-body-item-padding: 10px 15px;

    --easy-table-footer-background-color: #2d3a4f;
    --easy-table-footer-font-color: #c0c7d2;
    --easy-table-footer-font-size: 14px;
    --easy-table-footer-padding: 0px 10px;
    --easy-table-footer-height: 60px;

    --easy-table-rows-per-page-selector-width: 70px;
    --easy-table-rows-per-page-selector-option-padding: 10px;
    --easy-table-rows-per-page-selector-z-index: 1;

    --easy-table-scrollbar-track-color: #2d3a4f;
    --easy-table-scrollbar-color: #2d3a4f;
    --easy-table-scrollbar-thumb-color: #4c5d7a;
    --easy-table-scrollbar-corner-color: #2d3a4f;

    --easy-table-loading-mask-background-color: #2d3a4f;
}
</style>
