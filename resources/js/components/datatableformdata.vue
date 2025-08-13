<template>
    <!-- header stuff  -->
    <div class="flex mb-4 text-base-content">
        <span class="mr-2 mt-2 text-sm">Search specific field:</span>
        <select v-model="searchField" class="ml-2 mr-4 select bg-secondary select-sm w-36 ">
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
        <!-- Column visibility dropdown -->
        <div class="dropdown dropdown-end">
            <label class="btn btn-sm btn-secondary" tabindex="0">
                Columns
                <svg fill="none" height="16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="16" xmlns="http://www.w3.org/2000/svg">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </label>
            <ul class="dropdown-content z-[1] menu p-2 shadow bg-base-200 rounded-box w-52" tabindex="0">
                <li v-for="header in headers" :key="header.value">
                    <label class="flex items-center cursor-pointer">
                        <input
                            :checked="visibleColumns.includes(header.value)"
                            class="checkbox checkbox-sm"
                            type="checkbox"
                            @change="toggleColumnVisibility(header.value)"
                        />
                        <span class="ml-2">{{ header.text }}</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
    <EasyDataTable
        :expand-column-width="50"
        :headers="filteredHeaders"
        :items="items"
        :row-class-name="rowClass"
        :rows-per-page="15"
        :search-field="searchField"
        :search-value="searchValue"
        alternating
        body-text-direction="left"
        header-text-direction="left"
        sort-by="id"
        sort-type="desc"
        table-class-name="customize-table"
        theme-color="#1d90ff"
        hide-rows-per-page
    >

        <template #item-actions="item">
            <div class="flex justify-center">
                <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="confirmDeleteItem(item)"><span class="!text-base material-symbols-outlined">delete</span></button>
                <button class="btn btn-sm btn-secondary h-6 w-6  mr-1" @click.stop="editItem(item)"><span class="!text-base material-symbols-outlined">edit</span></button>
            </div>
        </template>


        <template #item-date_created="{ date_created }">
            <span :title="date_created">{{ formatDate(date_created) }}</span>
        </template>

        <!-- Tour Date field formatting -->
        <template #item-fields.select-1="item">
            <span :title="item.fields['select-1']">{{ formatTourDate(item.fields['select-1']) }}</span>
        </template>

        <!-- Expand template to show all form fields -->
        <template #expand="item">
            <div class="p-3 bg-stone-500 rounded text-yellow-100">
                <h4 class="font-bold mb-1 font-semibold text-stone-900 text-xl">Form Fields:</h4>
                <hr class="text-stone-700 mb-3">
                <div class="grid grid-cols-2 gap-2">
                    <div v-for="(value, key) in item.fields" :key="key" v-show="!excludeFromExpand.includes(key)" class="flex">
                        <span class="font-medium w-32 text-stone-900">{{ translateMetaKey(key) }}:</span>
                        <span v-if="value && value.includes(',')" class="whitespace-pre-line text-gray-200">{{ value.replace(/,/g, ',\n') }}</span>
                        <span v-else class="text-gray-200">{{ value }}</span>
                    </div>
                </div>
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
    <!-- Loading and error states -->
    <div v-if="loading" class="flex justify-center my-4">
        <div class="loading loading-spinner loading-lg"></div>
    </div>
    <div v-if="error" class="alert alert-error my-4">
        {{ error }}
    </div>
    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p class="py-4">{{ deleteModalMessage }}</p>
            <div class="modal-action">
                <button class="btn btn-error" @click="proceedWithDelete">
                    Yes, Delete
                </button>
                <button class="btn" @click="cancelDelete">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import axios from 'axios';
import {computed, onMounted, ref} from 'vue';
import type {Item} from "vue3-easy-data-table";
import {useToast} from "vue-toastification";

// Toast instance
const toast = useToast();

// Meta key lookup map
const metaKeyLookup = {
    'name-1': 'Users Name',
    '_forminator_user_ip': 'Users IP',
    'email-1': 'Email Address',
    'select-1': 'Tour Start',
    'checkbox-1': 'Diet',
    'checkbox-2': 'Fitness',
    'slider-1': 'Bird',
    'slider-2': 'Wildlife',
    'slider-3': 'Landscape',
    'slider-4': 'Documentary',
    'slider-5': 'Lighhroom',
    'slider-6': 'Contests',
    'radio-2': 'Tech Skill',
    'radio-1': 'Lightroom',
    'textarea-1': 'Special Needs'
    // Add more mappings as needed
};

