<script setup>
import { ref, computed } from 'vue'
import Button from '../Components/Button.vue'
import TemplateCard from '../Components/TemplateCard.vue'
import graphics from '../graphics.js'
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next'
import logo from '../../../public/assets/logo.png'
import { Inertia } from '@inertiajs/inertia'

const templateCards = ref(null);
const templateContainer = ref(null);

const templates = [
  {
    id: 1,
    title: 'Education Purpose',
    description: 'For students or teachers to plan lessons, track assignments, and manage deadlines.',
    illustration: graphics.educationIllustration
  },
  {
    id: 2,
    title: 'Event Planning',
    description: 'For planning events like conferences, weddings, or product launches.',
    illustration: graphics.eventIllustration
  },
  {
    id: 3,
    title: 'Scrum',
    description: 'Built for teams using the Scrum framework, emphasizing structured workflows and clear roles.',
    illustration: graphics.scrumIllustration
  },
  {
    id: 4,
    title: 'Research Development',
    description: 'For teams conducting research, testing prototypes, or innovating products.',
    illustration: graphics.researchIllustration
  },
  {
    id: 5,
    title: 'Content Calendar',
    description: 'For marketers, bloggers, or social media managers to plan and track content publication schedules.',
    illustration: graphics.contentIllustration
  }
]

let selectedIndex = ref(2)
const isAnimating = ref(false)

const getVisibleCards = computed(() => {
  const cards = []
  const count = templates.length
  
  for (let i = -2; i <= 2; i++) {
    const index = ((selectedIndex.value + i) % count + count) % count
    cards.push({
      ...templates[index],
      offset: i
    })
  }
  
  return cards
})

const getCardStyle = (offset) => {
  const baseTransform = 'translate(-50%, -50%)'
  // Responsive spacing based on screen width
  const xOffset = window.innerWidth < 768 ? offset * 300 : offset * 400
  const scale = offset === 0 ? 1 : Math.max(0.7 - Math.abs(offset) * 0.1, 0.5)
  const opacity = offset === 0 ? 1 : Math.max(0.6 - Math.abs(offset) * 0.2, 0.2)
  const zIndex = 10 - Math.abs(offset)

  return {
    transform: `${baseTransform} translateX(${xOffset}px) scale(${scale})`,
    opacity,
    zIndex
  }
}

const navigate = (direction) => {
  if (isAnimating.value) return
  
  isAnimating.value = true
  selectedIndex.value = ((selectedIndex.value + direction) % templates.length + templates.length) % templates.length
  
  setTimeout(() => {
    isAnimating.value = false
  }, 500)
}

const handleSelectTemplate = (index) => {
  if (isAnimating.value) return
  selectedIndex.value = index
}

const createProject = computed(() => {
   if (selectedIndex.value === 2) {return route('scrum-board')}
})


</script>

<template>
  <div class="h-fit bg-gray-50 p-4 sm:p-8">
    <div class="max-w-7xl mx-auto">
      <!-- Logo -->
      <div class="flex justify-center mb-4 sm:mb-12">
        <img :src="logo" alt="Aithorix" class="h-6 sm:h-8" />
      </div>
      
      <!-- Heading -->
      <div class="text-center mb-4 sm:mb-16">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2 px-4">
          Select the template that fits<br class="hidden sm:block"/>to your project?
        </h1>
      </div>
      
      <!-- Templates Carousel -->
      <div class="relative mx-auto mb-8 sm:mb-16 ">
        <!-- Navigation Buttons -->
        <button 
          @click="navigate(-1)"
          class="absolute left-0 top-1/2 -translate-y-1/2 z-30 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white shadow-lg hover:bg-gray-50 transition-colors flex items-center justify-center -translate-x-2 sm:-translate-x-6 lg:opacity-0 opacity-100"
          :disabled="isAnimating"
        >
          <ChevronLeftIcon class="w-5 h-5 sm:w-6 sm:h-6" />
        </button>
        
        <button 
          @click="navigate(1)"
          class="absolute right-0 top-1/2 -translate-y-1/2 z-30 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white shadow-lg hover:bg-gray-50 transition-colors flex items-center justify-center translate-x-2 sm:translate-x-6 lg:opacity-0 opacity-100"
          :disabled="isAnimating"
        >
          <ChevronRightIcon class="w-5 h-5 sm:w-6 sm:h-6" />
        </button>
        
        <!-- Cards Container -->
        <div class="relative w-full h-[400px]">
          <TemplateCard
            v-for="card in getVisibleCards"
            :key="card.id"
            v-bind="card"
            :style="getCardStyle(card.offset)"
            :isSelected="card.offset === 0"
            @click="handleSelectTemplate(templates.findIndex(t => t.id === card.id))"
            class="absolute top-1/2 left-1/2 transition-all duration-500 ease-out cursor-pointer"
          />
        </div>
      </div>
      
      <!-- Next Button -->
      <div class="flex justify-end px-4 sm:px-0">
        <Link :href="createProject" class="btn-primary px-4 py-2">Next</Link>
      </div>
    </div>
  </div>
</template> 