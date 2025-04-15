<script setup>
import {
    BrainCircuit,
    Video,
    LayoutTemplate,
    LineChart,
    Users,
    Shield,
} from 'lucide-vue-next';
</script>
<template>
    <section id="features" class="py-20 px-4 bg-white">
        <div class="container mx-auto">
          <div v-motion-pop-visible class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Powerful Features</h2>
            <p class="text-lg  max-w-2xl mx-auto">
              Aithorix combines cutting-edge AI technology with robust project management tools to help teams achieve more.
            </p>
          </div>

          <div v-motion-pop-visible class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-slate-50 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <BrainCircuit class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">AI Assistant LIRA</h3>
              <p class="">
                Get intelligent suggestions, meeting summaries, and project outcome predictions from your AI assistant.
              </p>
            </div>

            <div class="bg-slate-50 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <Video class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Video Conferencing</h3>
              <p class="">
                Host public or private video calls with your team, complete with meeting agendas and AI-generated summaries.
              </p>
            </div>

            <div class="bg-slate-50 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <LayoutTemplate class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Specialized Templates</h3>
              <p class="">
                Choose from 5 project templates designed for different workflows and team needs.
              </p>
            </div>

            <div class="bg-slate-50 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <LineChart class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Analytics & Insights</h3>
              <p class="">
                Track project progress, team performance, and content metrics with detailed analytics.
              </p>
            </div>

            <div class="bg-slate-50 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <Users class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Team Collaboration</h3>
              <p class="">
                Seamlessly work together with real-time updates, comments, and file sharing.
              </p>
            </div>

            <div class="bg-slate-50 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <Shield class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Enterprise Security</h3>
              <p class="">
                Keep your data safe with advanced security features and role-based permissions.
              </p>
            </div>
          </div>
        </div>
      </section>
</template>