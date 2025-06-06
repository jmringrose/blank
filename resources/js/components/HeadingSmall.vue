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
import {errorhandler} from "../helpers/ErrorHandlerForAxios.js";

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
    <Toaster richColors position="top-center" />
    <nav class="bg-base-300 dark:base-100 p-1 border-base-100 dark:border-b-yellow-900 border mb-2 shadow-lg mb-4">
        <div class="max-w-5xl flex flex-wrap items-center justify-between mx-auto p-4">
            <span class="text-2xl font-semibold whitespace-nowrap">{{ title }}</span>
            <p v-if="description" class="text-muted-foreground ml-6 text-sm">
                {{ description }}
            </p>
            <button aria-controls="navbar-default" aria-expanded="false" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" data-collapse-toggle="navbar-default" type="button">
                <span class="sr-only">Open main menu</span>
                <svg aria-hidden="true" class="w-5 h-5" fill="none" viewBox="0 0 17 14" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1h15M1 7h15M1 13h15" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                </svg>
            </button>
            <div id="navbar-default" class="hidden w-full md:block md:w-auto">
                <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border rounded-lg  md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
                    <li v-if="link1 > ''">
                        <a class="block py-2 px-3 rounded-sm md:border-0 md:p-0 md:dark:hover:bg-transparent" :href="url1">{{ link1 }}</a>
                    </li>
                    <li v-if="link2 > ''">
                        <a class="block py-2 px-3 rounded-sm md:border-0 md:p-0 md:dark:hover:bg-transparent" :href="url2">{{ link2 }}</a>
                    </li>
                    <li>
                       <button class="btn btn-accent btn-sm font-semibold" @click="logout">Logout</button>
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
        </div>
    </nav>
</template>
