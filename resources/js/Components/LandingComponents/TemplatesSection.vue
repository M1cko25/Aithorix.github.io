<script setup>
import {
    Code2,
    GraduationCap,
    FlaskConical,
    FileText,
    Calendar,
    Sparkles,
    CheckCircle,
} from 'lucide-vue-next';
</script>
<template>
  <section id="templates" class="py-20 px-4 bg-gradient-to-b from-slate-50 to-slate-100">
        <div class="container mx-auto">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Specialized Project Templates</h2>
            <p class="text-lg  max-w-2xl mx-auto">
              Choose from 5 specialized templates designed for different workflows and team needs.
            </p>
          </div>

          <div v-motion-pop-visible class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow transform hover:-translate-y-1 transition-transform duration-300">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <Code2 class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Scrum</h3>
              <p class=" mb-4">
                Perfect for development teams using agile methodology with sprints, backlogs, and burndown charts.
              </p>
              <ul class="space-y-2 mb-4">
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Sprint planning tools</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Backlog management</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Burndown charts</span>
                </li>
              </ul>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow transform hover:-translate-y-1 transition-transform duration-300">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <GraduationCap class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Educational</h3>
              <p class=" mb-4">
                Designed for students, educators, and teachers to manage courses, assignments, and learning objectives.
              </p>
              <ul class="space-y-2 mb-4">
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Course planning</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Assignment tracking</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Student progress monitoring</span>
                </li>
              </ul>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow transform hover:-translate-y-1 transition-transform duration-300">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <FlaskConical class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Research</h3>
              <p class=" mb-4">
                Helps research teams track objectives, check for plagiarism, and ensure proper grammar in publications.
              </p>
              <ul class="space-y-2 mb-4">
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Plagiarism detection</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Grammar checking</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Research objective tracking</span>
                </li>
              </ul>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow transform hover:-translate-y-1 transition-transform duration-300">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <FileText class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Content Management</h3>
              <p class=" mb-4">
                For content creators to track monetization, commissions, and engagement metrics.
              </p>
              <ul class="space-y-2 mb-4">
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Engagement analytics</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Monetization tracking</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Content calendar</span>
                </li>
              </ul>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow transform hover:-translate-y-1 transition-transform duration-300">
              <div class="bg-purple-100 p-3 rounded-lg inline-block mb-4">
                <Calendar class="h-8 w-8 text-purple-600" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Event Planning</h3>
              <p class=" mb-4">
                Helps event planners track budgets, guest lists, and schedules for successful events.
              </p>
              <ul class="space-y-2 mb-4">
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Budget management</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Guest list tracking</span>
                </li>
                <li class="flex items-start">
                  <CheckCircle class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                  <span class="">Schedule coordination</span>
                </li>
              </ul>
            </div>

            <div class="bg-gradient-to-br from-purple-600 to-pink-600 p-6 rounded-xl shadow-md text-white">
              <div class="bg-white/20 p-3 rounded-lg inline-block mb-4">
                <Sparkles class="h-8 w-8 text-white" />
              </div>
              <h3 class="text-xl font-semibold mb-3">Custom Templates</h3>
              <p class="text-white/90 mb-4">
                Need something specific? Create your own custom template tailored to your unique workflow.
              </p>
              <button class="mt-4 bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50 transition-colors">
                Learn More
              </button>
            </div>
          </div>
        </div>
      </section>
</template>