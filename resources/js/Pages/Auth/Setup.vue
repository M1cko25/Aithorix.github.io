<script setup>
import Button from '@/Components/Button.vue'
import TextField from '@/Components/TextField.vue'
import icon from '../../Icons.js';
import logo from '@/images/Logo.png'
import { ref } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import { route } from '../../../../vendor/tightenco/ziggy/src/index.js';

const page = usePage();

const url = ref(page.props.url)

const props = defineProps({
    email: {
        type: String,
        default: ''
    },
    name: String,
    avatar: String,
    google_id: String
})

const form = useForm({
    email: page.props.flash.email,
    name: props.name,
    password: null,
    password_confirmation: null,
    avatar: props.avatar,
    google_id: props.google_id
})

const submit = () => {
    form.post(route('create-account'), {
        preserveState: true,
    })
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
            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div>
                    <TextField 
                    v-model="form.name" 
                    :icon="icon.userIcon" 
                    label="fullName" 
                    type="text"
                    name="fullName"
                    labeltxt="Full Name"
                    placeholder="Jon Doe"
                    />
                    <p v-if="$page.props.errors.name" class="text-red-600 text-sm">{{ $page.props.errors.name }}</p>
                </div>
                
                <div>
                    <TextField 
                    v-model="form.password" 
                    :icon="icon.passwordIcon" 
                    label="password" 
                    type="password" 
                    name="password"
                    labeltxt="Password"
                    placeholder="Password"
                    />
                    <p v-if="$page.props.errors.password" class="text-red-600 text-sm">{{ $page.props.errors.password }}</p>
                </div>
                
                <div>
                    <TextField 
                    v-model="form.password_confirmation" 
                    :icon="icon.passwordIcon" 
                    label="confirmPassword" 
                    name="password_confirmation"
                    type="password" 
                    labeltxt="Confirm Password"
                    placeholder="Confirm Password"
                    />
                    <p v-if="$attrs.errors.confirmPassword" class="text-red-600 text-sm">{{ $attrs.errors.confirmPassword }}</p>
                </div>

                <Button type="submit" :style="`btn-primary w-full text-lg mt-4`">
                    Register    
                </Button>
            </form>
        </div>
        <Modal v-if="form.processing" v-model:modelValue="form.processing" class="absolute bg-light rounded-xl shadow-lg p-6 flex flex-col gap-4">
            <h1>We're making your account please wait</h1>
            <div class="flex justify-center items-center">
                <div class="flex flex-row gap-2">
                    <div class="w-4 h-4 rounded-full bg-blue animate-bounce"></div>
                    <div class="w-4 h-4 rounded-full bg-blue animate-bounce [animation-delay:-.3s]"></div>
                    <div class="w-4 h-4 rounded-full bg-blue animate-bounce [animation-delay:-.5s]"></div>
                </div>
            </div>
        </Modal>
    </div>
</template>