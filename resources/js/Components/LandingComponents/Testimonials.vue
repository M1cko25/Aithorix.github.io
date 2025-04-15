<script setup>
import { Star } from 'lucide-vue-next';
</script>
<template>
    <section id="testimonials" class="py-20 px-4 bg-gradient-to-b from-slate-50 to-slate-100">
        <div class="container mx-auto">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">What Our Users Say</h2>
            <p class="text-lg  max-w-2xl mx-auto">
              Discover how Aithorix has transformed project management for teams around the world.
            </p>
          </div>

          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-motion-slide-visible-bottom class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-6">
                <div class="mr-4">
                  <img src="" alt="User Avatar" class="h-14 w-14 rounded-full object-cover" />
                </div>
                <div>
                  <h4 class="font-semibold">Sarah Johnson</h4>
                  <p class="">Product Manager, TechCorp</p>
                </div>
              </div>
              <div class="mb-4">
                <div class="flex text-yellow-400">
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                </div>
              </div>
              <p class=" italic">
                "LIRA has completely transformed how our team collaborates. The AI-generated meeting summaries save us hours each week, and the project predictions have helped us avoid several potential roadblocks."
              </p>
            </div>

            <div v-motion-slide-visible-bottom :delay="200" class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-6">
                <div class="mr-4">
                  <img src="" alt="User Avatar" class="h-14 w-14 rounded-full object-cover" />
                </div>
                <div>
                  <h4 class="font-semibold">Michael Chen</h4>
                  <p class="">Professor, State University</p>
                </div>
              </div>
              <div class="mb-4">
                <div class="flex text-yellow-400">
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                </div>
              </div>
              <p class=" italic">
                "The Educational template has been a game-changer for our research department. We can easily track student progress, manage assignments, and ensure academic integrity with the plagiarism detection."
              </p>
            </div>

            <div v-motion-slide-visible-bottom :delay="400" class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-shadow">
              <div class="flex items-center mb-6">
                <div class="mr-4">
                  <img src="" alt="User Avatar" class="h-14 w-14 rounded-full object-cover" />
                </div>
                <div>
                  <h4 class="font-semibold">Alex Rivera</h4>
                  <p class="">Content Creator</p>
                </div>
              </div>
              <div class="mb-4">
                <div class="flex text-yellow-400">
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5 fill-current" />
                  <Star class="h-5 w-5" />
                </div>
              </div>
              <p class=" italic">
                "As a content creator, tracking engagement and monetization across platforms was a nightmare until I found Aithorix. Now I can see all my metrics in one place and make data-driven decisions about my content strategy."
              </p>
            </div>
          </div>
        </div>
      </section>
</template>