<script setup>
import { ref } from 'vue'
import Button from '../Components/Button.vue'
import TextField from '../Components/TextField.vue'
import PasswordIcon from '../../../public/assets/password-icon.svg'
import resetPasswordIllustration from '../../../public/assets/resetpass.svg'
import { useForm, usePage } from '@inertiajs/vue3'

const { props } = usePage();

const form = useForm({
  token: props.token,
  email: props.email,
  password: null,
  password_confirmation: null,
})

</script>

<template>
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-light rounded-xl shadow-lg overflow-hidden flex max-w-4xl w-full">
      <!-- Left side - Illustration -->
      <div class="hidden lg:block lg:w-1/2 bg-gray-100 p-12">
        <img :src="resetPasswordIllustration" alt="Reset Password Illustration" class="w-full h-full object-contain" />
      </div>
      
      <!-- Right side - Form -->
      <div class="w-full lg:w-1/2 p-10">
        <div class="pt-20 pb-10 px-6">
          <h1 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h1>
          <p class="text-gray-600 mb-8">Enter your new password</p>
          
          <!-- Form -->
          <form @submit.prevent="form.post('/reset-password')" class="flex flex-col gap-6">
            <div>
              <TextField 
                v-model="form.password" 
                :icon="PasswordIcon" 
                label="NewPassword" 
                type="password" 
                labeltxt="New Password" 
                placeholder="••••••••"
              />
              <p v-if="form.errors.password" class="text-red-600 text-sm">{{ form.errors.password }}</p>
            </div>
            
            <div>
              <TextField 
                v-model="form.password_confirmation" 
                :icon="PasswordIcon" 
                label="ConfirmPassword" 
                type="password" 
                labeltxt="Confirm Password" 
                placeholder="••••••••"
              />
              <p v-if="form.errors.password_confirmation" 
              class="text-red-600 text-sm">{{ form.errors.password_confirmation }}</p>
            </div>
            
            <Button 
              text="Reset Password" 
              :style="`py-2 w-full text-lg mt-4`" 
              type="submit"
            />
          </form>
        </div>

        <div class="w-full text-center mt-6">
          <p>Remember your password? <Link :href="route('login')" class="underline font-bold text-lg">Log In</Link></p>
        </div>
      </div>
    </div>

    <div v-if="form.wasSuccessful" v-motion="pop" class="absolute flex flex-col items-center bg-light p-8 gap-5 rounded-lg shadow">
      <div class="w-full max-w-2xl p-12 mx-4 text-center transition-all transform bg-light">
        <!-- Success Icon -->
        <div class="flex items-center justify-center w-24 h-24 mx-auto bg-green-100 rounded-full">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
      </div>
      <h1>Your Password is successfully updated</h1>
      <Link :href="route('login')" class="btn-primary px-5 py-2">Login Now</Link>
    </div>
  </div>
</template>