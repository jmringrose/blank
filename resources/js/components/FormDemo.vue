<template>
    <form @submit.prevent="handleSubmit" class="text-base-content">
        <div class="text-xl text-center font-bold">Test Form</div>
        <fieldset class="fieldset ml-6">
            <legend class="fieldset-legend">Name</legend>
            <input id="name" v-model="formData.name" class="input" placeholder="Your name" required type="text"/>
            <p class="label">You can edit your name later on from settings</p>
            <legend class="fieldset-legend">Page title</legend>
            <input class="input" placeholder="My awesome page" type="text"/>
            <p class="label">You can edit page title later on from settings</p>
            <legend class="fieldset-legend">Email</legend>
            <input id="email" v-model="formData.email" class="input" required type="email">
            <legend class="fieldset-legend">Browser</legend>
            <input class="input" list="browsers" placeholder="Which browser do you use" type="text"/>
            <datalist id="browsers">
                <option value="Chrome"></option>
                <option value="Firefox"></option>
                <option value="Safari"></option>
                <option value="Opera"></option>
                <option value="Edge"></option>
            </datalist>
            <legend class="fieldset-legend">Choose a file</legend>
            <input class="file-input file-input-sm" required type="file"/>
            <legend class="fieldset-legend">Choose a date</legend>
            <input ref="myDatepicker" class="input pika-single" type="text" value="Pick a day"/>
        </fieldset>
        <div class="text-center">
             <button class="btn btn-sm btn-neutral mt-6" type="submit">Submit</button>
        </div>
    </form>
</template>
<script setup>
import {onMounted, reactive, ref} from 'vue';
import Pikaday from 'pikaday';

const myDatepicker = ref(null);
const formData = reactive({
    name: '',
    email: ''
});
onMounted(() => {
    const picker = new Pikaday({
        field: myDatepicker.value
    });
});
const handleSubmit = () => {
    // Perform actions with formData, e.g., send to an API
    console.log('Form submitted:', formData);
    alert(`Form submitted!\nName: ${formData.name}\nEmail: ${formData.email}`);
    // Optionally, reset the form after submission
    formData.name = '';
    formData.email = '';
};
</script>
