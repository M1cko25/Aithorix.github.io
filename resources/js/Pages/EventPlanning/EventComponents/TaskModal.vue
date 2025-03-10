<script setup>
import { ref, computed } from 'vue';
import { X, AlertCircle, User, Calendar, Clock } from 'lucide-vue-next';

const props = defineProps({
  isOpen: Boolean,
  task: Object,
  event: Object,
  vendors: Array
});

const emit = defineEmits(['update:isOpen', 'update:task']);

const form = ref({
  title: '',
  description: '',
  dueDate: '',
  status: 'To Do',
  assignedVendor: null,
  budget: '',
  attachments: []
});

const hasAttemptedSubmit = ref(false);
const errors = ref({});

// Initialize form when task changes
watch(() => props.task, (newTask) => {
  if (newTask) {
    form.value = {
      title: newTask.title || '',
      description: newTask.description || '',
      dueDate: newTask.dueDate || '',
      status: newTask.status || 'To Do',
      assignedVendor: newTask.assignedVendor || null,
      budget: newTask.budget || '',
      attachments: newTask.attachments || []
    };
  }
}, { immediate: true });

const saveTask = async () => {
  hasAttemptedSubmit.value = true;
  
  if (!form.value.title?.trim() || !form.value.dueDate || !form.value.assignedVendor) {
    return;
  }

  try {
    const response = await axios.post('/events/tasks', {
      ...form.value,
      eventId: props.event.id
    });

    if (response.data.success) {
      emit('update:task', response.data.task);
      emit('update:isOpen', false);
    }
  } catch (error) {
    console.error('Error saving task:', error);
    errors.value = error.response?.data?.errors || {};
  }
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-dark bg-opacity-50" @click="$emit('update:isOpen', false)"></div>
    
    <!-- Modal Container -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-xl">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
          <div class="flex-1">
            <input 
              v-model="form.title"
              type="text"
              class="text-xl font-semibold w-full bg-transparent border-0 focus:ring-0"
              :class="{ 'border-error': hasAttemptedSubmit && !form.title?.trim() }"
              placeholder="Task title"
            />
            <div v-if="hasAttemptedSubmit && !form.title?.trim()" class="text-sm text-error flex items-center gap-1">
              <AlertCircle class="w-4 h-4" />
              Task title is required
            </div>
          </div>
          <button @click="$emit('update:isOpen', false)" class="p-1 hover:bg-neutral rounded">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- Status and Vendor -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-dark mb-1">Status</label>
              <select 
                v-model="form.status"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-button"
              >
                <option v-for="status in ['To Do', 'In Progress', 'Done']" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-dark mb-1">Assigned Vendor</label>
              <select 
                v-model="form.assignedVendor"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-button"
                :class="{ 'border-error': hasAttemptedSubmit && !form.assignedVendor }"
              >
                <option value="">Select Vendor</option>
                <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                  {{ vendor.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Due Date and Budget -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-dark mb-1">Due Date</label>
              <input
                type="date"
                v-model="form.dueDate"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-button"
                :class="{ 'border-error': hasAttemptedSubmit && !form.dueDate }"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-dark mb-1">Budget</label>
              <input
                type="number"
                v-model="form.budget"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-button"
                placeholder="Enter budget amount"
              />
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-dark mb-1">Description</label>
            <textarea 
              v-model="form.description"
              rows="4"
              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-button"
              placeholder="Add task description..."
            ></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t bg-neutral flex justify-between items-center">
          <div class="text-sm text-dark-gray">
            Press Esc to close
          </div>
          <div class="flex gap-3">
            <button @click="$emit('update:isOpen', false)" class="btn-cancel">
              Cancel
            </button>
            <button 
              @click="saveTask"
              class="btn-primary"
            >
              Save Task
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>