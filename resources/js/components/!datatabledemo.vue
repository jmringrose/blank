.customize-table {
--custom-selected: rgba(122, 201, 67, 0.2); /* Define the color for selected rows */
}<template>
    <!-- header stuff  -->
    <div class="flex mb-4 text-base-content">
        <div class="mr-4">
            <button :disabled="!itemsSelected.length" class="btn btn-error btn-sm" @click="deleteSelected">
                Delete
            </button>
        </div>
        <span class="mr-2 mt-2 text-sm">Search specific field:</span>
        <select v-model="searchField" class="ml-2 mr-4 select bg-secondary select-sm w-32 ">
        <option selected value="">Pick Something</option>
        <option v-for="header in headers" :key="header.value" :value="header.value">
            {{ header.text }}
        </option>
        </select>

        <div class="mt-2 text-sm">Search value: </div>


        <div><input v-model="searchValue" class="ml-2 w-32 input bg-base-200 input-sm" type="text"></div>


        <div class="mr-2 ml-2">
            <button class="btn btn-secondary btn-sm w-32" @click="clearSearch">
                Clear Search
            </button>
        </div>


        <!-- Column visibility dropdown -->
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-sm btn-secondary">
                Columns
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </label>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-200 rounded-box w-52">
                <li v-for="header in headers" :key="header.value">
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            :checked="visibleColumns.includes(header.value)"
                            @change="toggleColumnVisibility(header.value)"
                            class="checkbox checkbox-sm"
                        />
                        <span class="ml-2">{{ header.text }}</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>

    <EasyDataTable
        v-model:items-selected="itemsSelected"
        :headers="filteredHeaders"
        :items="items"
        :rows-per-page="15"
        :expand-column-width=10
        :search-field="searchField"
        :search-value="searchValue"
        :row-class-name="rowClass"
        theme-color="#1d90ff"
        alternating
        body-text-direction="center"
        header-text-direction="center"
        table-class-name="customize-table"
    >

        <template #expand="item">
            <div class="ml-8 border border-zinc-400 rounded-xl p-2 pb-6 w-128 shadow-lg bg-base-300">
                <h3 class="text-lg font-bold mb-4 ml-4">Additional Details</h3>
                <div class="mt-2 ml-4">
                    {{ item.name }} (Name)
                </div>
                <div class="mt-2 ml-4">
                    {{ item.role }} (Role)
                </div>
                <div class="mt-2 ml-4">
                    {{ item.result }} (Result)
                </div>
                <div class="mt-2 ml-4">
                    {{ item.user_id }} (User ID)
                </div>
                <div class="mt-2 ml-4">
                    {{ item.user_ip }} (IP Address)
                </div>
                <div class="mt-2 ml-4">
                    {{ item.location }} (Location)
                </div>
            </div>
        </template>

        <template #item-actions="item">
            <div class="flex justify-center">
                <button class="btn btn-sm btn-secondary h-6 w-6 mr-1" @click.stop="deleteItem(item)"><span class="!text-base material-symbols-outlined">delete</span></button>
                <button class="btn btn-sm btn-secondary h-6 w-6  mr-1" @click.stop="editItem(item)"><span class="!text-base material-symbols-outlined">edit</span></button>
<!--                <button class="btn btn-sm btn-secondary" @click.stop="deleteItem(item)"><span class="!text-base material-symbols-outlined">file_copy</span></button>-->
            </div>
        </template>

        <template #pagination="{ prevPage, nextPage, isFirstPage, isLastPage }">
            <div class="custom-pagination">
                <button
                    :disabled="isFirstPage"
                    @click="prevPage"
                    class="rounded-pagination-button"
                >
                    Prev
                </button>
                <button
                    :disabled="isLastPage"
                    @click="nextPage"
                    class="rounded-pagination-button"
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
</template>
<script lang="ts" setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import type { Item } from "vue3-easy-data-table";
import { useToast } from "vue-toastification";

// Toast instance
const toast = useToast();

// =====================
// Table Headers
// =====================
const headers = [
    { text: "Email", value: "email", sortable: true, width: 150 },
    { text: "Module", value: "module_name", sortable: true, width: 200 },
    { text: "Verb", value: "verb", sortable: true, width: 175 },
    { text: "Event", value: "event", width: 150 },
    { text: "Actions", value: "actions", width: 100 }
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
    axios.get('/history')
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
// Table Actions
// =====================
const deleteItem = (item: Item) => {
    // Assuming each item has a unique identifier, ideally an ID
    // If email is being used as a unique identifier, be cautious about duplicates
    const index = items.value.findIndex(i => i.email === item.email && i.event === item.event);
    if (index !== -1) {
        items.value.splice(index, 1);
        toast.success(`Deleted item: ${item.email}`);
    }
};

const editItem = (item: Item) => {
    toast.success(`This would whisk you away to edit the record: ${item.email}`);
};

const deleteSelected = () => {
    if (itemsSelected.value.length > 0) {
        // Create a more specific identifier using both email and event
        const selectedIdentifiers = itemsSelected.value.map(item => `${item.email}-${item.event}`);
        items.value = items.value.filter(item => !selectedIdentifiers.includes(`${item.email}-${item.event}`));
        itemsSelected.value = [];
        toast.success(`${selectedIdentifiers.length} items deleted`);
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

    --easy-table-body-row-hover-font-color: #2d3a4f;
    --easy-table-body-row-hover-background-color: rgba(202, 217, 232, 0.8);
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
