<template>
    <div class="container mx-auto p-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Newsletter Steps</h2>
            <div class="flex items-center ">
                <a class="btn btn-primary mr-2" href="newsletter-sequences" role="button">
                    <span class="material-symbols-outlined">edit</span>
                    Users
                </a>
                <button class="btn btn-primary" @click="showCreateModal = true">
                    <span class="material-symbols-outlined">add</span>
                    Add Step
                </button>
            </div>
        </div>
        <EasyDataTable
            :headers="headers"
            :items="items"
            :loading="loading"
            :rows-per-page="15"
            alternating
            body-text-direction="left"
            header-text-direction="left"
            hide-rows-per-page
            sort-by="order"
            sort-type="asc"
            table-class-name="customize-table"
            theme-color="#1d90ff"
        >
            <template #item-draft="item">
                <span :class="item.draft ? 'badge badge-warning' : 'badge badge-success'">
                    {{ item.draft ? 'Draft' : 'Published' }}
                </span>
            </template>
            <template #item-actions="item">
                <div class="flex justify-center space-x-1">
                    <button class="btn btn-sm btn-secondary h-6 w-6" title="Edit Record" @click.stop="editItem(item)">
                        <span class="!text-base material-symbols-outlined">database</span>
                    </button>
                    <button class="btn btn-sm btn-secondary h-6 w-6" title="Duplicate" @click.stop="duplicateItem(item)">
                        <span class="!text-base material-symbols-outlined">content_copy</span>
                    </button>
                    <a :href="`/newsletter-editor/${item.id}/edit`" class="btn btn-sm btn-secondary h-6 w-6" title="Edit File">
                        <span class="!text-base material-symbols-outlined">edit</span>
                    </a>
                    <a :href="`/preview/newsletter/${item.order}`" class="btn btn-sm btn-secondary h-6 w-6" target="_blank" title="View">
                        <span class="!text-base material-symbols-outlined">visibility</span>
                    </a>
                    <button :class="{'opacity-50 cursor-not-allowed': sendingEmails.has(item.id)}" :disabled="sendingEmails.has(item.id)" class="btn btn-sm btn-warning h-6 w-6" title="Send Test Email" @click.stop="sendTestEmail(item)">
                        <span class="!text-base material-symbols-outlined">mail</span>
                    </button>
                    <button :class="item.draft ? 'btn-success' : 'btn-info'" :title="item.draft ? 'Publish' : 'Unpublish'" class="btn btn-sm h-6 w-6" @click.stop="togglePublish(item)">
                        <span class="!text-base material-symbols-outlined">{{ item.draft ? 'check_circle' : 'cancel' }}</span>
                    </button>
                    <button class="btn btn-sm btn-secondary h-6 w-6 ml-2 bg-red-400" title="Delete" @click.stop="deleteItem(item)">
                        <span class="!text-base material-symbols-outlined">delete</span>
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
        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || showEditModal" class="modal modal-open">
            <div class="modal-box max-w-md">
                <h3 class="font-bold text-lg mb-4">{{ showCreateModal ? 'Add' : 'Edit' }} Newsletter Step</h3>
                <form class="space-y-4" @submit.prevent="saveItem">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Order</span>
                        </label>
                        <input
                            v-model="formData.order"
                            class="input input-bordered w-full"
                            placeholder="Enter order number"
                            required
                            type="number"
                        />
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Title</span>
                        </label>
                        <input
                            v-model="formData.title"
                            class="input input-bordered w-full"
                            placeholder="Enter newsletter title"
                            required
                            type="text"
                        />
                    </div>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Filename</span>
                            <span class="label-text-alt text-gray-500">.blade.php</span>
                        </label>
                        <input
                            v-model="formData.filename"
                            class="input input-bordered w-full"
                            placeholder="welcome-email.blade.php"
                            type="text"
                        />
                    </div>
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input v-model="formData.draft" class="checkbox checkbox-primary mr-3" type="checkbox"/>
                            <span class="label-text font-medium">Save as Draft</span>
                        </label>
                        <div class="label">
                            <span class="label-text-alt text-gray-500">Drafts won't be sent to subscribers</span>
                        </div>
                    </div>
                </form>
                <div class="modal-action mt-6">
                    <button class="btn btn-primary" @click="saveItem">
                        <span class="material-symbols-outlined mr-2">save</span>
                        Save Step
                    </button>
                    <button class="btn btn-ghost" @click="closeModal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Confirm Delete</h3>
                <p class="py-4">Are you sure you want to delete "{{ itemToDelete?.title }}"? This action cannot be undone.</p>
                <div class="modal-action">
                    <button class="btn btn-error" @click="confirmDelete">Delete</button>
                    <button class="btn btn-ghost" @click="closeDeleteModal">Cancel</button>
                </div>
            </div>
        </div>
        <!-- Test Email Modal -->
        <div v-if="showTestEmailModal" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Send Test Email</h3>
                <p class="py-2">Send test email for: <strong>{{ testEmailStep?.title }}</strong></p>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email Address</span>
                    </label>
                    <div class="flex gap-2">
                        <input v-model="testEmailAddress" class="input input-bordered flex-1" placeholder="Enter email address" required type="email">
                        <button class="btn btn-outline btn-sm" @click="useTestRecipient">Use Test Recipient</button>
                    </div>
                </div>
                <div class="modal-action">
                    <button :disabled="!testEmailAddress" class="btn btn-primary" @click="confirmSendTestEmail">Send Test Email</button>
                    <button class="btn btn-ghost" @click="closeTestEmailModal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {onMounted, ref} from 'vue'
