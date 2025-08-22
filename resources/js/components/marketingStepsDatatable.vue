<template>
    <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-1 md:p-3 rounded-lg shadow-lg border">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold ml-4">Marketing Steps</h1>
            <div class="space-x-2">
                <a href="/dashboard" class="btn btn-outline">← Back to Dashboard</a>
                <button @click="createStep" class="btn btn-primary">Create New Marketing Step</button>
            </div>
        </div>

        <div v-if="successMessage" class="alert alert-success mb-4">{{ successMessage }}</div>

        <EasyDataTable
            :headers="headers"
            :items="steps"
            :loading="loading"
            :rows-per-page="15"
            alternating
            body-text-direction="left"
            header-text-direction="left"
            sort-by="order"
            sort-type="asc"
            table-class-name="customize-table"
            theme-color="#1d90ff"
            hide-rows-per-page
        >
            <template #item-draft="item">
                <span :class="item.draft ? 'badge badge-warning' : 'badge badge-success'">
                    {{ item.draft ? 'Draft' : 'Published' }}
                </span>
            </template>
            <template #item-actions="item">
                <div class="flex justify-center space-x-1">

                    <button class="btn btn-sm btn-secondary h-6 w-6" @click.stop="editStep(item)" title="Edit Record">
                        <span class="!text-base material-symbols-outlined">database</span>
                    </button>

                    <button class="btn btn-sm btn-secondary h-6 w-6" @click.stop="duplicateStep(item)" title="Duplicate">
                        <span class="!text-base material-symbols-outlined">content_copy</span>
                    </button>

                    <a :href="`/marketing-editor/${item.id}/edit`" class="btn btn-sm btn-secondary h-6 w-6" title="Edit File">
                        <span class="!text-base material-symbols-outlined">edit</span>
                    </a>

                    <a :href="`/preview/marketing/${item.order}`" target="_blank" class="btn btn-sm btn-secondary h-6 w-6" title="View">
                        <span class="!text-base material-symbols-outlined">visibility</span>
                    </a>

                    <button class="btn btn-sm btn-warning h-6 w-6" @click.stop="sendTestEmail(item)" :disabled="sendingEmails.has(item.id)" :class="{'opacity-50 cursor-not-allowed': sendingEmails.has(item.id)}" title="Send Test Email">
                        <span class="!text-base material-symbols-outlined">mail</span>
                    </button>

                    <button class="btn btn-sm h-6 w-6" :class="item.draft ? 'btn-success' : 'btn-info'" @click.stop="toggleStep(item)" :title="item.draft ? 'Publish' : 'Unpublish'">
                        <span class="!text-base material-symbols-outlined">{{ item.draft ? 'check_circle' : 'cancel' }}</span>
                    </button>

                    <button class="btn btn-sm btn-secondary h-6 w-6 ml-2 bg-red-400" @click.stop="deleteStep(item)" title="Delete">
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

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="modal modal-open">
            <div class="modal-box max-w-md">
                <h3 class="font-bold text-lg mb-4">{{ editingStep ? 'Edit' : 'Create' }} Marketing Step</h3>

                <form @submit.prevent="saveStep" class="space-y-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Order</span>
                        </label>
                        <input
                            v-model="formData.order"
                            type="number"
                            class="input input-bordered w-full"
                            required
                        />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Title</span>
                        </label>
                        <input
                            v-model="formData.title"
                            type="text"
                            class="input input-bordered w-full"
                            required
                        />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Filename</span>
                        </label>
                        <input
                            v-model="formData.filename"
                            type="text"
                            class="input input-bordered w-full"
                        />
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input v-model="formData.draft" type="checkbox" class="checkbox checkbox-primary mr-3" />
                            <span class="label-text font-medium">Save as Draft</span>
                        </label>
                    </div>
                </form>

                <div class="modal-action mt-6">
                    <button @click="saveStep" class="btn btn-primary">
                        Save Step
                    </button>
                    <button @click="closeModal" class="btn btn-ghost">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Confirm Delete</h3>
                <p class="py-4">Are you sure you want to delete "{{ stepToDelete?.title }}"? This action cannot be undone.</p>
                <div class="modal-action">
                    <button @click="confirmDelete" class="btn btn-error">Delete</button>
                    <button @click="closeDeleteModal" class="btn btn-ghost">Cancel</button>
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
                        <input v-model="testEmailAddress" type="email" class="input input-bordered flex-1" placeholder="Enter email address" required>
                        <button @click="useTestRecipient" class="btn btn-outline btn-sm">Use Test Recipient</button>
                    </div>
                </div>
                <div class="modal-action">
                    <button @click="confirmSendTestEmail" class="btn btn-primary" :disabled="!testEmailAddress">Send Test Email</button>
                    <button @click="closeTestEmailModal" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

