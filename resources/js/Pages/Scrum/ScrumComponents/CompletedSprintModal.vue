<script setup>
import { ref } from 'vue'
import Modal from '../../../Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage().props
const props = defineProps({
  isOpen: Boolean,
  epicSelected: Object,
  epics: Array
})

const emit = defineEmits(['update:isOpen'])
const isCompleting = ref(false)

const completeSprint = async () => {
  if (isCompleting.value) return
  
  isCompleting.value = true
  try {
    const response = await axios.post('/scrum/complete-sprint', {
      epicId: props.epicSelected.id,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      // Update epic status and progress in the local state
      const epicIndex = props.epics.findIndex(e => e.id === props.epicSelected.id)
      if (epicIndex !== -1) {
        props.epics[epicIndex].status = 'Completed'
        props.epics[epicIndex].progress_percent = response.data.epic.progress_percent
        Object.assign(props.epicSelected, {
          status: 'Completed',
          progress_percent: response.data.epic.progress_percent
        })
      }
      
      emit('update:isOpen', false)
    }
  } catch (error) {
    console.error('Error completing sprint:', error)
  } finally {
    isCompleting.value = false
  }
}

const updateModalState = (value) => {
  emit('update:isOpen', value)
}
</script>

<template>
  <Modal 
    :modelValue="isOpen"
    @update:modelValue="updateModalState"
    title="Sprint Completed!"
  >
    <div class="text-center flex flex-col gap-6 p-4">
      <div class="text-6xl">🎉</div>
      <h3 class="text-xl font-semibold">Congratulations!</h3>
      <p class="text-gray-600">You've successfully completed the sprint for</p>
      <p class="text-lg font-medium">{{ epicSelected.name }}</p>
      <div class="flex flex-row justify-center w-full gap-4 mt-4">
        <button 
          class="btn-primary w-full" 
          @click="completeSprint"
          :disabled="isCompleting"
        >
          {{ isCompleting ? 'Completing Sprint...' : 'Complete Sprint' }}
        </button>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.text-6xl {
  font-size: 4rem;
  line-height: 1;
}
</style> 