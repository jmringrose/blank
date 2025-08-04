<template>
    <div class="text-base-content mt-4 ml-4">
        <h2>Store Dump</h2>
        <pre>{{ storeData }}</pre>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();
const storeData = computed(() => {
    return JSON.stringify(store.$state, null, 2);
});
</script>