import {useToast} from 'vue-toastification'
import axios from 'axios'

const toast = useToast()
const loading = ref(false)
const items = ref([])
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showTestEmailModal = ref(false)
const editingItem = ref(null)
const itemToDelete = ref(null)
const testEmailStep = ref(null)
const testEmailAddress = ref('')
const formData = ref({
    order: '',
    title: '',
    filename: '',
    draft: true
})
const headers = [
    {text: "ID", value: "id", sortable: true},
    {text: "Order", value: "order", sortable: true},
    {text: "Title", value: "title", sortable: true},
    {text: "Filename", value: "filename", sortable: true},
    {text: "Status", value: "draft"},
    {text: "Actions", value: "actions"}
]
const fetchData = async () => {
    loading.value = true
    try {
        const response = await axios.get('/newsletter-steps/data')
        items.value = response.data
    } catch (error) {
        toast.error('Failed to load newsletter steps')
    } finally {
        loading.value = false
    }
}
const editItem = (item) => {
    editingItem.value = item
    formData.value = {...item}
    showEditModal.value = true
}
const deleteItem = (item) => {
    itemToDelete.value = item
    showDeleteModal.value = true
}
const confirmDelete = async () => {
    try {
        await axios.delete(`/newsletter-steps/${itemToDelete.value.id}`)
        toast.success('Step deleted successfully')
        fetchData()
        closeDeleteModal()
    } catch (error) {
        toast.error('Failed to delete step')
    }
}
const closeDeleteModal = () => {
    showDeleteModal.value = false
    itemToDelete.value = null
}
const saveItem = async () => {
    try {
        if (showCreateModal.value) {
            await axios.post('/newsletter-steps', formData.value)
            toast.success('Step created successfully')
        } else {
            await axios.put(`/newsletter-steps/${editingItem.value.id}`, formData.value)
            toast.success('Step updated successfully')
        }
        closeModal()
        fetchData()
    } catch (error) {
        toast.error('Failed to save step')
    }
}
const duplicateItem = async (item) => {
    try {
        const maxOrder = Math.max(...items.value.map(i => i.order))
        const duplicateData = {
            order: maxOrder + 1,
            title: `${item.title} (Copy)`,
            filename: item.filename ? item.filename.replace('.blade.php', '-copy.blade.php') : '',
            original_filename: item.filename,
            draft: true
        }
        await axios.post('/newsletter-steps', duplicateData)
        toast.success('Step duplicated successfully')
        fetchData()
    } catch (error) {
        toast.error('Failed to duplicate step')
    }
}
const togglePublish = async (item) => {
    try {
        await axios.get(`/newsletter-editor/toggle/${item.id}`)
        const status = !item.draft ? 'draft' : 'published'
        toast.success(`Newsletter set as ${status}`)
        fetchData()
    } catch (error) {
        toast.error('Failed to toggle publish status')
    }
}
const sendingEmails = ref(new Set())
const sendTestEmail = (step) => {
    testEmailStep.value = step
    testEmailAddress.value = '' // Reset to empty so user must enter/confirm
    showTestEmailModal.value = true
}
const confirmSendTestEmail = async () => {
    const step = testEmailStep.value
    if (sendingEmails.value.has(step.id) || !testEmailAddress.value) {
        return
    }
    try {
        sendingEmails.value.add(step.id)
        toast.info(`Sending test email for step ${step.order}...`)
        const response = await axios.post('/send-test-email', {
            type: 'newsletter',
            step: step.order,
            email: testEmailAddress.value
        })
        toast.success(`Test email sent to ${testEmailAddress.value} for step ${step.order}: ${step.title}`)
        closeTestEmailModal()
    } catch (error) {
        console.error('Error sending test email:', error)
        toast.error(`Error sending test email: ${error.response?.data?.message || error.message}`)
    } finally {
        sendingEmails.value.delete(step.id)
    }
}
const useTestRecipient = () => {
    testEmailAddress.value = window.Laravel?.env?.ADMIN_EMAIL || 'james@jringrose.com'
    confirmSendTestEmail() // Also submit immediately
}
const closeTestEmailModal = () => {
    showTestEmailModal.value = false
    testEmailStep.value = null
    testEmailAddress.value = ''
}
const closeModal = () => {
    showCreateModal.value = false
    showEditModal.value = false
    editingItem.value = null
    formData.value = {order: '', title: '', filename: '', draft: true}
}
onMounted(() => {
    fetchData()
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
