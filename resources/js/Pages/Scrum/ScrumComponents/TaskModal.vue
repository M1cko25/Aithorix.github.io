<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { ClipboardList, Bug, Bookmark, Flag, Clock, User, X, AlertCircle } from 'lucide-vue-next'
import axios from 'axios'
import { formatDistanceToNow } from 'date-fns'

const page = usePage().props

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  task: {
    type: Object,
    required: true,
    default: () => ({})
  },
  epicSelected: {
    type: Object,
    required: false,
    default: null
  },
  context: {
    type: String,
    default: 'backlog' // can be 'backlog' or 'gantt'
  }
})

const emit = defineEmits(['update:isOpen', 'update:task'])

const taskTypes = [
  { name: 'Task', icon: ClipboardList },
  { name: 'Bug', icon: Bug },
  { name: 'Story', icon: Bookmark }
]

const priorities = [
  { name: 'Low', color: 'text-gray-500' },
  { name: 'Medium', color: 'text-yellow-500' },
  { name: 'High', color: 'text-red-500' }
]

const statuses = ['To Do', 'In Progress', 'Done']

const form = ref({
  title: '',
  description: '',
  type: 'Task',
  priority: 'Low',
  status: 'To Do',
  assignees: [],
  attachments: []
})

const selectedFile = ref(null)
const attachments = ref(props.task?.attachments || [])
const isUploading = ref(false)
const uploadProgress = ref(0)
const fileInputRef = ref(null)
const newComment = ref('')
const comments = ref(page.comments || [])
const projectMembers = ref([])
const showAssigneeDropdown = ref(false)
const errors = ref({
  title: '',
  description: '',
  type: '',
  priority: '',
  status: ''
})
const isSaving = ref(false)

watch(() => props.task, (newTask) => {
  if (newTask) {
    form.value = { 
      title: newTask.title || '',
      description: newTask.description || '',
      type: newTask.type || 'Task',
      priority: newTask.priority || 'Low',
      status: newTask.status || 'To Do',
      assignees: newTask.assignees || [],
      attachments: newTask.attachments || []
    }
    attachments.value = [...(newTask.attachments || [])];
    if (page.comments && page.comments[newTask.id]) {
      comments.value = page.comments[newTask.id]
    } else {
      comments.value = []
    }
  }
}, { immediate: true, deep: true })

const updateTask = () => {
  axios.post('/scrum/backlog-update', {
    id: props.task.id,
    epicId: props.epicSelected.epic_id,
    projectId: page.projectDetails.id,
    ...form.value
  })
  .then(() => {
    // Update the task in the parent component
    Object.assign(props.task, form.value)
    emit('update:isOpen', false)
  })
  .catch(error => {
    console.error('Error updating task:', error)
  })
}

const getTypeIcon = computed(() => {
  return taskTypes.find(type => type.name === form.value.type)?.icon
})

const updateModalState = (value) => {
  emit('update:isOpen', value)
}

const handleFileSelect = (event) => {
  const files = event.target.files
  if (files.length > 0) {
    selectedFile.value = files[0]
    uploadFile()
  }
}

