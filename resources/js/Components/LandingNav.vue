<script setup>
import { ref } from 'vue';
import logo from '../../../public/assets/Logo2.png'
import Button from '../Components/Button.vue'

const mobileMenuOpen = ref(false);

const signUp = () => {
}
</script>
<template>
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-button">Aithorix</a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-dark hover:text-button transition">Features</a>
                    <a href="#about" class="text-dark hover:text-button transition">About</a>
                    <a href="#contact" class="text-dark hover:text-button transition">Contact</a>
                    <a href="/login" class="btn-primary">Get Started</a>
                </div>

                <!-- Mobile Navigation Button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-dark">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden mt-4 space-y-4">
                <a href="#features" class="block text-dark hover:text-button transition">Features</a>
                <a href="#about" class="block text-dark hover:text-button transition">About</a>
                <a href="#contact" class="block text-dark hover:text-button transition">Contact</a>
                <a href="/login" class="btn-primary inline-block mt-2">Get Started</a>
            </div>
        </div>
    </nav>
</template>
<style scoped>
  .menu-link {
    position: relative;
  }
.menu-link::before {
    content: '';
    width: 0;
    height: 2px;
    border-radius: 2px;
    background-color: #000;
    position: absolute;
    bottom: -.25rem;
    left: 50%;
    transition: width .4s, left .4s;
  }

.menu-link:hover::before {
    width: 100%;
    left: 0;
}
</style>