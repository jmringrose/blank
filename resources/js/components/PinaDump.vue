<template>
    <div class="mt-4">
        <h1>Pinia Store Dump Example</h1>
        <button @click="counter.increment">Increment</button>
        <StoreDump :store="counter" />
    </div>
</template>

<script setup lang="ts">
import { useCounterStore } from './stores/statusStore';
import StoreDump from './components/StoreDump.vue';

const counter = useCounterStore();
</script>
