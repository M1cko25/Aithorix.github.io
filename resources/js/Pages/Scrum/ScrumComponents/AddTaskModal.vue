<script setup>
import { ref } from 'vue'
import Modal from '../../../Components/Modal.vue'
import { AlertCircle } from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage().props
const props = defineProps({
  isOpen: Boolean,
  sprint: Object
})

const emit = defineEmits(['update:isOpen', 'taskAdded'])

const form = ref({
  title: '',
  start_date: '',
  end_date: ''
})

const errors = ref({
  title: '',
  start_date: '',
  end_date: '',
  general: ''
})

const isProcessing = ref(false)

const validateForm = () => {
  errors.value = {
    title: '',
    start_date: '',
    end_date: '',
    general: ''
  }

  if (!form.value.title.trim()) {
    errors.value.title = 'Task title is required'
    return false
  }

  if (!form.value.start_date) {
    errors.value.start_date = 'Start date is required'
    return false
  }

  if (!form.value.end_date) {
    errors.value.end_date = 'End date is required'
    return false
  }

  if (new Date(form.value.end_date) < new Date(form.value.start_date)) {
    errors.value.end_date = 'End date must be after start date'
    return false
  }

  return true
}

const resetForm = () => {
  form.value = {
    title: '',
    start_date: '',
    end_date: ''
  }
  errors.value = {
    title: '',
    start_date: '',
    end_date: '',
    general: ''
  }
}

const handleSubmit = async () => {
  if (!validateForm() || isProcessing.value) return

  console.log('Form data:', form.value)
  console.log('Sprint data:', props.sprint)

  isProcessing.value = true
  try {
    const requestData = {
      title: form.value.title,
      start_date: form.value.start_date,
      end_date: form.value.end_date,
      sprintId: props.sprint.id,
      epicId: props.sprint.epic_id,
      projectId: props.sprint.project_id
    }

    console.log('Request data:', requestData)

    const response = await axios.post('/scrum/add-sprint-task', requestData)

    console.log('Response:', response.data)

    if (response.data.success) {
      emit('taskAdded', response.data.task)
      emit('update:isOpen', false)
      resetForm()
    } else {
      console.error('Server returned success: false', response.data)
      errors.value.general = response.data.message || 'Failed to add task'
    }
  } catch (error) {
    console.error('Full error object:', error)
    if (error.response) {
      console.error('Error response:', error.response.data)
      if (error.response.data.errors) {
        errors.value = error.response.data.errors
      } else {
        errors.value.general = error.response.data.message || 'An error occurred'
      }
    } else if (error.request) {
      console.error('Error request:', error.request)
      errors.value.general = 'No response received from server'
    } else {
      console.error('Error message:', error.message)
      errors.value.general = 'An error occurred while sending the request'
    }
  } finally {
    isProcessing.value = false
  }
}
</script>

<template>
  <Modal 
    :model-value="isOpen"
    @update:model-value="$emit('update:isOpen', $event)"
    title="Add New Task"
  >
    <div class="p-6 space-y-6">
      <!-- Title Input -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Task Title</label>
        <input 
          v-model="form.title"
          type="text"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          :class="{ 'border-red-500': errors.title }"
          placeholder="Enter task title"
        />
        <div v-if="errors.title" class="mt-1 text-sm text-red-500 flex items-center gap-1">
          <AlertCircle class="w-4 h-4" />
          {{ errors.title }}
        </div>
      </div>

      <!-- Date Inputs -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
          <input 
            v-model="form.start_date"
            type="date"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.start_date }"
            :min="sprint?.start_date"
            :max="form.end_date || sprint?.end_date"
          />
          <div v-if="errors.start_date" class="mt-1 text-sm text-red-500 flex items-center gap-1">
            <AlertCircle class="w-4 h-4" />
            {{ errors.start_date }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
          <input 
            v-model="form.end_date"
            type="date"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.end_date }"
            :min="form.start_date || sprint?.start_date"
            :max="sprint?.end_date"
          />
          <div v-if="errors.end_date" class="mt-1 text-sm text-red-500 flex items-center gap-1">
            <AlertCircle class="w-4 h-4" />
            {{ errors.end_date }}
          </div>
        </div>
      </div>

      <!-- General Error Message -->
      <div v-if="errors.general" class="rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <AlertCircle class="h-5 w-5 text-red-400" />
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">{{ errors.general }}</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-3 pt-4">
        <button 
          @click="$emit('update:isOpen', false)"
          class="btn-cancel"
          :disabled="isProcessing"
        >
          Cancel
        </button>
        <button 
          @click="handleSubmit"
          class="btn-primary"
          :disabled="isProcessing"
        >
          {{ isProcessing ? 'Adding...' : 'Add Task' }}
        </button>
      </div>
    </div>
  </Modal>
</template> 