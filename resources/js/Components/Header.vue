<script setup>
import { ref, watch } from 'vue'
import { Bell, Settings, HelpCircle, ChevronDown, Plus } from 'lucide-vue-next'
import Button from './Button.vue'
import Logo from '@/images/Logo.png'
import { usePage } from '@inertiajs/vue3'
import { route } from '../../../vendor/tightenco/ziggy/src/js'
import CreateProjectModal from './CreateProjectModal.vue'
import NotificationOverlay from './NotificationOverlay.vue'

const page = usePage().props;
const props = defineProps({
  logoDisplay: {
    type: Boolean,
    default: true
  },
})

let isShowLogin = ref(false)
const showLogin = () => {
  isShowLogin.value = !isShowLogin.value;
}

const showNotifications = ref(false)
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value;
  if (showNotifications.value) {
    isShowLogin.value = false;
  }
}

// Close dropdown when clicking outside
const closeDropdowns = (event) => {
  const notificationBtn = document.querySelector('.notification-btn');
  const profileBtn = document.querySelector('.profile-btn');

  if (notificationBtn && !notificationBtn.contains(event.target) &&
      !event.target.closest('.notification-overlay')) {
    showNotifications.value = false;
  }

  if (profileBtn && !profileBtn.contains(event.target) &&
      !event.target.closest('.profile-dropdown')) {
    isShowLogin.value = false;
  }
}

// Add click event listener to document
if (typeof window !== 'undefined') {
  document.addEventListener('click', closeDropdowns);
}
</script>

<template>
  <div v-if="isShowLogin" class="absolute w-screen h-screen" @click="showLogin"></div>
  <header class="h-16 bg-light border-b z-20 fixed top-0 right-0 left-0 flex items-center justify-between px-6 transition-all duration-300">
    <div class="flex items-center gap-6 transition-all duration-300 ease-in-out" :class="`${props.logoDisplay ? 'ml-64' : ''}`">
      <img :src="Logo" alt="Aithorix" class="h-8" />
    </div>

    <div class="flex items-center gap-4">
      <!-- Notification Button -->
      <button
        class="p-2 text-gray-600 hover:text-gray-800 notification-btn relative"
        @click.stop="toggleNotifications"
      >
        <Bell class="w-5 h-5" />
        <!-- Notification Badge -->
        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
      </button>

      <button class="p-2 text-gray-600 hover:text-gray-800">
        <Settings class="w-5 h-5" />
      </button>

      <button class="p-2 text-gray-600 hover:text-gray-800">
        <HelpCircle class="w-5 h-5" />
      </button>

      <div>
        <button @click="showLogin" class="flex w-fit items-center gap-2 ml-4 profile-btn">
            <img :src="page.auth.user.avatar" alt="User" class="w-8 h-8 rounded-full" />
            <ChevronDown class="w-4 h-4 text-gray-600" />
        </button>
        <div v-if="isShowLogin" class="absolute bg-light shadow-lg p-2 z-10 rounded-md bottom-0 translate-y-8 profile-dropdown" >
            <Link :href="route('logout')" method="post">Log out</Link>
        </div>
      </div>
    </div>
  </header>

  <!-- Notification Overlay -->
  <NotificationOverlay
    v-model="showNotifications"
    class="notification-overlay"
  />
</template>

<style scoped>
header {
  transition: left 0.3s ease-in-out;
}
</style>
