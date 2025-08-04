<template>
    <div class="navbar shadow-sm">
        <div class="navbar-start max-w-5/6 text-primary-content">
            <a class="hidden mr-4 ml-4 ">Template</a>
            <a class="hidden md:flex mr-4  align-middle" href="/home"><span class="material-symbols-outlined align-top">home</span><span class="hidden md:block ml-2">Home</span></a>
            <div class="dropdown">
                <button class="btn btn-ghost block md:hidden" tabindex="0">
                    <span class="material-symbols-outlined">Menu</span>
                </button>
                <ul class="menu menu-sm dropdown-content bg-base-300  rounded-box z-1 mt-3 w-52 p-2 shadow-lg border">
                    <li>
                        <a href="/home" class="mt-1 mr-2"><span class="material-symbols-outlined align-top">home</span> Home</a>
                    </li>
                    <li v-if="link1 > ''" class="mt-1 mr-2 ">
                        <a :href="url1" class=""><span class="material-symbols-outlined">Palette</span> {{ link1 }}</a>
                    </li>
                    <li v-if="link2 > ''"  class="mt-1 mr-2">
                        <a :href="url2" class=""><span class="material-symbols-outlined">Database</span> {{ link2 }}</a>
                    </li>
                    <li v-if="link3 > ''"  class="mt-1 mr-2 ">
                        <a :href="url3" class=""><span class="material-symbols-outlined">Table</span> {{ link3 }}</a>
                    </li>
                    <li v-if="link4 > ''"  class="mt-1 mr-2 ">
                        <a :href="url4" class=""><span class="material-symbols-outlined">Ballot</span> {{ link4 }}</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="navbar-center hidden md:flex">
            <ul class="menu menu-horizontal px-1 text-primary-content ">
                <li v-if="link1 > ''" class="mt-1 mr-2 ">
                    <a :href="url1" class=""><span class="material-symbols-outlined">Palette</span> {{ link1 }}</a>
                </li>
                <li v-if="link2 > ''"  class="mt-1 mr-2 ">
                    <a :href="url2" class=""><span class="material-symbols-outlined">Database</span> {{ link2 }}</a>
                </li>
                <li v-if="link3 > ''"  class="mt-1 mr-2 ">
                    <a :href="url3" class=""><span class="material-symbols-outlined">Table</span> {{ link3 }}</a>
                </li>
                <li v-if="link4 > ''"  class="mt-1 mr-2 ">
                    <a :href="url4" class=""><span class="material-symbols-outlined">Ballot</span> {{ link4 }}</a>
                </li>
            </ul>
        </div>
        <div class="navbar-end text-accent">
            <switcher :id=props.id :theme=props.theme></switcher>
            <button class="ml-2 btn btn-sm btn-secondary font-semibold" @click="logout">Logout</button>
        </div>
    </div>
</template>
<script lang="ts" setup>
interface Props {
    title: string;
    description?: string;
    name: string;
    id: string;
    link1: string;
    url1: string;
    link2: string;
    url2: string;
    link3: string;
    url3: string;
    link4: string;
    url4: string;
    theme: string;
}

import switcher from './ColorSwitcher.vue'
import {themeChange} from 'theme-change';
import {useToast} from "vue-toastification";
import {onMounted, ref} from 'vue';
import axios from 'axios';
import {errorhandler} from "../composables/ErrorHandlerForAxios.js";

const props = defineProps<Props>();
const toast = useToast();
const currentTheme = ref(props.theme || '');

onMounted(() => {
    themeChange(false);
    // Set initial theme based on prop
    if (props.theme) {
        currentTheme.value = props.theme;
        document.querySelector('html').setAttribute('data-theme', props.theme);
    }
});
const logout = () => {
    axios.post('/logout', {viewer_name: props.name})
        .then(res => {
            window.location.href = '/home';
        })
        .catch(err => {
            // no matter what the reply
            window.location.reload();
        });
}
const storeAndToggleTheme = (id, theme) => {
    // check user_id is here
    if (id === '') {
        toast.error("You don't appear to be logged in! Please refresh your browser.");
        return;
    }
    // Save theme preference
    axios.post('/theme', {id: id, theme: theme})
        .then(res => {
            toast.info("Color scheme saved [" + theme + "]", {duration: 100});
        })
        .catch(err => {
            errorhandler(err);
        });
};
</script>
