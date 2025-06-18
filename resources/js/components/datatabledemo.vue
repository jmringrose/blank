<template>

    <div class="flex mb-4">
        <div class="mr-4">
            <button :disabled="!itemsSelected.length" class="btn btn-error" @click="deleteSelected">
                Delete Selected
            </button>
        </div>
        <span class="mr-2 mt-2">Search specific field:</span>
        <select v-model="searchField" class="ml-2 mr-4 select w-46 ">
        <option selected value="">Pick Something</option>
        <option selected>Name</option>
        <option value="age">Age</option>
        </select>
        <div class="mr-2 mt-2">Search value: </div>
        <div><input v-model="searchValue" class="ml-2 w-24 input" type="text"></div>
        <div class="mr-4 ml-4">
            <button class="btn btn-alternative" @click="clearSearch">
                Clear Search
            </button>
        </div>
    </div>

    <EasyDataTable
        v-model:items-selected="itemsSelected"
        :headers="headers"
        :items="items"
        :search-field="searchField"
        :search-value="searchValue"
        alternating
        body-text-direction="center"
        buttons-pagination
        header-text-direction="center"
        table-class-name="customize-table"
        theme-color="#f48225"
    >
<!--

  'when_event', 'email', 'role', 'result', 'user_id', 'user_ip','location'
  -->
           <template #expand="item">
            <div class="mt-2 ml-12">
                {{ item.role }} (Role)
            </div>
            <div class="mt-2 ml-12">
                {{ item.result }} (Result)
            </div>
            <div class="mt-2 ml-12">
                {{ item.user_id }} (User ID)
            </div>
            <div class="mt-2 ml-12">
                {{ item.user_ip }} (IP Address)
            </div>
               <div class="mt-2 ml-12">
                {{ item.location }} (Location)
            </div>
        </template>
        <!-- twisty for extra info -->
        <template #item-actions="item">
            <div class="flex justify-center">
                <button class="btn btn-sm" @click.stop="deleteItem(item)">Delete</button>
            </div>
        </template>
    </EasyDataTable>
    items selected:<br/>
    {{ itemsSelected }}
</template>
<script lang="ts" setup>
import axios from 'axios';
import {ref, onMounted} from 'vue';
import type {Item} from "vue3-easy-data-table";

const searchField = ref("");
const searchValue = ref("");
const itemsSelected = ref<Item[]>([]);
const sortBy = "Age";
const sortType = "desc";

const showRow = (item) => {
    alert(JSON.stringify(item));
};
// Fix the getHistory function
const items = ref([]);

const getHistory = () => {
    console.log('getting history');
    axios.get('/history')
        .then((res) => {
            items.value = res.data; // Set the items with the response data
        })
        .catch((error) => {
            console.error('Error fetching history:', error);
            items.value = []; // Set empty array on error
        });
};

// Call getHistory in onMounted
onMounted(() => {
    getHistory();
});
const deleteItem = (item) => {
    const index = items.value.findIndex(i => i.name === item.name);
    if (index !== -1) {
        items.value.splice(index, 1);
    }
};

const clearSearch = () => {
    searchField.value = "";
    searchValue.value = "";
}

const deleteSelected = () => {
    if (itemsSelected.value.length > 0) {
        const selectedNames = itemsSelected.value.map(item => item.name);
        items.value = items.value.filter(item => !selectedNames.includes(item.name));
        itemsSelected.value = [];
    }
};

const headers = [

    // 'id','app_id','module_name','verb','event', 'email', 'name', 'when_event', 'email', 'role', 'result', 'user_id', 'user_ip','location'
    {text: "ID", value: "id", width: 50, fixed: true},
    {text: "Module", value: "module_name", width: 200},
    {text: "Verb", value: "verb", sortable: true, width: 130},
    {text: "Event", value: "event", width: 100},
    {text: "email", value: "email", width: 150},
    {text: "Actions", value: "actions", width: 100}
];
</script>
<style>
.customize-table {
    --easy-table-border: 1px solid #445269;
    --easy-table-row-border: 1px solid #445269;

    --easy-table-header-font-size: 14px;
    --easy-table-header-height: 50px;
    --easy-table-header-font-color: #c1cad4;
    --easy-table-header-background-color: #2d3a4f;

    --easy-table-header-item-padding: 10px 15px;

    --easy-table-body-even-row-font-color: #ffffff;
    --easy-table-body-even-row-background-color: #33435d;

    --easy-table-body-row-font-color: #c0c7d2;
    --easy-table-body-row-background-color: #2d3a4f;
    --easy-table-body-row-height: 50px;
    --easy-table-body-row-font-size: 14px;

    --easy-table-body-row-hover-font-color: #2d3a4f;
    --easy-table-body-row-hover-background-color: rgba(102, 160, 183, 0.8);

    --easy-table-body-item-padding: 10px 15px;

    --easy-table-footer-background-color: #2d3a4f;
    --easy-table-footer-font-color: #c0c7d2;
    --easy-table-footer-font-size: 14px;
    --easy-table-footer-padding: 0px 10px;
    --easy-table-footer-height: 50px;

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
