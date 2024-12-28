<script setup>
import Button from '../Components/Button.vue'
import signupIllustration from '../../../public/assets/signup-illustration.svg'
import googleIcon from '../../../public/assets/google-icon.svg'
import microsoftIcon from '../../../public/assets/microsoft-icon.svg'
import TextField from '../Components/TextField.vue';
import EmailIcon from '../../../public/assets/email-icon.svg'
</script>
<template>
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-light rounded-xl shadow-lg overflow-hidden flex max-w-6xl w-full">
            <!-- Left side - Form -->
            <div class="w-full lg:w-1/2 p-10">
                <div class="pt-20 pb-10 px-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Sign Up</h1>
                    <p class="text-gray-600 mb-6">Sign up with open account</p>
                    
                    <!-- OAuth Buttons -->
                    <div class=" mb-6 flex flex-col gap-2 border">
                        <Button social :pic="googleIcon" :style="`py-2 w-full text-lg`" text="Google" />
                        <Button social :pic="microsoftIcon" :style="`py-2 w-full text-lg`" text="Microsoft" />
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
                    <form @submit.prevent="handleSubmit" class="flex flex-col gap-12 p-4">
                    <TextField v-model="email" :icon="EmailIcon" label="Email" type="email" labeltxt="Email" placeholder="jon@email.com"/>
                    
                    <Button text="Sign Up" :style="`py-2 w-full text-lg`" type="submit"/>
                    </form>
                </div>

                <div class="w-full text-center">
                    <p>Already have an account? <Link :href="route('signin')" class="underline font-bold text-lg">Sign in</Link></p>
                </div>
            </div>
            <!-- Right side - Illustration -->
            <div class="hidden lg:block lg:w-1/2 bg-light p-12">
                <img :src="signupIllustration" alt="Illustration" class="w-full h-full object-contain" />
            </div>
        </div>
    </div>
</template>