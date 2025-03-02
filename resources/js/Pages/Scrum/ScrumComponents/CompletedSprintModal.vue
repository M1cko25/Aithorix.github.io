<script setup>
import Modal from '../../../Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'
import { updateEpicStatus } from '../ScrumServices/epicApi'

const page = usePage().props

const props = defineProps({
  isOpen: Boolean,
  epicSelected: Object,
  epics: Object
})

const emit = defineEmits(['update:isOpen'])

const handleComplete = () => {
  updateEpicStatus(props.epics, props.epicSelected, page.projectDetails)
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