export default {
    name: 'MarketingStepsDatatable',
    setup() {
        const steps = ref([])
        const successMessage = ref('')
        const toast = useToast()
        const loading = ref(false)
        const showEditModal = ref(false)
        const showDeleteModal = ref(false)
        const showTestEmailModal = ref(false)
        const editingStep = ref(null)
        const stepToDelete = ref(null)
        const testEmailStep = ref(null)
        const testEmailAddress = ref('')
        const formData = ref({
            order: '',
            title: '',
            filename: '',
            draft: false
        })

        const headers = [
            { text: "Order", value: "order", sortable: true },
            { text: "Title", value: "title", sortable: true },
            { text: "Filename", value: "filename", sortable: true },
            { text: "Status", value: "draft" },
            { text: "Actions", value: "actions" }
        ]

        const fetchSteps = async () => {
            loading.value = true
            try {
                const response = await axios.get(window.location.origin + '/marketing-steps/data')
                steps.value = response.data.sort((a, b) => a.order - b.order)
            } catch (error) {
                console.error('Error fetching marketing steps:', error)
                toast.error('Failed to fetch marketing steps')
            } finally {
                loading.value = false
            }
        }

        const toggleStep = async (step) => {
            try {
                await axios.get(window.location.origin + `/marketing-steps/toggle/${step.id}`)
                const status = !step.draft ? 'draft' : 'published'
                toast.success(`Marketing step set as ${status}`)
                fetchSteps()
            } catch (error) {
                console.error('Error toggling step:', error)
                toast.error('Failed to toggle step status')
            }
        }

        const duplicateStep = async (step) => {
            try {
                const maxOrder = Math.max(...steps.value.map(s => s.order))
                const duplicateData = {
                    order: maxOrder + 1,
                    title: `${step.title} (Copy)`,
                    filename: step.filename ? step.filename.replace('.blade.php', '-copy.blade.php') : '',
                    original_filename: step.filename,
                    draft: true
                }
                await axios.post(window.location.origin + '/marketing-steps', duplicateData)
                toast.success('Marketing step duplicated successfully')
                fetchSteps()
            } catch (error) {
                console.error('Error duplicating step:', error)
                toast.error('Failed to duplicate marketing step')
            }
        }

        const deleteStep = (step) => {
            stepToDelete.value = step
            showDeleteModal.value = true
        }

        const confirmDelete = async () => {
            try {
                await axios.delete(window.location.origin + `/marketing-steps/${stepToDelete.value.id}`)
                steps.value = steps.value.filter(s => s.id !== stepToDelete.value.id)
                toast.success('Marketing step deleted successfully')
                closeDeleteModal()
            } catch (error) {
                console.error('Error deleting step:', error)
                toast.error('Failed to delete marketing step')
            }
        }

        const closeDeleteModal = () => {
            showDeleteModal.value = false
            stepToDelete.value = null
        }

        onMounted(() => {
            fetchSteps()
        })

        const editStep = (step) => {
            editingStep.value = step
            formData.value = { ...step }
            showEditModal.value = true
        }

        const createStep = () => {
            editingStep.value = null
            formData.value = { order: '', title: '', filename: '', draft: true }
            showEditModal.value = true
        }

        const saveStep = async () => {
            try {
                if (editingStep.value) {
                    // Update existing step
                    await axios.put(window.location.origin + `/marketing-steps/${editingStep.value.id}`, formData.value)
                    toast.success('Marketing step updated successfully')
                } else {
                    // Create new step
                    await axios.post(window.location.origin + '/marketing-steps', formData.value)
                    toast.success('Marketing step created successfully')
                }
                closeModal()
                fetchSteps()
            } catch (error) {
                console.error('Error saving step:', error)
                toast.error('Failed to save marketing step')
            }
        }

        const sendingEmails = ref(new Set())
        
        const sendTestEmail = (step) => {
            testEmailStep.value = step
            testEmailAddress.value = ''
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
                    type: 'marketing',
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
            showEditModal.value = false
            editingStep.value = null
            formData.value = { order: '', title: '', filename: '', draft: false }
        }

        return {
            steps,
            successMessage,
            loading,
            headers,
            showEditModal,
            showDeleteModal,
            showTestEmailModal,
            formData,
            stepToDelete,
            testEmailStep,
            testEmailAddress,
            sendingEmails,
            toggleStep,
            deleteStep,
            duplicateStep,
            createStep,
            editStep,
            saveStep,
            sendTestEmail,
            confirmSendTestEmail,
            useTestRecipient,
            closeTestEmailModal,
            closeModal,
            confirmDelete,
            closeDeleteModal
        }
    }
}
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
