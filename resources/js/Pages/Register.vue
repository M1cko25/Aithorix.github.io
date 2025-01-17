<script setup>
import icon from '../Icons.js';
import graphics from '../graphics.js';
import Button from '../Components/Button.vue'
import TextField from '../Components/TextField.vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm, router } from '@inertiajs/vue3';

const goBack =() => {(window.history.length > 1) ? window.history.back() : Inertia.visit('/');}

const  handleSubmit = () => {
    
}

const verify = ()=> {
    router.visit('/verification')
}
</script>
<template>
    <Head title="Sign Up"/>
    <div class="min-h-screen flex items-center justify-center">
        <button @click="goBack" class="flex flex-row items-center gap-4 absolute top-4 left-4">
            <img :src="icon.leftIcon">
            <p>Back</p>
        </button>
        <div class="bg-light rounded-xl shadow-lg overflow-hidden flex max-w-4xl w-full">
            <!-- Left side - Form -->
            <div class="w-full lg:w-1/2 p-6">
                <div class="pb-10 px-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Register</h1>
                    <p class="text-gray-600 mb-6">Register with open account</p>
                    
                    <!-- OAuth Buttons -->
                    <div class=" mb-6 flex flex-col gap-2 border">
                        <Button social :pic="icon.googleIcon" :style="`py-2 w-full text-lg`" :href="route('googleLogin')" text="Google" />
                        <Button social :pic="icon.microsoftIcon" :style="`py-2 w-full text-lg`" text="Microsoft" />
                    </div>

                    <div class="relative flex flex-col gap-3">
                        <div class="inset-0 flex items-center">
                            <div class="w-full border-t border-dark"></div>
                        </div>
                        <div class="relative flex text-sm">
                            <span class="px-2 bg-light text-lg text-gray-500">or continue with email address</span>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="handleSubmit" class="flex flex-col gap-8 p-4">
                    <TextField :icon="icon.emailIcon" label="Email" type="email" labeltxt="Email" name="email" placeholder="jon@email.com"/>
                    
                    <Button :click="verify" text="Submit" :style="`py-2 w-full text-lg`"/>
                    </form>
                </div>

                <div class="w-full text-center">
                    <p>Already have an account? <Link :href="route('login')" class="underline font-bold text-lg">Log in</Link></p>
                </div>
            </div>
            <!-- Right side - Illustration -->
            <div class="hidden lg:block lg:w-1/2 bg-light p-6">
                <img :src="graphics.signupIllustration" alt="Illustration" class="w-full h-full object-contain" />
            </div>
        </div>
    </div>
</template>