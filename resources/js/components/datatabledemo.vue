<template>

    <div class="flex mb-4">
        <div class="mr-4">
            <button :disabled="!itemsSelected.length" class="btn btn-error" @click="deleteSelected">
                Delete Selected
            </button>
        </div>
        <span class="mr-2 mt-2">Search field:</span>
        <select v-model="searchField" class="ml-2 mr-4 select w-46 ">
        <option selected value="">Pick Something</option>
        <option selected>Name</option>
        <option value="age">Age</option>
        </select>
        <div class="mr-2 mt-2">Search value: </div>
        <div><input v-model="searchValue" class="ml-2 w-24 input" type="text"></div>
        <div class="mr-4">
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
        theme-color="#1d90ff"
    >
        <template #expand="item">
            <div class="mt-2 ml-12">
                {{ item.age }} (years)
            </div>
            <div class="mt-2 ml-12">
                {{ item.weight }} (lbs)
            </div>
            <div class="mt-2 ml-12">
                {{ item.name }} (persons name)
            </div>
            <div class="mt-2 ml-12">
                {{ item.favouriteFruits }} (fruit)
            </div>
        </template>
        <!-- twisty for extra info -->
        <template #item-actions="item">
            <div class="flex justify-center">
                <button class="btn btn-sm btn-error" @click.stop="deleteItem(item)">Delete</button>
            </div>
        </template>
    </EasyDataTable>
    items selected:<br/>
    {{ itemsSelected }}
</template>
<script lang="ts" setup>
import {ref} from 'vue';
import type {Item} from "vue3-easy-data-table";

const searchField = ref("");
const searchValue = ref("");
const sortBy = "Age";
const sortType = "desc";
const itemsSelected = ref<Item[]>([]);

const showRow = (item) => {
    alert(JSON.stringify(item));
};

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

const mockClientItems = (itemsNumber = 100) => {
    const mockItems = [];
    const sports = ["basketball", "football", "running", "swimming"];
    const fruits = ["banana", "apple", "orange", "peach"];

    for (let i = 1; i < itemsNumber + 1; i += 1) {
        mockItems.push({
            name: `name-${i}`,
            address: `address-${i}`,
            height: i,
            weight: i * 4,
            age: i,
            favouriteSport: sports[i % 4],
            favouriteFruits: fruits[i % 4]
        });
    }
    return mockItems;
};

const headers = [
    {text: "Name", value: "name", width: 150, fixed: true},
    {text: "Address", value: "address", width: 200},
    {text: "Height", value: "height", sortable: true, width: 100},
    {text: "Favourite sport", value: "favouriteSport", width: 100},
    {text: "Favourite fruits", value: "favouriteFruits", width: 100},
    {text: "Actions", value: "actions", width: 100}
];

const items = ref(mockClientItems());
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
    --easy-table-body-row-hover-background-color: #eeeeee;

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
    --easy-table-scrollbar-thumb-color: #4c5d7a;;
    --easy-table-scrollbar-corner-color: #2d3a4f;

    --easy-table-loading-mask-background-color: #2d3a4f;
}
</style>
