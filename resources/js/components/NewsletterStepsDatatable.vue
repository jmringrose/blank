<template>
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Newsletter Steps</h2>
            <button @click="showCreateModal = true" class="btn btn-primary">
                <span class="material-symbols-outlined">add</span>
                Add Step
            </button>
        </div>

        <EasyDataTable
            :headers="headers"
            :items="items"
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
                <div class="flex justify-center">
                    <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="deleteItem(item)">
                        <span class="!text-base material-symbols-outlined">delete</span>
                    </button>
                    <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="editItem(item)">
                        <span class="!text-base material-symbols-outlined">edit</span>
                    </button>
                    <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="duplicateItem(item)">
                        <span class="!text-base material-symbols-outlined">content_copy</span>
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

                <form @submit.prevent="saveItem" class="space-y-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Order</span>
                        </label>
                        <input
                            v-model="formData.order"
                            type="number"
                            class="input input-bordered w-full"
                            placeholder="Enter order number"
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
                            placeholder="Enter newsletter title"
                            required
                        />
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Filename</span>
                            <span class="label-text-alt text-gray-500">.blade.php</span>
                        </label>
                        <input
                            v-model="formData.filename"
                            type="text"
                            class="input input-bordered w-full"
                            placeholder="welcome-email.blade.php"
                        />
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start">
                            <input v-model="formData.draft" type="checkbox" class="checkbox checkbox-primary mr-3" />
                            <span class="label-text font-medium">Save as Draft</span>
                        </label>
                        <div class="label">
                            <span class="label-text-alt text-gray-500">Drafts won't be sent to subscribers</span>
                        </div>
                    </div>
                </form>

                <div class="modal-action mt-6">
                    <button @click="saveItem" class="btn btn-primary">
                        <span class="material-symbols-outlined mr-2">save</span>
                        Save Step
                    </button>
                    <button @click="closeModal" class="btn btn-ghost">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const toast = useToast()
const loading = ref(false)
const items = ref([])
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingItem = ref(null)

const formData = ref({
    order: '',
    title: '',
    filename: '',
    draft: true
})

const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Order", value: "order", sortable: true },
    { text: "Title", value: "title", sortable: true },
    { text: "Filename", value: "filename", sortable: true },
    { text: "Status", value: "draft" },
    { text: "Actions", value: "actions" }
]

const fetchData = async () => {
    loading.value = true
    try {
        const response = await axios.get('/newsletter-steps')
        items.value = response.data
    } catch (error) {
        toast.error('Failed to load newsletter steps')
    } finally {
        loading.value = false
    }
}

const editItem = (item) => {
    editingItem.value = item
    formData.value = { ...item }
    showEditModal.value = true
}

const deleteItem = async (item) => {
    if (confirm('Are you sure you want to delete this step?')) {
        try {
            await axios.delete(`/newsletter-steps/${item.id}`)
            toast.success('Step deleted successfully')
            fetchData()
        } catch (error) {
            toast.error('Failed to delete step')
        }
    }
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
        const duplicateData = {
            order: item.order + 1,
            title: `${item.title} (Copy)`,
            filename: item.filename ? item.filename.replace('.blade.php', '-copy.blade.php') : '',
            draft: true
        }
        await axios.post('/newsletter-steps', duplicateData)
        toast.success('Step duplicated successfully')
        fetchData()
    } catch (error) {
        toast.error('Failed to duplicate step')
    }
}

const closeModal = () => {
    showCreateModal.value = false
    showEditModal.value = false
    editingItem.value = null
    formData.value = { order: '', title: '', filename: '', draft: true }
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
