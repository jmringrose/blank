<template>
    <div class="navbar shadow-sm">
        <div class="container flex mx-auto">
            <div class="navbar-start text-primary-content">
                <a class="hidden mr-4 ml-4 ">Template</a>
                <a class="hidden md:flex mr-4  align-middle" href="/dashboard"><span class="material-symbols-outlined align-top">home</span><span class="hidden md:block ml-2">Dashboard</span></a>

               <!-- mobile menu -->
                <div class="dropdown">
                    <button class="btn btn-ghost block md:hidden" tabindex="0">
                        <span class="material-symbols-outlined">Menu</span>
                    </button>
                    <ul class="menu menu-sm dropdown-content bg-base-300  rounded-box z-1 mt-3 w-52 p-2 shadow-lg border">

                        <li class="mt-1 mr-2">
                            <details>
                                <summary><span class="material-symbols-outlined">Ballot</span> Marketing</summary>
                                <ul>
                                    <li><a href="/sequences">Marketing Sequences</a></li>
                                    <li><a href="/marketing-steps">Marketing Steps</a></li>
                                </ul>
                            </details>
                        </li>

                        <li class="mt-1 mr-2">
                            <a href="/formdata" class=""><span class="material-symbols-outlined">Database</span> Surveys</a>
                        </li>

                        <li class="mt-1 mr-2">
                            <details>
                                <summary><span class="material-symbols-outlined">Table</span> Newsletters</summary>
                                <ul>
                                    <li><a href="/newsletter-sequences">Newsletter Sequences</a></li>
                                    <li><a href="/newsletter-steps">Newsletter Steps</a></li>
                                </ul>
                            </details>
                        </li>


                        <li class="mt-1 mr-2">
                            <details>
                                <summary><span class="material-symbols-outlined">Help</span> Questions</summary>
                                <ul>
                                    <li><a href="/question-sequences">Questioners</a></li>
                                    <li><a href="/question-steps">Question Emails</a></li>
                                </ul>
                            </details>
                        </li>



                    </ul>
                </div>
            </div>

            <div class="navbar-center hidden md:flex">
                <ul class="menu menu-horizontal px-1 text-primary-content ">
<!--
          <li v-if="link1 > ''" class="mt-1 mr-2 ">
                        <a :href="url1" class=""><span class="material-symbols-outlined">Palette</span> {{ link1 }}</a>
                    </li>-->

                    <li class="dropdown dropdown-hover">
                        <div tabindex="0" role="button" class="btn btn-ghost">
                            <span class="material-symbols-outlined">Email</span> Marketing
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-300 rounded-box z-[1] w-52 p-2 shadow border border-primary-content">
                            <li><a href="/sequences">Marketing Sequences</a></li>
                            <li><a href="/marketing-steps">Marketing Steps</a></li>
                        </ul>
                    </li>

                    <li class="mt-1 mr-2">
                        <a href="/formdata" class=""><span class="material-symbols-outlined">Table</span> Surveys</a>
                    </li>

                    <li class="dropdown dropdown-hover">
                        <div tabindex="0" role="button" class="btn btn-ghost">
                            <span class="material-symbols-outlined">Email</span> Newsletters
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-300 rounded-box z-[1] w-52 p-2 shadow border border-primary-content">
                            <li><a href="/newsletter-sequences">Newsletter Sequences</a></li>
                            <li><a href="/newsletter-steps">Newsletter Steps</a></li>
                        </ul>
                    </li>

                    <li class="dropdown dropdown-hover">
                        <div tabindex="0" role="button" class="btn btn-ghost">
                            <span class="material-symbols-outlined">Help</span> Questions
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-300 rounded-box z-[1] w-52 p-2 shadow border border-primary-content">
                            <li><a href="/question-sequences">Questioners</a></li>
                            <li><a href="/question-steps">Question Emails</a></li>
                        </ul>
                    </li>



                </ul>
            </div>
            <div class="navbar-end text-accent">
                <switcher :id=props.id :theme=props.theme></switcher>
                <button class="ml-2 btn btn-sm btn-secondary font-semibold" @click="logout">Logout</button>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
interface Props {
    title: string;
    description?: string;
    name: string;
    id: string;
    theme: string;
}

import switcher from './ColorSwitcher.vue'
import {themeChange} from 'theme-change';
import {useToast} from "vue-toastification";
import {onMounted, ref} from 'vue';
import axios from 'axios';
import {errorhandler} from "../composables/ErrorHandlerForAxios.js";
import { useAuthStore } from '@/stores/auth';

const props = defineProps<Props>();
const toast = useToast();
const auth = useAuthStore();
const currentTheme = ref(props.theme || '');

onMounted(() => {
    themeChange(false);
    // Set initial theme based on prop
    if (props.theme) {
        currentTheme.value = props.theme;
        document.querySelector('html').setAttribute('data-theme', props.theme);
    }
});
const logout = async () => {
    // Clear client-side data first
    auth.logout()
    localStorage.removeItem('api_token')
    delete axios.defaults.headers.common['Authorization']

    // Create form to submit to web logout route (destroys session)
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = '/logout'

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (csrfToken) {
        const csrfInput = document.createElement('input')
        csrfInput.type = 'hidden'
        csrfInput.name = '_token'
        csrfInput.value = csrfToken
        form.appendChild(csrfInput)
    }

    document.body.appendChild(form)
    form.submit()
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
