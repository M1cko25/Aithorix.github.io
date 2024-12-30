<script setup>
import Button from '../Components/Button.vue'
import TextField from '../Components/TextField.vue'
import icon from '../Icons.js';
import logo from '../../../public/assets/logo.png'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const fullName = ref('')
const password = ref('')
const confirmPassword = ref('')

const handleSubmit = () => {
  router.visit('/template-selection')
}
</script>

<template>
    <Head title="Account Setup"/>
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-light rounded-xl shadow-lg w-full max-w-md p-8">
            <!-- Logo -->
            <div class="flex justify-center mb-8 w-fit relative left-1/2 -translate-x-1/2 rounded-lg bg-white p-4 shadow-md">
                <img :src="logo" alt="Logo" class="w-16 h-16" />
            </div>

            <!-- Content -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Welcome to Aithorix</h1>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="flex flex-col gap-6">
                <TextField 
                    v-model="fullName" 
                    :icon="icon.userIcon" 
                    label="fullName" 
                    type="text" 
                    labeltxt="Full Name"
                    placeholder="Jon Doe"
                />
                
                <TextField 
                    v-model="password" 
                    :icon="icon.passwordIcon" 
                    label="password" 
                    type="password" 
                    labeltxt="Password"
                    placeholder="Password"
                />
                
                <TextField 
                    v-model="confirmPassword" 
                    :icon="icon.passwordIcon" 
                    label="confirmPassword" 
                    type="password" 
                    labeltxt="Confirm Password"
                    placeholder="Confirm Password"
                />

                <Button 
                    text="Register" 
                    :click="handleSubmit"
                    :style="`py-2 w-full text-lg mt-4`" 
                    type="submit"
                />
            </form>
        </div>
    </div>
</template>