const translateMetaKey = (key: string) => {
    return metaKeyLookup[key] || key;
};

// Fields to exclude from expand view (already shown in table)
const excludeFromExpand = ['name-1', 'email-1', 'select-1'];

const formatDate = (dateString: string) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
};

const formatTourDate = (dateRange: string) => {
    if (!dateRange) return 'N/A';
    // Extract first date from "February 1 - February 7" format
    const firstDate = dateRange.split(' - ')[0];
    return firstDate;
};

// =====================
// Table Headers
// =====================
const headers = [
    {text: "ID", value: "entry_id", sortable: true, width: 59},
    {text: "Tour Date", value: "fields.select-1", sortable: true, width: 180},
    {text: "Name", value: "fields.name-1", sortable: true, width: 180},
    {text: "Email", value: "fields.email-1", sortable: true, width: 200},
    {text: "Created", value: "date_created", sortable: true, width: 180},
    {text: "Actions", value: "actions", width: 80}
];

// =====================
// Reactive State
// =====================
const searchField = ref("");
const searchValue = ref("");
const itemsSelected = ref<Item[]>([]);
const items = ref<Item[]>([]);
const loading = ref(false);
const error = ref("");

// Delete confirmation modal
const showDeleteModal = ref(false);
const deleteModalMessage = ref("");
const pendingDeleteAction = ref<(() => Promise<void>) | null>(null);

// Column visibility
const visibleColumns = ref(headers.map(header => header.value));
const filteredHeaders = computed(() =>
    headers.filter(header => visibleColumns.value.includes(header.value))
);
const rowClass = (item) => {
    return itemsSelected.value.includes(item) ? 'custom-selected' : 'b1';
};

// =====================
// Fetch Data
// =====================
const getHistory = () => {
    loading.value = true;
    error.value = "";
    axios.get('/forms/data')
        .then((res) => {
            items.value = res.data; // Assign response data to table items
        })
        .catch((err) => {
            console.error('Error fetching history:', err);
            error.value = "Failed to load data. Please try again.";
            items.value = []; // Fallback to empty data
            toast.error("Failed to load data");
        })
        .finally(() => {
            loading.value = false;
        });
};

onMounted(() => {
    getHistory();
});

// =====================
// Delete Confirmation Functions
// =====================
const confirmDeleteItem = (item: Item) => {
    deleteModalMessage.value = `Are you sure you want to delete the record for "${item.email}"? This action cannot be undone.`;
    pendingDeleteAction.value = () => deleteItem(item);
    showDeleteModal.value = true;
};

const confirmDeleteSelected = () => {
    if (itemsSelected.value.length === 0) return;

    const count = itemsSelected.value.length;
    const emails = itemsSelected.value.map(item => item.email).slice(0, 3).join(', ');
    const moreText = count > 3 ? ` and ${count - 3} more` : '';

    deleteModalMessage.value = `Are you sure you want to delete ${count} selected record${count > 1 ? 's' : ''}? (${emails}${moreText}) This action cannot be undone.`;
    pendingDeleteAction.value = () => deleteSelected();
    showDeleteModal.value = true;
};

const proceedWithDelete = async () => {
    if (pendingDeleteAction.value) {
        await pendingDeleteAction.value();
    }
    cancelDelete();
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteModalMessage.value = "";
    pendingDeleteAction.value = null;
};

// =====================
// Table Actions
// =====================
const deleteItem = async (item: Item) => {
    try {
        await axios.delete(`/sequence/${item.id}`);
        const index = items.value.findIndex(i => i.id === item.id);
        if (index !== -1) {
            items.value.splice(index, 1);
            toast.success(`Deleted: ${item.email}`);
            // This function would load data for the table
            getHistory();
        }
    } catch (err) {
        console.error('Delete failed:', err);
        toast.error('Failed to delete item');
    }
};


