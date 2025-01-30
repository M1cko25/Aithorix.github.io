<script setup>
import { Plus } from 'lucide-vue-next'

defineProps({
  title: {
    type: String,
    required: true
  },
  tasks: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="w-80 bg-gray-100 rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-medium">{{ title }}</h3>
      <span class="text-sm text-gray-500">{{ tasks.length }}</span>
    </div>

    <button class="w-full p-3 bg-white rounded-lg border border-gray-200 text-left text-gray-600 hover:bg-gray-50 flex items-center gap-2">
      <Plus class="w-4 h-4" />
      Create Task
    </button>

    <div class="mt-4 space-y-3">
      <div 
        v-for="task in tasks" 
        :key="task.id"
        class="p-4 bg-white rounded-lg shadow-sm"
      >
        {{ task.title }}
      </div>
    </div>
  </div>
</template>