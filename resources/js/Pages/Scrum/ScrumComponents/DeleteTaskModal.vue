<script setup>
import Modal from '@/Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage().props

const props = defineProps({
  isOpen: Boolean,
  task: {
    type: Object,
    default: null
  },
  tasks: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:isOpen', 'confirm'])

const handleDelete = () => {
  emit('confirm')
}

const updateModalState = (value) => {
  emit('update:isOpen', value)
}
</script>

<template>
  <Modal 
    :modelValue="isOpen"
    @update:modelValue="updateModalState"
    :title="tasks && tasks.length > 1 ? 'Delete Tasks' : 'Delete Task'"
  >
    <div class="text-center flex flex-col gap-4">
      <template v-if="tasks && tasks.length > 1">
        <p>Are you sure you want to delete these {{ tasks.length }} tasks?</p>
        <div class="text-sm text-gray-600 max-h-40 overflow-y-auto">
          <p v-for="task in tasks" :key="task.id" class="mb-1">
            {{ task.title }} ({{ task.key }})
          </p>
        </div>
      </template>
      <template v-else>
        <p>Are you sure you want to delete this task?</p>
        <p v-if="task" class="text-sm text-gray-600">
          Task: {{ task.title }} ({{ task.key }})
        </p>
      </template>
      <div class="flex flex-row justify-center w-full gap-4">
        <button class="btn-cancel w-full" @click="updateModalState(false)">Cancel</button>
        <button class="btn-danger w-full" @click="handleDelete">Delete</button>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.btn-danger {
  @apply bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors;
}
</style> 