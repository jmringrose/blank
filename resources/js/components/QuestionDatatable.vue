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
            :expand-column-width="50"
        >
            <template #item-sent="item">
                <span :class="item.unsubscribed ? 'badge badge-error' : (item.sent ? 'badge badge-success' : 'badge badge-warning')">
                    {{ item.unsubscribed ? 'Unsubscribed' : (item.sent ? 'Sent' : 'Pending') }}
                </span>
            </template>

            <template #item-question_step.title="{ question_step }">
                <span v-if="question_step">{{ question_step.title }}</span>
                <span v-else class="text-gray-400">No question assigned</span>
            </template>

            <template #item-actions="item">
                <div class="flex justify-center">
                    <button class="btn btn-sm btn-info h-6 w-6 mr-1" @click.stop="confirmDeleteItem(item)">
                        <span class="!text-base material-symbols-outlined">delete</span>
                    </button>
                    <button class="btn btn-sm btn-info h-6 w-6 mr-1" @click.stop="editItem(item)">
                        <span class="!text-base material-symbols-outlined">edit</span>
                    </button>
                    <button class="btn btn-sm btn-info h-6 w-6 mr-1" @click.stop="cloneItem(item)">
                        <span class="!text-base material-symbols-outlined">content_copy</span>
                    </button>
                    <button class="btn btn-sm btn-info h-6 w-6 mr-1" @click.stop="previewEmail(item)" :disabled="!item.question_step">
                        <span class="!text-base material-symbols-outlined">visibility</span>
                    </button>
                    <button class="btn btn-sm h-6 w-6 mr-1" :class="[item.sent ? 'btn-info' : 'btn-success', sendingEmails.has(item.id) ? 'loading' : '']" @click.stop="sendEmail(item)" :disabled="!item.question_step || sendingEmails.has(item.id) || item.unsubscribed">
                        <span class="!text-base material-symbols-outlined">send</span>
                    </button>
                    <button class="btn btn-sm h-6 w-6" :class="expandedRows.has(item.id) ? 'btn-success' : 'btn-info'" @click.stop="toggleHistory(item)">
                        <span class="!text-base material-symbols-outlined">{{ expandedRows.has(item.id) ? 'expand_less' : 'expand_more' }}</span>
                    </button>
                </div>
            </template>

        </EasyDataTable>

        <!-- Manual expand rows -->
        <div v-for="item in items" :key="'expand-' + item.id">
            <div v-if="expandedRows.has(item.id)" class="p-4 bg-base-200 border border-gray-300 mt-1 mb-2 rounded">
                <h4 class="font-semibold mb-2">Email History for {{ item.first }} {{ item.last }}:</h4>
                <div v-if="item.email_history && item.email_history.length > 0" class="text-sm">
                    <div v-for="(email, index) in item.email_history" :key="index" class="mb-1">
                        • {{ email }}
                    </div>
                </div>
                <div v-else class="text-sm text-gray-500">
                    No emails sent yet
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div v-if="showAddUserModal" class="modal modal-open">
            <div class="modal-box max-w-md">
                <h3 class="font-bold text-lg mb-4">{{ editingItem ? 'Edit' : 'Add' }} Question User</h3>

                <form @submit.prevent="saveUser" class="space-y-4">
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
                            <span class="label-text font-medium">Question</span>
                        </label>
                        <select
                            v-model="userFormData.question_step_id"
                            class="select select-bordered w-full"
                            required
                        >
                            <option value="">Select a question</option>
                            <option v-for="step in questionSteps" :key="step.id" :value="step.id">
                                {{ step.title }}
                            </option>
                        </select>
                    </div>

                    <div v-if="editingItem && userFormData.email_history && userFormData.email_history.length > 0" class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Email History</span>
                        </label>
                        <div class="space-y-2">
                            <div v-for="(email, index) in userFormData.email_history" :key="index" class="flex items-center justify-between bg-base-200 p-2 rounded">
                                <span class="text-sm">{{ email }}</span>
                                <button type="button" class="btn btn-xs btn-error" @click="removeHistoryItem(index)">
                                    <span class="material-symbols-outlined text-xs">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal-action mt-6">
                    <button @click="saveUser" class="btn btn-primary">
                        <span class="material-symbols-outlined mr-2">{{ editingItem ? 'edit' : 'person_add' }}</span>
                        {{ editingItem ? 'Update' : 'Add' }} User
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
    {text: "Question", value: "question_step.title", sortable: true, width: 200},
    {text: "Sent", value: "sent", sortable: true, width: 80},
    {text: "Actions", value: "actions", width: 120}
]

