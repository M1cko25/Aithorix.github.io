<script setup>
import Modal from '../../../Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'
import { deleteTask } from '../ScrumServices/taskApi'

const page = usePage().props

const props = defineProps({
  isOpen: Boolean,
  epicSelected: Object,
  selectedTaskToUpdate: Object,
  taskCounts: Object
})

const emit = defineEmits(['update:isOpen'])

const handleDelete = () => {
  deleteTask(props.epicSelected, props.taskCounts, props.selectedTaskToUpdate, page.projectDetails)
  emit('update:isOpen', false)
}

const updateModalState = (value) => {
  emit('update:isOpen', value)
}
</script>

<template>
  <Modal 
    :modelValue="isOpen"
    @update:modelValue="updateModalState"
    title="Delete Task"
  >
    <div class="text-center flex flex-col gap-4">
      <p>Are you sure you want to delete this task?</p>
      <div class="flex flex-row justify-center w-full gap-4">
        <button class="btn-cancel w-full" @click="updateModalState(false)">No</button>
        <button class="btn-primary w-full" @click="handleDelete">Yes</button>
      </div>
    </div>
  </Modal>
</template> 