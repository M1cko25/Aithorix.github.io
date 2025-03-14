<script setup>
import { ref, watch, computed } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import { updateEpicStatus } from '../ScrumServices/epicApi'
import axios from 'axios'

const page = usePage().props
const props = defineProps({
  isOpen: Boolean,
  epicSelected: Object,
  epics: Object,
})

const emit = defineEmits(['update:isOpen', 'sprintCreated'])

const form = ref({
  name: '',
  description: '',
  start_date: '',
  end_date: '',
  epic_id: props.epicSelected?.id,
  projectId: props.epicSelected?.project_id
})

const sprintSpans = [
  { label: '1 Week', value: 1 },
  { label: '2 Weeks', value: 2 },
  { label: '3 Weeks', value: 3 },
  { label: '4 Weeks', value: 4 },
  { label: 'Custom', value: 'custom' }
]

const selectedSpan = ref(sprintSpans[0])

// Function to calculate end date based on start date and weeks
const calculateEndDate = (startDate, weeks) => {
  const date = new Date(startDate)
  date.setDate(date.getDate() + (weeks * 7))
  return date.toISOString().split('T')[0]
}

// Set initial dates and name when modal opens
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    const today = new Date().toISOString().split('T')[0]
    form.value.start_date = today
    form.value.end_date = calculateEndDate(today, 1)
    form.value.name = `${props.epicSelected.name} Sprint` // Set default name based on epic
    selectedSpan.value = sprintSpans[0]
  }
})

// Watch for sprint span changes
watch(() => selectedSpan.value, (newSpan) => {
  if (newSpan.value !== 'custom') {
    form.value.end_date = calculateEndDate(form.value.start_date, newSpan.value)
  }
})

// Watch for date changes to update span
watch([() => form.value.start_date, () => form.value.end_date], ([newStart, newEnd]) => {
  if (newStart && newEnd) {
    const start = new Date(newStart)
    const end = new Date(newEnd)
    const diffTime = Math.abs(end - start)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    const diffWeeks = Math.round(diffDays / 7)
    
    const matchingSpan = sprintSpans.find(span => span.value === diffWeeks)
    selectedSpan.value = matchingSpan || sprintSpans[sprintSpans.length - 1] // Set to custom if no match
  }
}, { immediate: true })

// Watch for epic changes to update sprint name
watch(() => props.epicSelected, (newEpic) => {
  if (newEpic) {
    form.value.name = `${newEpic.name} Sprint`
  }
}, { immediate: true })

const submit = () => {
  axios.post('/scrum/start-sprint', {
    ...form.value,
    epic_id: props.epicSelected.id,
    projectId: page.projectDetails.id
  })
  .then((response) => {
    updateEpicStatus(props.epics, props.epicSelected, page.projectDetails)
    // Emit the sprint data to update parent component
    emit('sprintCreated', {
      epic_id: props.epicSelected.id,
      start_date: form.value.start_date,
      end_date: form.value.end_date,
      name: form.value.name,
      status: 'Active'
    })
    emit('update:isOpen', false)
  })
  .catch(error => {
    console.error('Error starting sprint:', error)
  })
}

const updateModalState = (value) => {
  emit('update:isOpen', value)
}

// Get minimum date for end date input
const getMinEndDate = computed(() => {
  return form.value.start_date || new Date().toISOString().split('T')[0]
})
</script>

<template>
  <Modal 
    :modelValue="isOpen"
    @update:modelValue="updateModalState"
    title="Start Sprint"
  >
    <div class="space-y-4 p-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sprint Name</label>
        <input 
          v-model="form.name"
          type="text"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          placeholder="Enter sprint name"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sprint Span</label>
        <select 
          v-model="selectedSpan"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option v-for="span in sprintSpans" :key="span.value" :value="span">
            {{ span.label }}
          </option>
        </select>
      </div>

      <div class="flex gap-4">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input 
            v-model="form.start_date"
            type="date"
            :min="new Date().toISOString().split('T')[0]"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input 
            v-model="form.end_date"
            type="date"
            :min="getMinEndDate"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea 
          v-model="form.description"
          rows="3"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          placeholder="Enter sprint description"
        ></textarea>
      </div>

      <div class="flex justify-end gap-4 mt-6">
        <button @click="updateModalState(false)" class="btn-cancel">Cancel</button>
        <button @click="submit" class="btn-primary">Start Sprint</button>
      </div>
    </div>
  </Modal>
</template>