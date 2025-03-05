<template>
  <div class="p-4">
    <div v-if="tasks.length === 0" class="text-gray-400 text-center py-4">
      No tasks yet. Add one below!
    </div>
    
    <div class="space-y-2">
      <div v-for="task in tasks" 
           :key="task.id" 
           class="flex items-center p-3 bg-gray-700 rounded-lg">
        <input type="checkbox"
               v-model="task.completed"
               class="mr-3" />
        <span class="flex-1 text-white" :class="{ 'line-through': task.completed }">
          {{ task.text }}
        </span>
        <button @click="$emit('delete', task.id)" class="text-gray-400 hover:text-red-500">
          <Trash class="h-4 w-4" />
        </button>
      </div>
    </div>
    
    <div class="mt-4 flex gap-2">
      <input v-model="newTask"
             @keyup.enter="addTask"
             placeholder="Add task..."
             class="flex-1 px-3 py-2 bg-gray-700 rounded-md text-white" />
      <button @click="addTask" class="p-2 bg-blue-500 rounded-md text-white">
        <Plus class="h-4 w-4" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Trash, Plus } from 'lucide-vue-next'

const props = defineProps(['tasks'])
const emit = defineEmits(['add', 'delete'])
const newTask = ref('')

const addTask = () => {
  if (newTask.value.trim()) {
    emit('add', newTask.value)
    newTask.value = ''
  }
}
</script>