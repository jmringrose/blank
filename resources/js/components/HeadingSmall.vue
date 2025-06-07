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
    theme: string;
}
import {themeChange} from 'theme-change';
import { onMounted, ref } from 'vue';
import { useToast } from '../composables/useToast';
import axios from 'axios';
import {errorhandler} from "../composables/ErrorHandlerForAxios.js";

const props = defineProps<Props>();
const currentTheme = ref(props.theme || '');
const toast = useToast();

onMounted(() => {
    themeChange(false);
    // Set initial theme based on prop
    if (props.theme) {
        currentTheme.value = props.theme;
        document.querySelector('html').setAttribute('data-theme', props.theme);
    }
});
const logout = () => {
    axios.post('/logout', { viewer_name: props.name })
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
    axios.post('/theme', { id: id, theme: theme })
        .then(res => {
            toast.info("Color scheme saved [" + theme + "]");
        })
        .catch(err => {
            errorhandler(err);
        });
};
</script>
<template>

     <div class="navbar bg-base-300 shadow-sm">
        <div class="navbar-start max-w-5/6">
            <div class="dropdown">

                <button tabindex="0" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /> </svg>
                </button>

                <ul class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                    <li v-if="link1 > ''">
                        <a class="block mt-1 py-2 px-3 rounded-sm md:border-0 md:p-0 md:dark:hover:bg-transparent" :href="url1">{{ link1 }}</a>
                    </li>
                    <li v-if="link2 > ''">
                        <a class="block mt-1 py-2 px-3 rounded-sm md:border-0 md:p-0 md:dark:hover:bg-transparent" :href="url2">{{ link2 }}</a>
                    </li>
                    <li>
                        <select id="theme" name="theme" class="select select-sm bg-base-300"
                                v-model="currentTheme"
                                @change="storeAndToggleTheme(id, currentTheme)"
                                data-choose-theme>
                            <option value="abyss">Default</option>
                            <option value="retroish">Light</option>
                            <option value="luxury">Dark</option>
                        </select>
                    </li>

                </ul>
            </div>
            <a class="btn btn-ghost text-xl">James Template</a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li v-if="link1 > ''">
                    <a class="block mt-1 mr-4 py-2 px-3 rounded-sm md:border-0 md:p-0 md:dark:hover:bg-transparent" :href="url1">{{ link1 }}</a>
                </li>
                <li v-if="link2 > ''">
                    <a class="block mt-1 mr-4 py-2 px-3 rounded-sm md:border-0 md:p-0 md:dark:hover:bg-transparent" :href="url2">{{ link2 }}</a>
                </li>
            </ul>
        </div>
        <div class="navbar-end">
            <select id="theme" name="theme" class="mr-4 select select-sm bg-base-300 w-36"
                    v-model="currentTheme"
                    @change="storeAndToggleTheme(id, currentTheme)"
                    data-choose-theme>
                <option value="abyss">Default</option>
                <option value="retroish">Light</option>
                <option value="luxury">Dark</option>
            </select>
            <button class="btn btn-accent btn-sm font-semibold" @click="logout">Logout</button>
        </div>
    </div>


    <Toaster richColors position="top-center" />

</template>
