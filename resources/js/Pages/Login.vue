<script setup>
import icon from '../Icons.js'
import graphics from '../graphics.js'
import Button from '../Components/Button.vue'
import TextField from '../Components/TextField.vue'
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const goBack =() => {(window.history.length > 1) ? window.history.back() : Inertia.visit('/');}

const form = useForm({
  email: null,
  password: null,
  remember: false
})

</script>

<template>
    <Head title="| Log In" />
    <div class="min-h-screen flex items-center justify-center overflow-hidden">
        <Link :href="route('landing')" class="flex flex-row items-center gap-4 absolute top-4 left-4">
            <img :src="icon.leftIcon">
            <p>Back</p>
        </Link>
        <div class="bg-light rounded-xl shadow-lg overflow-hidden flex max-w-4xl w-full">
            <!-- Left side - Illustration -->
            <div class="hidden lg:block lg:w-1/2 bg-gray-100 p-6">
                <img :src="graphics.signinIllustration" alt="Illustration" class="w-full h-full object-contain" />
            </div>
            
            <!-- Right side - Form -->
            <div class="w-full lg:w-1/2 px-3 py-6">
                <div class=" pb-10 px-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Log In</h1>
                    <p class="text-gray-600 mb-6">Log in with open account</p>
                    
                    <!-- OAuth Buttons -->
                    <div class="flex gap-4 mb-6">
                        <Button 
                            social 
                            :pic="icon.googleIcon" 
                            :style="`py-2 flex-1 text-lg`" 
                            text="Google" 
                            :href="route('googleLogin')"
                        />
                        <Button 
                            social 
                            :pic="icon.microsoftIcon" 
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
                    <form @submit.prevent="form.post('/login')" class="flex flex-col gap-2 p-2">
                        <TextField 
                            v-model="form.email" 
                            :icon="icon.emailIcon" 
                            label="Email" 
                            type="email" 
                            labeltxt="Email" 
                            placeholder="jon@email.com"
                        />
                        <p class="text-red-600 font-sm" v-if="form.errors.email">{{ form.errors.email }}</p>
                        
                        
                        <div class="flex flex-col gap-1">
                            <TextField 
                            v-model="form.password" 
                            :icon="icon.passwordIcon" 
                            label="Password" 
                            type="password" 
                            labeltxt="Password" 
                            placeholder="Password"
                            />
                            <p class="text-red-600 font-sm" v-if="form.errors.password">{{ form.errors.password }}</p>
                            <div class="flex flex-row items-center justify-between">
                                <div class="flex items-center gap-2 mt-2">
                                    <input
                                    type="checkbox"
                                    id="remember"
                                    v-model="form.remember"
                                    class="rounded border-gray-300 text-primary focus:ring-primary"
                                    >
                                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                                </div>
                                <Link :href="route('forgot-password')" class="text-sm text-gray-600 hover:underline">
                                    Forgot Password?
                                </Link>
                            </div>
                            
                        </div>
                        
                        
                        <Button v-if="form.processing"
                            text="Logging in" 
                            :style="`py-2 w-full text-lg mt-4`" 
                            disableBtn
                        />
                        <Button v-else 
                        text="Log In"
                        :style="`py-2 w-full text-lg mt-4`"
                        type="submit"
                        />

                    </form>
                </div>

                <div class="w-full text-center">
                    <p>Don't have an account? <Link :href="route('register')" class="underline font-bold text-lg">Sign Up</Link></p>
                </div>
            </div>
        </div>
    </div>
</template>