const uploadFile = async () => {
  if (!selectedFile.value) return

  isUploading.value = true
  uploadProgress.value = 0

  const formData = new FormData()
  formData.append('file', selectedFile.value)
  formData.append('id', props.task.id)
  formData.append('projectId', page.projectDetails.id)

  try {
    const response = await axios.post('/scrum/upload-attachment', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        uploadProgress.value = Math.round(
          (progressEvent.loaded * 100) / progressEvent.total
        )
      }
    })

    if (response.data.success) {
      const newAttachment = {
        id: response.data.id,
        file_name: response.data.name,
        file_size: response.data.size,
        file_type: response.data.type,
        file_path: response.data.file_path
      }
      
      if (!form.value.attachments) form.value.attachments = []
      form.value.attachments.push(newAttachment)
      attachments.value.push(newAttachment)
      
      // Clear the file input
      if (fileInputRef.value) {
        fileInputRef.value.value = ''
      }
      selectedFile.value = null
    }
  } catch (error) {
    console.error('Error uploading file:', error)
  } finally {
    isUploading.value = false
    uploadProgress.value = 0
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const saveChanges = async () => {
  errors.value = {
    title: '',
    description: '',
    type: '',
    priority: '',
    status: ''
  }

  if (!form.value.title || !form.value.title.trim()) {
    errors.value.title = 'Task title is required'
    return
  }

  isSaving.value = true
  try {
    const response = await axios.post('/scrum/backlog-update', {
      id: props.task.id,
      title: form.value.title.trim(),
      description: form.value.description || '',
      type: form.value.type,
      priority: form.value.priority,
      status: form.value.status,
      epicId: props.epicSelected?.id || props.task.epic_id,
      projectId: page.projectDetails.id,
      assignees: form.value.assignees ? form.value.assignees.map(a => a.id) : []
    })

    if (response.data.success) {
      const updatedTask = {
        ...props.task,
        title: form.value.title.trim(),
        description: form.value.description || '',
        type: form.value.type,
        priority: form.value.priority,
        status: form.value.status,
        epic_id: props.epicSelected?.id || props.task.epic_id,
        assignees: form.value.assignees || []
      }
      
      // Emit both the modal close and task update
      emit('update:task', updatedTask)
      emit('update:isOpen', false)
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      console.error('Error saving task:', error)
    }
  } finally {
    isSaving.value = false
  }
}

const handleEscapeKey = (event) => {
  if (event.key === 'Escape' && props.isOpen) {
    updateModalState(false)
  }
}

watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    document.addEventListener('keydown', handleEscapeKey)
  } else {
    document.removeEventListener('keydown', handleEscapeKey)
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})

const addComment = async () => {
  if (!newComment.value.trim()) return

  try {
    const response = await axios.post('/scrum/add-comment', {
      taskId: props.task.id,
      comment: newComment.value.trim(),
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      comments.value.push(response.data.comment)
      if (!page.comments[props.task.id]) {
        page.comments[props.task.id] = []
      }
      page.comments[props.task.id].push(response.data.comment)
      newComment.value = ''
    }
  } catch (error) {
    console.error('Error adding comment:', error)
  }
}

const formatDate = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true })
}

const toggleAssignee = async (member) => {
  const currentAssignees = form.value.assignees || []
  let newAssignees = []
  
  // Check if member is already assigned using the correct ID comparison
  const isAssigned = currentAssignees.some(a => a.id === member.id)
  
  if (isAssigned) {
    newAssignees = currentAssignees.filter(a => a.id !== member.id)
  } else {
    newAssignees = [...currentAssignees, {
      id: member.id,
      name: member.name,
      avatar: member.avatar
    }]
  }
  
  try {
    const response = await axios.post('/scrum/update-assignees', {
      taskId: props.task.id,
      assignees: newAssignees.map(a => a.id),
      projectId: page.projectDetails.id
    })
    
    if (response.data.success) {
      form.value.assignees = response.data.assignees
      // Also update the original task's assignees
      if (props.task) {
        props.task.assignees = response.data.assignees
      }
    }
  } catch (error) {
    console.error('Error updating assignees:', error)
  }
}

const deleteAttachment = async (attachmentId) => {
  try {
    const response = await axios.post('/scrum/delete-attachment', {
      attachmentId: attachmentId,
      taskId: props.task.id,
      projectId: page.projectDetails.id
    });

    if (response.data.success) {
      // Remove the attachment from both arrays
      form.value.attachments = form.value.attachments.filter(a => a.id !== attachmentId);
      attachments.value = attachments.value.filter(a => a.id !== attachmentId);
      
      // Update the task's attachments in the parent component
      const updatedTask = {
        ...props.task,
        attachments: form.value.attachments
      };
      
      // Emit the updated task
      emit('update:task', updatedTask);
      
      // Also update the original task object
      Object.assign(props.task, updatedTask);
    }
  } catch (error) {
    console.error('Error deleting attachment:', error);
  }
};
</script>

