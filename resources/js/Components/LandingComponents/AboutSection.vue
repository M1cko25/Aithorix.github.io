<script setup>
import { Users, FileText, Award } from 'lucide-vue-next';
</script>
<template>
    <section id="about" class="py-20 px-4 bg-white">
        <div class="container mx-auto">
          <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
              <h2 class="text-3xl md:text-4xl font-bold mb-6">About Aithorix</h2>
              <p class="text-lg mb-6">
                Aithorix was born from a simple idea: project management should be intelligent, intuitive, and adaptable to any workflow.
              </p>
              <p class="text-lg mb-6">
                Our team of developers, designers, and AI specialists has created a platform that combines powerful project management tools with cutting-edge artificial intelligence to help teams work smarter, not harder.
              </p>
              <p class="text-lg mb-6">
                Whether you're a development team using Scrum, a university department managing research, or an event planner coordinating your next big event, Aithorix has the tools and templates you need to succeed.
              </p>
              <div class="flex space-x-4 mt-8">
                <a href="#" class="text-purple-600 hover:text-purple-700 transition-colors flex items-center">
                  <Users class="h-5 w-5 mr-2" />
                  Our Team
                </a>
                <a href="#" class="text-purple-600 hover:text-purple-700 transition-colors flex items-center">
                  <FileText class="h-5 w-5 mr-2" />
                  Case Studies
                </a>
                <a href="#" class="text-purple-600 hover:text-purple-700 transition-colors flex items-center">
                  <Award class="h-5 w-5 mr-2" />
                  Achievements
                </a>
              </div>
            </div>
            <div class="md:w-1/2 md:pl-16 grid grid-cols-2 gap-4">
              <img src="" alt="Team working" class="rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300" />
              <img src="" alt="Office space" class="rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300 mt-8" />
              <img src="" alt="Product demo" class="rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300" />
              <img src="" alt="Team meeting" class="rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300 mt-8" />
            </div>
          </div>
        </div>
      </section>
</template>