const getUnsubStatusIcon = (unsubToken: string | null) => {
    if (unsubToken) {
        return {icon: '✓', class: 'text-green-600 font-bold'};
    } else {
        return {icon: '✗', class: 'text-red-600 font-bold'};
    }
}


// Function to format relative date
const getRelativeDate = (dateString: string | null) => {
    if (!dateString) return {display: 'Not scheduled', class: 'text-gray-400'};

    try {
        const date = new Date(dateString);
        const now = new Date();
        // Strip out time, work with dates only
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const target = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const diffInMs = target.getTime() - today.getTime();
        const diffInDays = Math.round(diffInMs / (1000 * 60 * 60 * 24));

        const weekdays = ['Sun', 'Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat'];
        const dayName = weekdays[date.getDay()];

        // Today
        if (diffInDays === 0) {
            return {display: 'Today', class: 'text-blue-600 font-semibold'};
        }
        // Tomorrow
        if (diffInDays === 1) {
            return {display: 'Tomorrow', class: 'text-green-600'};
        }
        // Yesterday
        if (diffInDays === -1) {
            return {display: 'Yesterday', class: 'text-orange-600'};
        }

        // "This [Day]" or "Last [Day]" or "Next [Day]" (focus on Tue as example)
        const weekdayDiff = (target.getDay() - today.getDay());
        const sameWeek = Math.abs(diffInDays) <= 6 && (target > today ? target < new Date(today.getTime() + 7 * 86400000) : target > new Date(today.getTime() - 7 * 86400000));
        if (sameWeek) {
            if (diffInDays > 0) {
                return {display: `This ${dayName}`, class: 'text-green-600'};
            } else {
                return {display: `Last ${dayName}`, class: 'text-orange-600'};
            }
        }

        // "Next [Day]"
        if (diffInDays > 6 && diffInDays <= 13) {
            return {display: `Next ${dayName}`, class: 'text-green-600'};
        }

        // "Last [Day]"
        if (diffInDays < -6 && diffInDays >= -13) {
            return {display: `Last ${dayName}`, class: 'text-orange-600'};
        }

        // Future (e.g., "in 17 days")
        if (diffInDays > 13) {
            return {display: `In ${diffInDays} days`, class: 'text-green-600'};
        }
        // Past (e.g., "17 days ago")
        if (diffInDays < -13) {
            return {display: `${Math.abs(diffInDays)} days ago`, class: 'text-red-600'};
        }

        // Fallback: formatted date
        const month = date.toLocaleDateString('en-US', {month: 'short'});
        return {display: `${month} ${date.getDate()}, ${date.getFullYear()}`, class: 'text-gray-600'};
    } catch (e) {
        return {display: 'Invalid date', class: 'text-red-600'};
    }
};

const editItem = (item: Item) => {
    window.location.href = `/email-sequence/${item.id}/edit`;
};

const deleteSelected = async () => {
    if (itemsSelected.value.length > 0) {
        const selectedIds = itemsSelected.value.map(item => item.id);

        try {
            await axios.post('/sequence/bulk-delete', {
                ids: selectedIds
            });
            items.value = items.value.filter(item => !selectedIds.includes(item.id));
            itemsSelected.value = [];
            getHistory();
            toast.success(`${selectedIds.length} items deleted`);
        } catch (err) {
            console.error('Bulk delete failed:', err);
            toast.error('Failed to delete selected items');
        }
    }
};

// =====================
// Search
// =====================
const clearSearch = () => {
    searchField.value = "";
    searchValue.value = "";
};

// =====================
// Column Visibility Toggle
// =====================
const toggleColumnVisibility = (columnValue: string) => {
    if (visibleColumns.value.includes(columnValue)) {
        visibleColumns.value = visibleColumns.value.filter(col => col !== columnValue);
    } else {
        visibleColumns.value.push(columnValue);
    }
};
</script>
<style scoped>
.custom-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px 0;
}

.customize-table {
    --custom-selected: rgba(122, 201, 67, 0.2); /* Define the color for selected rows */
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

    --easy-table-body-row-hover-font-color: #2d3a4f !important;
    --easy-table-body-row-hover-background-color: #374151 !important;
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
