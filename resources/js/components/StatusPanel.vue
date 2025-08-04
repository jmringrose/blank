<template>
   <div class="p-2 mt-4 h-12 border border-base-300 text-base-content bg-base-200 shadow-lg rounded-lg">
    <div v-if="store.hasInsurance === false && store.message === ''">
        <p>Click [Mark as Insured] to change record</p>
    </div>
    <div v-else>
        <p>{{ store.message }}</p>
    </div>
   </div>
</template>

<script setup>
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();
</script>
