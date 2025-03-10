
<script setup>
defineProps({
  isOpen: Boolean,
  buttons: Array,
  position: {
    type: Object,
    default: () => ({ x: 0, y: 0 })
  }
})
</script>
<template>
    <div v-if="isOpen" class="fixed inset-0 overflow-y-auto" @click="$emit('close')">
      <div 
        class="absolute bg-white shadow-lg overlay rounded-lg p-2 min-w-[150px]" 
        :style="{
          left: position.x + 'px',
          top: position.y + 'px'
        }"
      >
        <button 
          v-for="button in buttons" 
          :key="button.text"
          @click="button.function($event.target)"
          class="w-full p-2 text-left hover:bg-gray-100 rounded"
        >
          {{ button.text }}
        </button>
      </div>
    </div>
</template>