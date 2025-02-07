<script setup>
import { ref } from 'vue'
import Button from '../Components/Button.vue'
import TextField from '../Components/TextField.vue'
import EmailIcon from '../../../public/assets/email-icon.svg'
import forgotPasswordIllustration from '../../../public/assets/forgotpass.svg'
import { useForm } from '@inertiajs/vue3'

const email = ref('')

let form = useForm({
    email: '',
})
</script>

<template>
  <Head title="| Forgot Password"/>
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-light rounded-xl shadow-lg overflow-hidden flex max-w-4xl w-full">
      <!-- Left side - Illustration -->
      <div class="hidden lg:block lg:w-1/2 bg-gray-100 p-12">
        <img :src="forgotPasswordIllustration" alt="Forgot Password Illustration" class="w-full h-full object-contain" />
      </div>
      
      <!-- Right side - Form -->
      <div class="w-full lg:w-1/2 p-10">
        <div class="pt-20 pb-10 px-6">
          <h1 class="text-3xl font-bold text-gray-800 mb-2">Forgot Password</h1>
          <p class="text-gray-600 mb-8">Enter your email to reset your password</p>
          
          <!-- Form -->
          <form @submit.prevent="form.post('/forgot-password')" class="flex flex-col gap-6">
            <div>
                <TextField 
                v-model="form.email" 
                :icon="EmailIcon" 
                label="Email" 
                type="email" 
                name="email"
                labeltxt="Email" 
                placeholder="john@email.com"
                />
                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>

            </div>
            
            <Button 
              text="Reset Password" 
              :style="`py-2 w-full text-lg mt-4`" 
              type="submit"
              :disableBtn="form.processing"
            />
          </form>
        </div>

        <div class="w-full text-center mt-6">
          <p>Remember your password? <Link :href="route('login')" class="underline font-bold text-lg">Log In</Link></p>
        </div>
      </div>
    </div>
    <div v-if="form.wasSuccessful" v-motion="pop" class="absolute popup top-0 right-0 flex 
    flex-col gap-6 m-4 bg-success md:p-6 text-white rounded-lg shadow">
      <h1>Email sent successfully</h1>
    </div>
  </div>
</template>
<style scoped>
.popup {
  animation: timer 5s forwards;
}

@keyframes timer {
  0%, 80% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}
</style>