const searchField = ref("")
const searchValue = ref("")
const items = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const showAddUserModal = ref(false)
const deleteModalMessage = ref("")
const pendingDeleteAction = ref(null)
const editingItem = ref(null)

const userFormData = ref({
    first: '',
    last: '',
    email: '',
    question_step_id: null
})

const questionSteps = ref([])
const sendingEmails = ref(new Set())
const expandedRows = ref(new Set())

const getHistory = () => {
    loading.value = true
    axios.get('/question-sequences/data')
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
    editingItem.value = item
    userFormData.value = { 
        ...item,
        email_history: item.email_history ? [...item.email_history] : []
    }
    showAddUserModal.value = true
}

const removeHistoryItem = (index) => {
    userFormData.value.email_history.splice(index, 1)
}

const previewEmail = (item) => {
    if (item.question_step) {
        window.open(`/preview/question/${item.question_step.order}?questioner_id=${item.id}`, '_blank')
    }
}

const cloneItem = (item) => {
    editingItem.value = null
    userFormData.value = {
        first: item.first,
        last: item.last,
        email: '', // Clear email for clone
        question_step_id: item.question_step_id
    }
    showAddUserModal.value = true
}

const confirmDeleteItem = (item) => {
    deleteModalMessage.value = `Are you sure you want to delete the record for "${item.email}"?`
    pendingDeleteAction.value = () => deleteItem(item)
    showDeleteModal.value = true
}

const deleteItem = async (item) => {
    try {
        await axios.delete(`/question-sequence/${item.id}`)
        items.value = items.value.filter(i => i.id !== item.id)
        toast.success(`Deleted: ${item.email}`)
    } catch (err) {
        console.error('Delete failed:', err)
        toast.error('Failed to delete item')
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

const sendEmail = async (item) => {
    if (sendingEmails.value.has(item.id) || !item.question_step) {
        return
    }
    
    try {
        sendingEmails.value.add(item.id)
        const response = await axios.post('/question-sequences/send', { id: item.id })
        
        // Update local state with server response
        const index = items.value.findIndex(i => i.id === item.id)
        if (index !== -1) {
            items.value[index].sent = true
            items.value[index].email_history = response.data.email_history || []
        }
        
        toast.success('Email sent successfully')
    } catch (error) {
        console.error('Error sending email:', error)
        toast.error('Failed to send email')
    } finally {
        sendingEmails.value.delete(item.id)
    }
}

const toggleHistory = (item) => {
    if (expandedRows.value.has(item.id)) {
        expandedRows.value.delete(item.id)
    } else {
        expandedRows.value.clear()
        expandedRows.value.add(item.id)
    }
}

const saveUser = async () => {
    try {
        if (editingItem.value) {
            // Check if question changed
            const questionChanged = editingItem.value.question_step_id !== userFormData.value.question_step_id
            
            await axios.put(`/question-sequence/${editingItem.value.id}`, userFormData.value)
            
            // Update local state immediately
            const index = items.value.findIndex(i => i.id === editingItem.value.id)
            if (index !== -1) {
                items.value[index] = { ...items.value[index], ...userFormData.value }
                if (questionChanged) {
                    items.value[index].sent = false
                }
            }
            
            toast.success('User updated successfully')
        } else {
            // Create new
            await axios.post('/question-sequences', userFormData.value)
            toast.success('User added successfully')
        }
        closeAddUserModal()
        getHistory()
    } catch (error) {
        console.error('Error saving user:', error.response?.data || error.message)
        toast.error('Failed to save user: ' + (error.response?.data?.message || error.message))
    }
}

const closeAddUserModal = () => {
    showAddUserModal.value = false
    editingItem.value = null
    userFormData.value = { first: '', last: '', email: '', question_step_id: null }
}

const fetchQuestionSteps = async () => {
    try {
        const response = await axios.get('/question-steps/data')
        questionSteps.value = response.data.filter(step => !step.draft)
    } catch (error) {
        console.error('Error fetching question steps:', error)
    }
}

const clearSearch = () => {
    searchField.value = ""
    searchValue.value = ""
}

onMounted(() => {
    getHistory()
    fetchQuestionSteps()
    
    document.getElementById('addUserBtn')?.addEventListener('click', () => {
        showAddUserModal.value = true
    })
})
</script>

<style scoped>
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
    --easy-table-scrollbar-track-color: #2d3a4f;
    --easy-table-scrollbar-color: #2d3a4f;
    --easy-table-scrollbar-thumb-color: #4c5d7a;
    --easy-table-scrollbar-corner-color: #2d3a4f;
    --easy-table-loading-mask-background-color: #2d3a4f;
}
</style>