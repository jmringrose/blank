<template>


    <div v-if="store.hasInsurance">
       <button class="btn btn-accent text-utility-content" @click="markAsUninsured">Mark as Uninsured</button>
    </div>
    <div v-else>
       <button class="btn btn-accent text-accent-content" @click="markAsInsured">Mark as Insured</button>
    </div>

</template>

<script setup>
import { useStatusStore } from '../stores/statusStore';
const store = useStatusStore();

function markAsInsured() {
    store.updateInsuranceStatus(true, 'User now has insurance.');
}
function markAsUninsured() {
    store.updateInsuranceStatus(false,'User now does not have insurance.');
}
</script>
