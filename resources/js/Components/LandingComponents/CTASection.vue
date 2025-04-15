<script setup>
import { Calendar, Rocket } from "lucide-vue-next";
</script>
<template>
    <section class="py-20 px-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white">
        <div class="container mx-auto text-center">
          <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Transform Your Project Management?</h2>
          <p class="text-xl max-w-2xl mx-auto mb-10">
            Join thousands of teams already using Aithorix to streamline their workflows and boost productivity.
          </p>
          <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
            <button class="bg-white text-purple-600 hover:bg-purple-50 px-8 py-3 rounded-full transition-colors shadow-lg flex items-center justify-center mx-auto sm:mx-0">
              <Rocket class="h-5 w-5 mr-2" />
              Start Free Trial
            </button>
            <button class="border border-white text-white hover:bg-white/10 px-8 py-3 rounded-full transition-colors flex items-center justify-center mx-auto sm:mx-0">
              <Calendar class="h-5 w-5 mr-2" />
              Schedule Demo
            </button>
          </div>
        </div>
      </section>
</template>