<script setup>
import { ref } from 'vue'
import { Bell, Settings, HelpCircle, ChevronDown } from 'lucide-vue-next'
import Button from './Button.vue'
import Logo from '../../../public/assets/logo.png'
import { usePage } from '@inertiajs/vue3'
import { route } from '../../../vendor/tightenco/ziggy/src/js'

const props = usePage().props;

const userAvatar = '/placeholder.svg?height=40&width=40'
</script>

<template>
  <header class="h-16 bg-white border-b fixed top-0 left-64 right-0 flex items-center justify-between px-6">
    <div class="flex items-center">
      <img :src="Logo" alt="Aithorix" class="h-8" />
    </div>

    <div class="flex items-center gap-4">
      <button class="p-2 text-gray-600 hover:text-gray-800">
        <Bell class="w-5 h-5" />
      </button>
      <button class="p-2 text-gray-600 hover:text-gray-800">
        <Settings class="w-5 h-5" />
      </button>
      <button class="p-2 text-gray-600 hover:text-gray-800">
        <HelpCircle class="w-5 h-5" />
      </button>
      
      <div>
        <button class="flex w-fit items-center gap-2 ml-4">
            <img :src="props.auth.user.avatar" alt="User" class="w-8 h-8 rounded-full" />
            <ChevronDown class="w-4 h-4 text-gray-600" />
        </button>
        <div class="absolute bg-white bottom-0 translate-y-2" >
            <Link :href="route('logout')" method="post">Log out</Link>
        </div>
      </div>
    </div>
  </header>
</template>