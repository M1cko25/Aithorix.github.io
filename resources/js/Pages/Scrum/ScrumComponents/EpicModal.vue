<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Modal from '../../../Components/Modal.vue'
import { X, Trash2, AlertCircle } from 'lucide-vue-next'
import axios from 'axios'

const page = usePage().props

const props = defineProps({
  isOpen: Boolean,
  epic: Object
})

const emit = defineEmits(['update:isOpen', 'epicDeleted'])

const form = ref({
  name: '',
  description: '',
  start_date: '',
  end_date: ''
})

const errors = ref({
  name: '',
  description: '',
  start_date: '',
  end_date: ''
})

const isDeleting = ref(false)
const showDeleteConfirm = ref(false)
const isSaving = ref(false)

const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toISOString().split('T')[0]
}

const validateForm = () => {
  errors.value = {
    name: '',
    description: '',
    start_date: '',
    end_date: ''
  }
  
  if (!form.value.name.trim()) {
    errors.value.name = 'Epic name is required'
    return false
  }
  
  return true
}

watch(() => props.epic, (newEpic) => {
  if (newEpic) {
    form.value = {
      name: newEpic.name,
      description: newEpic.description || '',
      start_date: formatDate(newEpic.start_date) || formatDate(new Date()),
      end_date: formatDate(newEpic.end_date) || formatDate(new Date(Date.now() + 7 * 24 * 60 * 60 * 1000))
    }
  }
}, { immediate: true })

const updateEpic = async () => {
  if (!validateForm()) return
  
  isSaving.value = true
  try {
    const response = await axios.post('/scrum/epic-update', {
      epicId: props.epic.id,
      name: form.value.name.trim(),
      description: form.value.description.trim(),
      start_date: form.value.start_date,
      end_date: form.value.end_date,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      console.log(response.data)
      const updatedEpic = response.data.epic
      Object.assign(props.epic, {
        name: updatedEpic.name,
        description: updatedEpic.description,
        start_date: updatedEpic.start_date,
        end_date: updatedEpic.end_date
      })
      emit('update:isOpen', false)
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      console.error('Error updating epic:', error)
    }
  } finally {
    isSaving.value = false
  }
}

const deleteEpic = async () => {
  if (!props.epic || isDeleting.value) return
  
  isDeleting.value = true
  try {
    const response = await axios.post('/scrum/epic-delete', {
      epicId: props.epic.id,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      emit('epicDeleted', props.epic.id)
      emit('update:isOpen', false)
    }
  } catch (error) {
    console.error('Error deleting epic:', error)
  } finally {
    isDeleting.value = false
    showDeleteConfirm.value = false
  }
}

const updateModalState = (value) => {
  showDeleteConfirm.value = false
  errors.value = { name: '', description: '', start_date: '', end_date: '' }
  emit('update:isOpen', value)
}
</script>

<template>
  <Modal 
    :modelValue="isOpen"
    @update:modelValue="updateModalState"
    :title="showDeleteConfirm ? 'Delete Epic' : 'Edit Epic'"
  >
    <div v-if="!showDeleteConfirm" class="space-y-6 p-6">
      <!-- Epic Key Display -->
      <div class="bg-gray-50 px-4 py-2 rounded-lg">
        <span class="text-sm text-gray-500">Epic Key:</span>
        <span class="ml-2 font-medium">{{ props.epic?.key }}</span>
      </div>

      <!-- Name Input -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Epic Name</label>
        <input 
          v-model="form.name"
          type="text"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          :class="{ 'border-red-500': errors.name }"
          placeholder="Enter epic name"
        />
        <div v-if="errors.name" class="mt-1 text-sm text-red-500 flex items-center gap-1">
          <AlertCircle class="w-4 h-4" />
          {{ errors.name }}
        </div>
      </div>

      <!-- Description Input -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
        <textarea 
          v-model="form.description"
          rows="4"
          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          :class="{ 'border-red-500': errors.description }"
          placeholder="Enter epic description"
        ></textarea>
        <div v-if="errors.description" class="mt-1 text-sm text-red-500 flex items-center gap-1">
          <AlertCircle class="w-4 h-4" />
          {{ errors.description }}
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
            :max="form.end_date"
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
            :min="form.start_date"
          />
          <div v-if="errors.end_date" class="mt-1 text-sm text-red-500 flex items-center gap-1">
            <AlertCircle class="w-4 h-4" />
            {{ errors.end_date }}
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-between items-center pt-4 border-t">
        <button 
          @click="showDeleteConfirm = true" 
          class="flex items-center gap-2 px-4 py-2 text-red-600 hover:text-red-700 rounded-lg hover:bg-red-50"
        >
          <Trash2 class="w-4 h-4" />
          Delete Epic
        </button>
        <div class="flex gap-3">
          <button @click="updateModalState(false)" class="btn-cancel">
            Cancel
          </button>
          <button 
            @click="updateEpic" 
            class="btn-primary"
            :disabled="isSaving"
          >
            {{ isSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <div v-else class="p-6 space-y-6">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
          <Trash2 class="h-6 w-6 text-red-600" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Epic</h3>
        <p class="text-sm text-gray-500">
          Are you sure you want to delete this epic? This action cannot be undone.
          All tasks in this epic will also be deleted.
        </p>
      </div>
      <div class="flex justify-center gap-3 pt-4">
        <button 
          @click="showDeleteConfirm = false" 
          class="btn-cancel"
        >
          Cancel
        </button>
        <button 
          @click="deleteEpic" 
          class="btn-error"
          :disabled="isDeleting"
        >
          {{ isDeleting ? 'Deleting...' : 'Delete Epic' }}
        </button>
      </div>
    </div>
  </Modal>
</template> 