<template>
  <Transition name="modal">
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black bg-opacity-50" @click="updateModalState(false)"></div>
      
      <!-- Modal Container -->
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-[90vw] max-h-[90vh] flex flex-col">
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b">
            <div class="flex-1">
              <input 
                v-model="form.title"
                type="text"
                class="text-xl font-semibold w-full bg-transparent border-0 focus:ring-0 focus:outline-none"
                :class="{ 'border-red-500': errors.title }"
                placeholder="Task title"
              />
              <div v-if="errors.title" class="mt-1 text-sm text-red-500 flex items-center gap-1">
                <AlertCircle class="w-4 h-4" />
                {{ errors.title }}
              </div>
            </div>
            <button @click="updateModalState(false)" class="p-1 hover:bg-gray-100 rounded">
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Content -->
          <div class="flex flex-1 min-h-0">
            <!-- Left Column -->
            <div class="w-2/3 border-r p-6 overflow-y-auto">
              <div class="space-y-6">
                <!-- Type, Priority, Status Row -->
                <div class="grid grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select 
                      v-model="form.type"
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                      <option v-for="type in taskTypes" :key="type.name" :value="type.name">
                        {{ type.name }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select 
                      v-model="form.priority"
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                      <option v-for="priority in priorities" :key="priority.name" :value="priority.name">
                        {{ priority.name }}
                      </option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select 
                      v-model="form.status"
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                      :class="{
                        'text-gray-700 bg-gray-50': form.status === 'To Do',
                        'text-orange-600 bg-orange-50': form.status === 'In Progress',
                        'text-green-600 bg-green-50': form.status === 'Done'
                      }"
                    >
                      <option v-for="status in statuses" :key="status" :value="status">
                        {{ status }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea 
                    v-model="form.description"
                    rows="8"
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="Add a description..."
                  ></textarea>
                </div>

                <!-- Task Info -->
                <div class="flex items-center gap-3 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                  <component :is="getTypeIcon" class="w-4 h-4" />
                  <span>{{ form.type }}</span>
                  <span class="mx-1">•</span>
                  <Flag class="w-4 h-4" />
                  <span :class="priorities.find(p => p.name === form.priority)?.color">
                    {{ form.priority }} Priority
                  </span>
                  <span class="mx-1">•</span>
                  <Clock class="w-4 h-4" />
                  <span>Created {{ new Date(task?.created_at).toLocaleDateString() }}</span>
                </div>

                <!-- Assignees -->
                <div>
                  <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-700">Assignees</h3>
                    <button 
                      @click="showAssigneeDropdown = !showAssigneeDropdown"
                      class="text-sm text-blue-600 hover:underline flex items-center gap-1"
                    >
                      <User class="w-4 h-4" />
                      Add assignee
                    </button>
                  </div>
                  
                  <!-- Assignee list -->
                  <div class="flex -space-x-2 mb-2">
                    <template v-if="form.assignees && form.assignees.length > 0">
                      <div 
                        v-for="assignee in form.assignees" 
                        :key="assignee.id"
                        class="relative group"
                      >
                        <img 
                          :src="assignee.avatar"
                          :alt="assignee.name"
                          class="w-8 h-8 rounded-full border-2 border-white"
                        />
                        <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                          {{ assignee.name }}
                        </span>
                      </div>
                    </template>
                    <div v-else class="text-sm text-gray-500">
                      No assignees
                    </div>
                  </div>

                  <!-- Assignee dropdown -->
                  <div v-if="showAssigneeDropdown" class="relative">
                    <div class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
                      <div class="p-2">
                        <div 
                          v-for="member in page.projectMembers"
                          :key="member.id"
                          @click="toggleAssignee(member)"
                          class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer"
                        >
                          <img 
                            :src="member.avatar"
                            :alt="member.name"
                            class="w-6 h-6 rounded-full"
                          />
                          <span class="text-sm">{{ member.name }}</span>
                          <span v-if="form.assignees?.some(a => a.id === member.id)" class="ml-auto text-blue-600">
                            ✓
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="w-1/3 p-6 overflow-y-auto bg-gray-50">
              <!-- Attachments -->
              <div class="mb-8">
                <h3 class="text-lg font-medium mb-4">Attachments</h3>
                <div class="space-y-4">
                  <!-- File Upload -->
                  <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <input
                      ref="fileInputRef"
                      type="file"
                      @change="handleFileSelect"
                      class="hidden"
                      id="file-upload"
                    />
                    <label
                      for="file-upload"
                      class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center"
                    >
                      Upload File
                    </label>

                    <div v-if="selectedFile" class="mt-2 text-sm text-gray-500">
                      {{ selectedFile.name }}
                    </div>

                    <!-- Upload Progress -->
                    <div v-if="isUploading" class="mt-2">
                      <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div
                          class="bg-blue h-1.5 rounded-full"
                          :style="{ width: uploadProgress + '%' }"
                        ></div>
                      </div>
                      <span class="text-xs text-gray-500 mt-1">{{ uploadProgress }}%</span>
                    </div>
                  </div>

                  <!-- Attachments List -->
                  <div v-if="attachments.length > 0" class="bg-white rounded-lg border border-gray-200">
                    <ul class="divide-y divide-gray-200">
                      <li
                        v-for="file in attachments"
                        :key="file.id"
                        class="flex items-center justify-between p-3"
                      >
                        <div class="flex items-center gap-2">
                          <span class="text-sm font-medium">{{ file.file_name }}</span>
                          <span class="text-xs text-gray-500">({{ formatFileSize(file.file_size) }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                          <a
                            v-if="file.file_path"
                            :href="'/storage/' + file.file_path"
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 text-sm"
                          >
                            Open
                          </a>
                          <button
                            @click="deleteAttachment(file.id)"
                            class="text-gray-400 hover:text-gray-600"
                          >
                            <X class="w-4 h-4" />
                          </button>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div v-else class="text-sm text-gray-500 text-center">
                    No attachments yet
                  </div>
                </div>
              </div>

              <!-- Comments -->
              <div>
                <h3 class="text-lg font-medium mb-4">Comments</h3>
                <div class="space-y-4">
                  <!-- Add comment input -->
                  <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <textarea 
                      v-model="newComment"
                      rows="3"
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 mb-2"
                      placeholder="Write a comment..."
                    ></textarea>
                    <button 
                      @click="addComment"
                      :disabled="!newComment.trim()"
                      class="btn-primary w-full"
                      :class="{ 'opacity-50 cursor-not-allowed': !newComment.trim() }"
                    >
                      Add Comment
                    </button>
                  </div>

                  <!-- Comments list -->
                  <div v-if="comments.length > 0" class="space-y-4">
                    <div  
                      v-for="comment in comments" 
                      :key="comment.id"
                      class="bg-white p-4 rounded-lg border border-gray-200"
                    >
                      <div class="flex items-start gap-3">
                        <img 
                          :src="comment.user.avatar" 
                          :alt="comment.user.name"
                          class="w-8 h-8 rounded-full"
                        />
                        <div class="flex-1">
                          <div class="flex items-left flex-col justify-center">
                            <span class="font-medium text-sm">{{ comment.user.name }}</span>
                            <span class="text-xs text-dark">{{ formatDate(comment.created_at) }}</span>
                          </div>
                          <p class="text-sm text-dark mt-1">{{ comment.comment }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-sm text-dark text-center">
                    No comments yet
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 border-t bg-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-500">
              Press Esc to close
            </div>
            <div class="flex gap-3">
              <button @click="updateModalState(false)" class="btn-cancel">Cancel</button>
              <button 
                @click="saveChanges" 
                class="btn-primary"
                :disabled="isSaving"
              >
                {{ isSaving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style> 