<script setup>
import { ref, computed, watch } from 'vue'
import { X, Plus, Search, ChevronRight } from 'lucide-vue-next'
import Modal from './Modal.vue'
import TextField from './TextField.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

const page = usePage().props

const templates = [
  {
    id: 1,
    title: 'Education Purpose',
    description: 'For students or teachers to plan lessons, track assignments, and manage deadlines.'
  },
  {
    id: 2,
    title: 'Event Planning',
    description: 'For planning events like conferences, weddings, or product launches.'
  },
  {
    id: 3,
    title: 'Scrum',
    description: 'Built for teams using the Scrum framework, emphasizing structured workflows and clear roles.'
  },
  {
    id: 4,
    title: 'Research Development',
    description: 'For teams conducting research, testing prototypes, or innovating products.'
  },
  {
    id: 5,
    title: 'Content Calendar',
    description: 'For marketers, bloggers, or social media managers to plan and track content publication schedules.'
  }
]

const selectedTemplate = ref(templates[2]) // Default to Scrum
const projectName = ref('')
const searchQuery = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const members = ref([
  {
    id: page.auth.user.id,
    name: page.auth.user.name,
    email: page.auth.user.email,
    role: 'Scrum Master',
    avatar: page.auth.user.avatar,
    isOwner: true
  }
])

const leader = ['Scrum Master', 'Educator', 'Coordinator', 'Team Lead', 'Content Manager']

const roles = {
  'Scrum': [
    'Product Owner',
    'Frontend Developer',
    'Backend Developer',
    'UI/UX Designer',
    'Quality Assurance',
    'Custom'
  ],
  'Education Purpose': [
    'Student',
    'Assistant',
    'Custom'
  ],
  'Event Planning': [
    'Vendor',
    'Guest',
    'Custom'
  ],
  'Research Development': [
    'Researcher',
    'Grammarian',
    'Evaluator',
    'Participant',
    'Statistician',
    'Custom'
  ],
  'Content Calendar': [
    'Assistant',
    'Artist',
    'Writer',
    'Editor',
    'Custom'
  ]
}

const projectKey = computed(() => {
  if (!projectName.value) return ''
  const words = projectName.value.split(' ')
  
  if (words.length === 1) {
    return words[0].slice(0, 4).toUpperCase()
  }
  
  if (words.length === 2) {
    return (words[0].length <= 4 && words[0].length >= 2) 
      ? words[0].toUpperCase()
      : words[0].slice(0, 2).toUpperCase() + words[1].slice(0, 2).toUpperCase()
  }
  
  return words.map(word => word[0]).join('').slice(0, 4).toUpperCase()
})

const form = useForm({
  name: '',
  key: '',
  owner_id: page.auth.user.id,
  template: 'Scrum',
  members: members
})

watch(projectName, (newValue) => {
  form.name = newValue
  form.key = projectKey.value
})

watch(members, (newValue) => {
  form.members = newValue
})

watch(selectedTemplate, (newValue) => {
  form.template = newValue.title
  const templateIndex = templates.findIndex(t => t.title === newValue.title)
  if (members.value[0]) {
    members.value[0].role = leader[templateIndex]
  }
})

watch(searchQuery, async (newValue) => {
  if (newValue.length >= 2) {
    isSearching.value = true
    try {
      const response = await axios.get('/search-users', {
        params: { search: newValue }
      })
      
      searchResults.value = response.data.filter(user => 
        !members.value.some(member => member.id === user.id)
      )
    } catch (error) {
      console.error('Search failed:', error)
    }
    isSearching.value = false
  } else {
    searchResults.value = []
  }
})

const addMember = (user) => {
  members.value.push({
    id: user.id,
    name: user.name,
    email: user.email || user.google_email || user.slack_email,
    role: roles[selectedTemplate.value.title][0],
    avatar: user.avatar,
    isOwner: false
  })
  searchResults.value = searchResults.value.filter(r => r.id !== user.id)
  searchQuery.value = ''
}

const removeMember = (memberId) => {
  members.value = members.value.filter(member => member.id !== memberId)
}

const createProject = () => {
  form.post('/create-project', {
    onSuccess: (response) => {
      emit('update:modelValue', false)
      // Force a full page reload to update the sidebar projects
      window.location.reload()
    }
  })
}
</script>

