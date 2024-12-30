<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  illustration: {
    type: String,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div 
    class="w-[280px] sm:w-[320px] md:w-[380px] bg-white rounded-xl shadow-lg p-4 sm:p-6 md:p-8 transition-all duration-500"
    :class="[
      isSelected ? 'shadow-xl' : 'shadow-md hover:shadow-lg'
    ]"
  >
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">{{ title }}</h2>
    
    <div class="h-40 sm:h-44 md:h-52 flex items-center justify-center mb-4 sm:mb-6">
      <img 
        :src="illustration" 
        :alt="title" 
        class="w-full h-full object-contain transition-transform duration-500"
        :class="{ 'scale-110': isSelected }"
      />
    </div>
    
    <p class="text-gray-600 text-sm sm:text-base leading-relaxed">{{ description }}</p>
  </div>
</template>