<template>
  <div class="fixed right-0 top-0 bottom-0 w-80 bg-white shadow-lg transition-transform duration-300 transform h-full flex flex-col">
    <div class="p-4 border-b">
      <h2 class="text-xl font-semibold">Meeting Tasks</h2>
    </div>
    
    <div class="flex-1 overflow-y-auto p-4">
      <p v-if="tasks.length === 0" class="text-gray-500 text-center py-4">
        No tasks yet. Add one below!
      </p>
      <ul v-else class="space-y-2">
        <li
          v-for="task in tasks"
          :key="task.id"
          class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
        >
          <div class="flex items-center space-x-3">
            <input
              type="checkbox"
              :id="`task-${task.id}`"
              v-model="task.completed"
              class="rounded border-gray-300 text-blue focus:ring-blue"
            />
            <label
              :for="`task-${task.id}`"
              class="cursor-pointer"
              :class="{ 'line-through text-gray-500': task.completed }"
            >
              {{ task.text }}
            </label>
          </div>
          <button
            @click="$emit('delete-task', task.id)"
            class="text-gray-500 hover:text-red-500"
          >
            <Trash class="h-4 w-4" />
          </button>
        </li>
      </ul>
    </div>
    
    <div class="p-4 border-t">
      <div class="flex space-x-2">
        <input
          v-model="newTaskText"
          type="text"
          placeholder="Add a new task..."
          class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue"
          @keydown.enter="addTask"
        />
        <button
          @click="addTask"
          class="p-2 bg-blue text-white rounded-md hover:bg-blue focus:outline-none focus:ring-2 focus:ring-blue"
        >
          <Plus class="h-4 w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Trash, Plus } from 'lucide-vue-next'

const props = defineProps({
  tasks: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['add-task', 'delete-task'])

const newTaskText = ref('')

const addTask = () => {
  if (newTaskText.value.trim() === '') return
  emit('add-task', newTaskText.value.trim())
  newTaskText.value = ''
}
</script> 