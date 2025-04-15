<script setup>
import { Rocket, PlayCircle } from "lucide-vue-next";
import Mockup from "@/images/Mockups.png";
import Phone from '@/images/Phone.png';
import Laptop from '@/images/Laptop.png';
</script>
<template>
    <section class="pt-32 pb-20 px-4">
        <div class="container mx-auto">
          <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
              <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                Manage Projects Smarter with
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600">AI-Powered</span>
                Assistance
              </h1>
              <p class="text-lg mb-8">
                Aithorix combines powerful project management tools with LIRA, your AI assistant, to streamline workflows and boost productivity.
              </p>
              <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                <button class="btn-primary p-2">
                  <Rocket class="h-5 w-5 mr-2" />
                  Start Free Trial
                </button>
                <button class="btn-border flex items-center justify-center">
                  <PlayCircle class="h-5 w-5 mr-2" />
                  Watch Demo
                </button>
              </div>
            </div>
            <div class="md:w-1/2 relative">
              <div v-motion-slide-visible-right class="relative w-full hidden md:block h-full z-10">
                <img :src="Laptop" alt="Aithorix Dashboard" class="relative top-0 z-8 left-40 h-auto" />
                <img :src="Phone" alt="Aithorix Dashboard" class="absolute top-0 -left-36 z-10 h-auto" />
              </div>
              <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-to-r from-purple-300 to-pink-300 rounded-full filter blur-3xl opacity-20 -z-10"></div>
            </div>
          </div>
        </div>
      </section>
</template>