<template>
   <div class="p-2 mt-4 border border-base-300 bg-base-200 shadow-lg rounded-lg">
    <div class="indicator">
        <span class="indicator-item status" v-bind:class="{ 'status-success':  store.hasInsurance  }"></span>
        <div class="bg-base-300 grid h-32 w-32 place-items-center p-2" v-bind:class="store.hasInsurance ?  'font-bold text-utility-content' : 'font-bold text-accent-content'">{{ store.message }}</div>
    </div>
   </div>
</template>

<script setup>
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();
</script>
