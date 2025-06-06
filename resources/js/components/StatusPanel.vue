<template>
   <div class="p-2 mt-4 border border-base-300 bg-base-200 shadow-lg rounded-lg">
    <div v-if="store.hasInsurance">
        <p>{{ store.message }}</p>
    </div>
    <div v-else>
        <p>No Insurance</p>
    </div>
    </div>
</template>

<script setup>
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();
</script>
