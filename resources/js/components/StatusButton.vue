<template>
    <button class="btn btn-accent" @click="markAsInsured">Mark as Insured</button>
</template>

<script setup>
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();

function markAsInsured() {
    const newCount = store.incrementCount();
    store.updateInsuranceStatus('User now has insurance.', newCount);
}
</script>
