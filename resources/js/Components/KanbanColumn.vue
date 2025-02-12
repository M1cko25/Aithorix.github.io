<script setup>
import { Plus } from 'lucide-vue-next'
import { ref } from 'vue'
import CreateTaskModal from '../Components/CreateTaskModal.vue'
import TaskCard from './TaskCard.vue'

const isModalOpen = ref(false)
const columnId = ref('')

const openModal = () => {
  columnId.value = props.id // Set the column ID
  isModalOpen.value = true
}

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  tasks: {
    type: Array,
    default: () => []
  }
})

const closeModal = () => {
  isModalOpen.value = false
}

const handleCreateTask = (newTask) => {
  tasks.value.push({
    ...newTask,
    id: Date.now().toString(),
    createdAt: new Date().toISOString()
  })
  closeModal()
}

</script>

<template>
  <div class="w-80 bg-gray-100 rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-medium">{{ title }}</h3>
      <span class="text-sm text-gray-500">{{ tasks.length }}</span>
    </div>

    <button @click="openModal"
      class="w-full p-3 bg-white rounded-lg border border-gray-200 text-left text-gray-600 hover:bg-gray-50 flex items-center gap-2">
      <Plus class="w-4 h-4" />
      Create Task
    </button>

    <CreateTaskModal :is-open="isModalOpen" :column-id="columnId" @close="closeModal" @create="handleCreateTask" />

    <div class="mt-4 space-y-3">
      <div v-for="task in tasks" :key="task.id" :task="task" class="p-4 bg-white rounded-lg shadow-sm">
        {{ task.title }}
      </div>
    </div>
  </div>
</template>