<script setup>
import Modal from '../../../Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'
import { updateEpicStatus } from '../ScrumServices/epicApi'
import axios from 'axios'

const page = usePage().props

const props = defineProps({
  isOpen: Boolean,
  epicSelected: Object,
  epics: Object
})

const emit = defineEmits(['update:isOpen'])

const handleComplete = async () => {
  try {
    // First update the epic status to Completed
    await axios.post('/scrum/epic-status-update', {
      epicId: props.epicSelected.epic_id,
      status: 'Completed',
      projectId: page.projectDetails.id
    })

    // Update sprint status to Completed
    if (page.sprint?.epic_id === props.epicSelected.epic_id) {
      await axios.post('/scrum/complete-sprint', {
        epicId: props.epicSelected.epic_id,
        projectId: page.projectDetails.id
      })
    }

    // Update the local epic status
    if (props.epicSelected) {
      props.epicSelected.status = 'Completed'
    }

    // Find and update the epic in the epics array
    if (props.epics.value) {
      const epicIndex = props.epics.value.findIndex(e => e.epic_id === props.epicSelected.epic_id)
      if (epicIndex !== -1) {
        props.epics.value[epicIndex].status = 'Completed'
      }
    }

    emit('update:isOpen', false)
    
    // Refresh the page to get updated sprint data
    window.location.reload()
  } catch (error) {
    console.error('Error completing sprint:', error)
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
        <button class="btn-primary w-full" @click="handleComplete">Complete Sprint</button>
      </div>
    </div>
  </Modal>
</template> 