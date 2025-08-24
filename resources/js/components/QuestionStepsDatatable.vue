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
            sort-by="order"
            sort-type="asc"
            table-class-name="customize-table"
            theme-color="#1d90ff"
            hide-rows-per-page
        >
            <template #item-draft="{ draft }">
                <span :class="draft ? 'badge badge-warning' : 'badge badge-success'">
                    {{ draft ? 'Draft' : 'Published' }}
                </span>
            </template>

            <template #item-actions="item">
                <div class="flex justify-center">
                    <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="confirmDeleteItem(item)">
                        <span class="!text-base material-symbols-outlined">delete</span>
                    </button>
                    <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="editItem(item)">
                        <span class="!text-base material-symbols-outlined">settings</span>
                    </button>
                    <button class="btn btn-sm btn-info h-6 w-6 mr-1" @click.stop="editEmail(item)">
                        <span class="!text-base material-symbols-outlined">edit</span>
                    </button>
                    <button class="btn btn-sm btn-warning h-6 w-6 mr-1" @click.stop="previewEmail(item)">
                        <span class="!text-base material-symbols-outlined">visibility</span>
                    </button>
                    <button class="btn btn-sm h-6 w-6 mr-1" :class="item.draft ? 'btn-success' : 'btn-info'" @click.stop="toggleDraft(item)" :title="item.draft ? 'Publish' : 'Unpublish'">
                        <span class="!text-base material-symbols-outlined">{{ item.draft ? 'check_circle' : 'cancel' }}</span>
                    </button>
                </div>
            </template>
        </EasyDataTable>

        <!-- Add/Edit Step Modal -->
        <div v-if="showAddStepModal" class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg mb-4">{{ editingItem ? 'Edit' : 'Add' }} Question</h3>

                <form @submit.prevent="saveStep" class="space-y-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">ID</span>
                        </label>
                        <input
                            v-model.number="stepFormData.order"
                            type="number"
                            class="input input-bordered w-full"
                            placeholder="Enter question ID"
                            required
                        />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Title</span>
                        </label>
                        <input
                            v-model="stepFormData.title"
                            type="text"
                            class="input input-bordered w-full"
                            placeholder="Enter step title"
                            required
                        />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Notes (Optional)</span>
                        </label>
                        <textarea
                            v-model="stepFormData.notes"
                            class="textarea textarea-bordered w-full h-24"
                            placeholder="Internal notes about this question"
                        ></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">Draft</span>
                            <input v-model="stepFormData.draft" type="checkbox" class="checkbox" />
                        </label>
                    </div>
                </form>

                <div class="modal-action mt-6">
                    <button @click="saveStep" class="btn btn-primary">
                        <span class="material-symbols-outlined mr-2">{{ editingItem ? 'edit' : 'add' }}</span>
                        {{ editingItem ? 'Update' : 'Add' }} Question
                    </button>
                    <button @click="closeAddStepModal" class="btn btn-ghost">
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
    {text: "ID", value: "order", sortable: true, width: 80},
    {text: "Title", value: "title", sortable: true, width: 300},
    {text: "Notes", value: "notes", sortable: false, width: 250},
    {text: "Status", value: "draft", sortable: true, width: 100},
    {text: "Actions", value: "actions", width: 120}
]

const searchField = ref("")
const searchValue = ref("")
const items = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const showAddStepModal = ref(false)
const deleteModalMessage = ref("")
const pendingDeleteAction = ref(null)
const editingItem = ref(null)

const stepFormData = ref({
    order: 1,
    title: '',
    notes: '',
    draft: true
})

const getHistory = () => {
    loading.value = true
    axios.get('/question-steps/data')
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
    stepFormData.value = { ...item }
    showAddStepModal.value = true
}

const confirmDeleteItem = (item) => {
    deleteModalMessage.value = `Are you sure you want to delete step "${item.title}"?`
    pendingDeleteAction.value = () => deleteItem(item)
    showDeleteModal.value = true
}

const deleteItem = async (item) => {
    try {
        await axios.delete(`/question-steps/${item.id}`)
        items.value = items.value.filter(i => i.id !== item.id)
        toast.success(`Deleted: ${item.title}`)
    } catch (err) {
        console.error('Delete failed:', err)
        toast.error('Failed to delete item')
    }
}

const toggleDraft = async (item) => {
    try {
        await axios.get(`/question-steps/toggle/${item.id}`)
        const index = items.value.findIndex(i => i.id === item.id)
        if (index !== -1) {
            items.value[index].draft = !items.value[index].draft
        }
        toast.success(`Step ${items.value[index].draft ? 'drafted' : 'published'}`)
    } catch (err) {
        console.error('Toggle failed:', err)
        toast.error('Failed to toggle status')
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

const saveStep = async () => {
    try {
        if (editingItem.value) {
            await axios.put(`/question-steps/${editingItem.value.id}`, stepFormData.value)
            toast.success('Step updated successfully')
        } else {
            await axios.post('/question-steps', stepFormData.value)
            toast.success('Step added successfully')
        }
        closeAddStepModal()
        getHistory()
    } catch (error) {
        console.error('Error saving step:', error.response?.data || error.message)
        toast.error('Failed to save step: ' + (error.response?.data?.message || error.message))
    }
}

const editEmail = (item) => {
    window.location.href = `/question-editor/${item.id}/edit`
}

const previewEmail = (item) => {
    window.open(`/preview/question/${item.order}`, '_blank')
}

const closeAddStepModal = () => {
    showAddStepModal.value = false
    editingItem.value = null
    stepFormData.value = { order: 1, title: '', notes: '', draft: true }
}

const clearSearch = () => {
    searchField.value = ""
    searchValue.value = ""
}

onMounted(() => {
    getHistory()
    
    document.getElementById('addStepBtn')?.addEventListener('click', () => {
        showAddStepModal.value = true
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