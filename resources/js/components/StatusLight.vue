<template>
   <div class="p-2 mt-4 border border-base-300 text-base-content bg-base-200 shadow-lg rounded-lg">
    <div class="">
        <div class="ml-2">State of Insurance <span class="indicator-item status h-6 w-6 mt-0 mr-2" v-bind:class="{ 'status-success':  store.hasInsurance  }"></span></div>

        <div class="h-24 w--full font-bold place-items-center p-2"
             v-bind:class="store.hasInsurance ?  'text-base-content' : 'text-default-content'">{{ store.message }}</div>
    </div>
   </div>
</template>

<script setup>
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();
</script>
