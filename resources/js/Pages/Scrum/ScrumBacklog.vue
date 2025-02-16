<script setup>
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref } from 'vue'
import { Search, Filter, ArrowUpDown, MoreHorizontal, ChevronRight, Edit2, Users } from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3';

const page = usePage().props;

const searchQuery = ref('')
const selectedEpic = ref('Login and Register')

const epics = ref([
  { 
    name: 'Login and Register',
    isActive: true
  },
  { 
    name: 'Project Creation',
    isActive: false
  }
])

const tasks = ref([
  {
    id: 1,
    title: 'Wireframe',
    status: 'To Do',
    assignees: [
      '/placeholder.svg?height=32&width=32',
      '/placeholder.svg?height=32&width=32'
    ]
  },
  {
    id: 2,
    title: 'User Flow',
    status: 'In Progress',
    assignees: ['/placeholder.svg?height=32&width=32']
  },
  {
    id: 3,
    title: 'UI Design',
    status: 'Done',
    assignees: [
      '/placeholder.svg?height=32&width=32',
      '/placeholder.svg?height=32&width=32'
    ]
  },
  {
    id: 4,
    title: 'UX Design',
    status: 'To Do',
    assignees: [
      '/placeholder.svg?height=32&width=32',
      '/placeholder.svg?height=32&width=32'
    ]
  }
])

const statusOptions = ['To Do', 'In Progress', 'Done']

const taskCounts = {
  todo: 6,
  inProgress: 3,
  completed: 6
}
</script>

<template>
  <Header />
  <Sidebar />
  
  <div class="ml-64 pt-16 p-6">
    <div class="flex items-center justify-between mb-6">
      <div class="p-12 flex items-center">
        <h1 class="text-2xl font-bold mb-6">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Backlog</span></h1>
      </div>
      <div class="flex items-center gap-2">
        <div class="flex -space-x-2">
          <img 
            v-for="i in 2" 
            :key="i"
            src="" 
            class="w-8 h-8 rounded-full border-2 border-white"
          />
          <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600 border-2 border-white">
            +3
          </span>
        </div>
        <button class="p-2 hover:bg-gray-100 rounded-lg">
          <Users class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Controls -->
    <div class="flex gap-4 mb-6">
      <div class="relative flex-1 max-w-md">
        <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search"
          class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 flex items-center gap-2">
        <Filter class="w-5 h-5" />
        Filter
      </button>

      <button class="px-4 py-2 border rounded-lg hover:bg-gray-50 flex items-center gap-2">
        <ArrowUpDown class="w-5 h-5" />
        Sort
      </button>
    </div>

    <!-- Two Column Layout -->
    <div class="flex gap-6">
      <!-- Epic List -->
      <div class="w-64 bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-xl font-semibold mb-4">Epic</h2>
        
        <div class="space-y-2">
          <button
            v-for="epic in epics"
            :key="epic.name"
            class="w-full px-4 py-2 rounded-lg text-left flex items-center justify-between hover:bg-gray-50"
            :class="epic.isActive ? 'bg-blue-50 text-blue-600' : 'text-gray-700'"
          >
            {{ epic.name }}
            <ChevronRight v-if="epic.isActive" class="w-5 h-5" />
          </button>
        </div>

        <button class="w-full mt-4 px-4 py-2 border border-dashed rounded-lg text-gray-600 hover:border-gray-400 flex items-center gap-2">
          <span class="text-xl">+</span> Create Epic
        </button>
      </div>

      <!-- Task List -->
      <div class="flex-1 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-4">
            <h2 class="text-xl font-semibold">{{ selectedEpic }}</h2>
            <div class="text-sm text-gray-500">4 Tasks</div>
            <button class="p-1 hover:bg-gray-100 rounded">
              <Edit2 class="w-4 h-4" />
            </button>
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full">{{ taskCounts.todo }}</span>
              <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full">{{ taskCounts.inProgress }}</span>
              <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full">{{ taskCounts.completed }}</span>
            </div>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Complete Sprint
            </button>
          </div>
        </div>

        <div class="text-sm text-gray-500 mb-6">
          Dec. 1, 2024 - Dec. 14, 2024
        </div>

        <!-- Tasks -->
        <div class="space-y-2">
          <div
            v-for="task in tasks"
            :key="task.id"
            class="flex items-center gap-4 p-4 border rounded-lg hover:bg-gray-50"
          >
            <input type="checkbox" class="w-5 h-5 rounded border-gray-300" />
            
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13 2v7h7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-medium">{{ task.title }}</span>
              </div>
            </div>

            <select 
              v-model="task.status"
              class="px-3 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500"
              :class="{
                'text-gray-700 bg-gray-50': task.status === 'To Do',
                'text-orange-600 bg-orange-50': task.status === 'In Progress',
                'text-green-600 bg-green-50': task.status === 'Done'
              }"
            >
              <option v-for="status in statusOptions" :key="status" :value="status">
                {{ status }}
              </option>
            </select>

            <div class="flex -space-x-2">
              <img 
                v-for="(assignee, index) in task.assignees"
                :key="index"
                :src="assignee"
                class="w-8 h-8 rounded-full border-2 border-white"
              />
            </div>

            <button class="p-2 hover:bg-gray-100 rounded">
              <MoreHorizontal class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>