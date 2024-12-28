<script setup>
import Button from '../Components/Button.vue'
import signinIllustration from '../../../public/assets/signin-illustration.svg'
import googleIcon from '../../../public/assets/google-icon.svg'
import microsoftIcon from '../../../public/assets/microsoft-icon.svg'
import TextField from '../Components/TextField.vue'
import EmailIcon from '../../../public/assets/email-icon.svg'
import PasswordIcon from '../../../public/assets/password-icon.svg'
import { ref } from 'vue'

let options = [ 'Option 1', 'Option 2', 'Option 3']
const email = ref('')
const password = ref('')

const handleSubmit = () => {
  // Handle form submission
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="bg-light rounded-xl shadow-lg overflow-hidden flex max-w-6xl w-full">
            <!-- Left side - Illustration -->
            <div class="hidden lg:block lg:w-1/2 bg-gray-100 p-12">
                <img :src="signinIllustration" alt="Illustration" class="w-full h-full object-contain" />
            </div>
            
            <!-- Right side - Form -->
            <div class="w-full lg:w-1/2 p-10">
                <div class="pt-20 pb-10 px-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Sign In</h1>
                    <p class="text-gray-600 mb-6">Sign in with open account</p>
                    
                    <!-- OAuth Buttons -->
                    <div class="flex gap-4 mb-6">
                        <Button 
                            social 
                            :pic="googleIcon" 
                            :style="`py-2 flex-1 text-lg`" 
                            text="Google" 
                        />
                        <Button 
                            social 
                            :pic="microsoftIcon" 
                            :style="`py-2 flex-1 text-lg`" 
                            text="Microsoft" 
                        />
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
                    <form @submit.prevent="handleSubmit" class="flex flex-col gap-6 p-4">
                        <TextField 
                            v-model="email" 
                            :icon="EmailIcon" 
                            label="Email" 
                            type="email" 
                            labeltxt="Email" 
                            placeholder="john@email.com"
                        />
                        
                        <div class="flex flex-col gap-1">
                            <TextField 
                                v-model="password" 
                                :icon="PasswordIcon" 
                                label="Password" 
                                type="password" 
                                labeltxt="Password" 
                                placeholder="Password"
                            />
                            <div class="flex justify-end">
                                <a href="#" class="text-sm text-gray-600 hover:underline">
                                    Forgot Password?
                                </a>
                            </div>
                        </div>
                        
                        <Button 
                            @click="handleSubmit"
                            text="Sign In" 
                            :style="`py-2 w-full text-lg mt-4`" 
                            type="submit"
                        />
                    </form>
                </div>

                <div class="w-full text-center">
                    <p>Don't have an account? <Link :href="route('signup')" class="underline font-bold text-lg">Sign Up</Link></p>
                </div>
            </div>
        </div>
    </div>
</template>