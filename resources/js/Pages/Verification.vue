<script setup>
import Button from '../Components/Button.vue'
import OtpInput from '../Components/OtpInput.vue'
import logo from '../../../public/assets/logo.png'
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from '../../../vendor/tightenco/ziggy/src/js'

const { props } = usePage();
const otp = ref(['', '', '', ''])

let errorMessage = ref(null);

const handleVerify = () => {
    let otpValue = Number(otp.value.join(''))
    if (otpValue === props.code) {
        router.visit(route('verify', { email: props.email }))
    } else if (otp.value.join('').length != 4){
        errorMessage.value = 'Code must be 4 digits'
    } else {
        errorMessage.value = 'Incorrect code'
    }
}

const goBack =() => {(window.history.length > 1) ? window.history.back() : Inertia.visit('/');}
</script>

<template>
    <Head title="Verification"/>
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-light rounded-xl shadow-lg w-full max-w-md p-8">
            <!-- Logo -->
            <div class="flex justify-center mb-8 w-fit relative left-1/2 -translate-x-1/2 rounded-lg bg-white p-4 shadow-md">
                <img :src="logo" alt="Logo" class="w-16 h-16" />
            </div>

            <!-- Content -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Enter verification code</h1>
                <p class="text-gray-600">We've sent a code to {{ props.email }}</p>
            </div>

            <!-- OTP Input -->
            <div class="mb-8">
                <OtpInput v-model="otp" />
                <p v-if="errorMessage" class="text-red-600 text-sm text-center">{{ errorMessage }}</p>
            </div>

            <!-- Resend Link -->
            <div class="text-center mb-8">
                <p class="text-gray-600">
                    Didn't get a code?
                    <button 
                        @click="handleResend"
                        class="text-gray-800 font-semibold ml-1 hover:underline"
                    >
                        Click to resend
                    </button>
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between gap-4">
                <Button 
                    text="Cancel" 
                    :btn2="true" 
                    :style="`w-full p-2`"
                    social
                    @click="goBack"
                />
                <Button 
                    text="Verify" 
                    :style="`w-full p-2`"
                    @click="handleVerify"
                />
            </div>
        </div>
    </div>
</template>