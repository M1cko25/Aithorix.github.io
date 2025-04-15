<script setup>
import {
    FileText,
    Lightbulb,
    TrendingUp,
} from "lucide-vue-next";
</script>
<template>
    <section class="py-20 px-4 bg-white">
        <div class="container mx-auto">
          <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0 relative">
              <div class="relative z-10 animate-float-slow">
                <img src="" alt="LIRA AI Assistant" class="rounded-lg shadow-xl" />
              </div>
              <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-to-r from-purple-300 to-pink-300 rounded-full filter blur-3xl opacity-20 -z-10"></div>
            </div>
            <div class="md:w-1/2 md:pl-16">
              <h2 class="text-3xl md:text-4xl font-bold mb-6">Meet LIRA, Your AI Assistant</h2>
              <p class="text-lg  mb-8">
                LIRA is more than just an AI – it's your project partner that helps you work smarter, not harder.
              </p>

              <div class="space-y-6">
                <div class="flex items-start">
                  <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                    <FileText class="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-1">Meeting Summaries</h3>
                    <p class="">LIRA automatically generates concise summaries of your meetings, capturing key points and action items.</p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                    <Lightbulb class="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-1">Project Growth Tips</h3>
                    <p class="">Get intelligent suggestions to improve your project based on your team's performance and industry best practices.</p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                    <TrendingUp class="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-1">Outcome Predictions</h3>
                    <p class="">LIRA analyzes your project data to predict outcomes and help you stay ahead of potential issues.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
</template>