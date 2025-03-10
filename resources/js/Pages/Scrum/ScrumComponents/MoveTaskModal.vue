<script setup>
import { ref, watch, computed } from 'vue'
import Modal from '../../../Components/Modal.vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const page = usePage().props

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  tasks: {
    type: Array,
    required: true
  },
  currentEpicId: {
    type: Number,
    required: true
  },
  epics: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['update:isOpen', 'tasksMoved'])

const availableEpics = computed(() => {
  return props.epics.filter(epic => epic.epic_id !== props.currentEpicId)
})

const moveTaskToEpic = async (targetEpic) => {
  try {
    const response = await axios.post('/scrum/move-tasks', {
      taskIds: props.tasks.map(task => task.id),
      targetEpicId: targetEpic.id,
      sourceEpicId: props.currentEpicId,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      emit('tasksMoved', {
        tasks: props.tasks,
        targetEpic: targetEpic
      })
      emit('update:isOpen', false)
    }
  } catch (error) {
    console.error('Error moving tasks:', error)
  }
}

watch(() => props.isOpen, (newValue) => {
  if (!newValue) {
    emit('update:isOpen', false)
  }
})
</script> 

<template>
  <Modal 
    :modelValue="isOpen"
    @update:modelValue="$emit('update:isOpen', $event)"
    title="Move Task"
  >
    <div class="p-4">
      <div class="mb-4">
        <h3 class="text-lg font-medium mb-2">Select Epic</h3>
        <p class="text-sm text-gray-600 mb-4">
          {{ tasks.length > 1 ? `Moving ${tasks.length} tasks to:` : 'Moving task to:' }}
        </p>
        <div class="space-y-2 max-h-60 overflow-y-auto">
          <button
            v-for="epic in availableEpics"
            :key="epic.id"
            @click="moveTaskToEpic(epic)"
            class="w-full px-4 py-2 text-left rounded-lg hover:bg-gray-100 flex items-center justify-between"
            :class="{'bg-gray-50': epic.id === currentEpicId}"
          >
            <span>{{ epic.name }}</span>
            <span v-if="epic.id === currentEpicId" class="text-sm text-gray-500">(Current)</span>
          </button>
        </div>
      </div>
    </div>
  </Modal>
</template>

