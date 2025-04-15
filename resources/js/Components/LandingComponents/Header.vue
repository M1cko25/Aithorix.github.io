<script setup>
import { BrainCircuit, Menu} from "lucide-vue-next";
import { ref } from "vue";
import Logo from '@/images/Logo2.png';
const mobileMenuOpen = ref(false);
</script>
<template>
    <nav class="w-screen fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <img :src="Logo" alt="" class="h-8 w-30" />
            <!-- <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600">Aithorix</span> -->
          </div>
          <div class="hidden md:flex items-center space-x-8">
            <a href="#features" class=" hover:text-purple-600 transition-colors">Features</a>
            <a href="#templates" class=" hover:text-purple-600 transition-colors">Templates</a>
            <a href="#testimonials" class=" hover:text-purple-600 transition-colors">Testimonials</a>
            <a href="#about" class=" hover:text-purple-600 transition-colors">About</a>
            <a href="#contact" class=" hover:text-purple-600 transition-colors">Contact</a>
          </div>
          <div class="flex items-center space-x-4">
            <Link class="btn-primary" href="/login">
              Sign In
            </Link>
            <button class="md:hidden" @click="mobileMenuOpen = !mobileMenuOpen">
                <Menu class="h-6 w-6 " />
            </button>
          </div>
        </div>

        <div v-if="mobileMenuOpen" class="md:hidden bg-white shadow-lg absolute w-full py-4 px-4 transition-all duration-300 ease-in-out">
          <div class="flex flex-col space-y-4">
            <a href="#features" class=" hover:text-purple-600 transition-colors" @click="mobileMenuOpen = false">Features</a>
            <a href="#templates" class=" hover:text-purple-600 transition-colors" @click="mobileMenuOpen = false">Templates</a>
            <a href="#testimonials" class=" hover:text-purple-600 transition-colors" @click="mobileMenuOpen = false">Testimonials</a>
            <a href="#about" class=" hover:text-purple-600 transition-colors" @click="mobileMenuOpen = false">About</a>
            <a href="#contact" class=" hover:text-purple-600 transition-colors" @click="mobileMenuOpen = false">Contact</a>
          </div>
        </div>
      </nav>
</template>