<template>
  <div v-if="modelValue" class="absolute inset-0 z-20 bg-black/50" @click="emit('update:modelValue', false)"></div>
  <Transition name="modal-fade">
    <div v-if="modelValue" class="fixed inset-0 z-50">
      <div class="absolute inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal content -->
          <div class="relative w-[1000px] max-w-[90vw] bg-white rounded-lg shadow-xl">
            <div class="max-h-[90vh] overflow-y-auto">
              <!-- Header -->
              <div class="sticky top-0 bg-white px-6 py-4 border-b z-10">
                <div class="flex justify-between items-center">
                  <h2 class="text-2xl font-bold">Create New Project</h2>
                  <button @click="emit('update:modelValue', false)" 
                          class="text-gray-400 hover:text-gray-600 transition-colors">
                    <X class="w-6 h-6" />
                  </button>
                </div>
              </div>

              <!-- Content -->
              <div class="grid grid-cols-2">
                <!-- Left Column - Project Details & Members -->
                <div class="p-6 space-y-6 border-r">
                  <!-- Project Details -->
                  <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                      <TextField
                        v-model="projectName"
                        type="text"
                        placeholder="Enter project name"
                        class="w-full"
                      />
                    </div>

                    <div v-if="projectName">
                      <label class="block text-sm font-medium text-gray-700 mb-1">Project Key</label>
                      <TextField
                        v-model="form.key"
                        type="text"
                        class="w-1/2"
                        readonly
                      />
                      <p v-if="form.errors.key" class="text-red-500 text-xs mt-1">{{ form.errors.key }}</p>
                    </div>
                  </div>

                  <!-- Members Section -->
                  <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Invite Members</label>
                    <div class="relative">
                      <TextField
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search by name or email"
                        class="w-full"
                      >
                        <template #prefix>
                          <Search class="w-5 h-5 text-gray-400" />
                        </template>
                      </TextField>

                      <!-- Search Results -->
                      <div v-if="searchResults.length > 0" 
                           class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-48 overflow-auto">
                        <div
                          v-for="user in searchResults"
                          :key="user.id"
                          @click="addMember(user)"
                          class="p-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2"
                        >
                          <img :src="user.avatar" :alt="user.name" class="w-8 h-8 rounded-full">
                          <div>
                            <div class="font-medium">{{ user.name }}</div>
                            <div class="text-sm text-gray-600">{{ user.email || user.google_email || user.slack_email }}</div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Members List -->
                    <div class="space-y-2 max-h-[250px] overflow-y-auto custom-scrollbar">
                      <div
                        v-for="member in members"
                        :key="member.id"
                        class="p-3 border rounded-lg flex items-center justify-between bg-white"
                      >
                        <div class="flex items-center gap-2">
                          <img :src="member.avatar" :alt="member.name" class="w-8 h-8 rounded-full">
                          <div>
                            <div class="font-medium">{{ member.name }}</div>
                            <div class="text-sm text-gray-600">{{ member.role }}</div>
                          </div>
                        </div>
                        <button
                          v-if="!member.isOwner"
                          @click="removeMember(member.id)"
                          class="text-gray-400 hover:text-gray-600"
                        >
                          <X class="w-5 h-5" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Right Column - Templates -->
                <div class="p-6">
                  <label class="block text-sm font-medium text-gray-700 mb-3">Select Template</label>
                  <div class="grid gap-3">
                    <div
                      v-for="template in templates"
                      :key="template.id"
                      @click="selectedTemplate = template"
                      class="p-3 border rounded-lg cursor-pointer transition-colors"
                      :class="selectedTemplate.id === template.id ? 'border-blue bg-blue/5' : 'hover:bg-gray-50'"
                    >
                      <h3 class="font-medium">{{ template.title }}</h3>
                      <p class="text-sm text-gray-600 mt-1">{{ template.description }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="sticky bottom-0 bg-white px-6 py-4 border-t flex justify-between items-center">
                <div class="text-sm text-gray-500">
                  {{ members.length }} member{{ members.length !== 1 ? 's' : '' }} selected
                </div>
                <div class="flex gap-3">
                  <button @click="emit('update:modelValue', false)" 
                          class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                  </button>
                  <button
                    @click="createProject"
                    class="btn-primary flex items-center gap-2"
                    :class="!projectName || members.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    :disabled="!projectName || members.length === 0"
                  >
                    <Plus class="w-4 h-4" />
                    Create Project
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: all 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #E5E7EB transparent;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #E5E7EB;
  border-radius: 3px;
}
</style> 