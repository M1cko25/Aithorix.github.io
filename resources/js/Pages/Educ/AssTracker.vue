<script setup>
import { ref } from 'vue'
import Sidebar from '../../Components/Sidebar.vue'
import Header from '../../Components/Header.vue'
import { Head } from '@inertiajs/vue3'
import {
  Filter,
  SortAsc,
  MoreHorizontal,
  X
} from 'lucide-vue-next'

// Only one modal can be open at a time
const closeAllModals = () => {
  isFilterModalOpen.value = false
  isSortModalOpen.value = false
}

const toggleFilterModal = () => {
  closeAllModals()
  isFilterModalOpen.value = !isFilterModalOpen.value
}

const toggleSortModal = () => {
  closeAllModals()
  isSortModalOpen.value = !isSortModalOpen.value
}

// Separate the modal states
const isFilterModalOpen = ref(false)
const isSortModalOpen = ref(false)

const filterOptions = ref({
  status: ''
})

const sortOptions = ref({
  priority: ''
})

const statusOptions = [
  'All',
  'Completed',
  'In Progress',
  'Pending',
  'Not Started'
]

const priorityOptions = [
  'All',
  'High',
  'Medium',
  'Low'
]

// const selectedStatus = ref([])
// const selectedPriority = ref([])

const selectStatus = (status) => {
  filterOptions.value.status = status
  applyFilters()
}

const selectPriority = (priority) => {
  sortOptions.value.priority = priority
  applySort()
}
const tasks = ref([
  {
    id: 1,
    name: 'Math Class',
    dueDate: 'Dec 4, 2024',
    priority: 'High',
    status: 'Completed'
  },
  {
    id: 2,
    name: 'History Paper',
    dueDate: 'Dec 5, 2024',
    priority: 'Medium',
    status: 'In Progress'
  },
  {
    id: 3,
    name: 'Chemistry Lab Report',
    dueDate: 'Dec 3, 2024',
    priority: 'Low',
    status: 'Pending'
  },
  {
    id: 4,
    name: 'Physics Exam',
    dueDate: 'Dec 10, 2024',
    priority: 'High',
    status: 'Not Started'
  },
  {
    id: 5,
    name: 'Final Exam',
    dueDate: 'Dec 12, 2024',
    priority: 'High',
    status: 'Pending'
  },
  {
    id: 6,
    name: 'Biology Class',
    dueDate: 'Dec 11, 2024',
    priority: 'Medium',
    status: 'In Progress'
  },
  {
    id: 7,
    name: 'Algebra Assignment',
    dueDate: 'Dec 15, 2024',
    priority: 'Medium',
    status: 'Not Started'
  }
])

// const toggleFilterModal = () => {
//   isFilterModalOpen.value = !isFilterModalOpen.value
// }

// const toggleSortModal = () => {
//   isSortModalOpen.value = !isSortModalOpen.value
// }

const applyFilters = () => {
  const filteredTasks = tasks.value.filter(task => {
    if (filterOptions.value.status === '' || filterOptions.value.status === 'All') return true
    return task.status === filterOptions.value.status
  })
  tasks.value = filteredTasks
  closeAllModals()
}

const applySort = () => {
  const sortedTasks = [...tasks.value].sort((a, b) => {
    if (sortOptions.value.priority === '' || sortOptions.value.priority === 'All') return 0
    if (a.priority === sortOptions.value.priority) return -1
    if (b.priority === sortOptions.value.priority) return 1
    return 0
  })
  tasks.value = sortedTasks
  closeAllModals()
}

const getPriorityClass = (priority) => {
  const classes = {
    High: 'bg-red-100 text-red-700',
    Medium: 'bg-orange-100 text-orange-700',
    Low: 'bg-green-100 text-green-700'
  }
  return classes[priority]
}
</script>

<template>

  <Head title=" | Assignment Tracker" />
  <div class="p-6 bg-gray-50 min-h-screen">
    <Sidebar />
    <Header />
    <div class="ml-64 pt-16">
      <main class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
          <div class="justify-between mb-6">
            <h1 class="text-2xl font-semibold">Assignment Tracker</h1>
            <div class="flex gap-4 pt-6 pb-4">

              <!-- Filter Button and Popup -->
              <div class="relative">
                <button @click="toggleFilterModal"
                  class="flex items-center gap-2 px-6 py-2 border rounded-md hover:bg-gray-50">
                  <Filter class="w-4 h-4" />
                  Filter
                </button>

                <div v-if="isFilterModalOpen"
                  class="absolute top-full right-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[160px]">
                  <button v-for="status in statusOptions" :key="status" @click="selectStatus(status)"
                    class="w-full px-4 py-2 text-left hover:bg-gray-100">
                    {{ status }}
                  </button>
                </div>
              </div>
              <!-- Sort Button and Popup -->
              <div class="relative">
                <button @click="toggleSortModal"
                  class="flex items-center gap-2 px-6 py-2 border rounded-md hover:bg-gray-50">
                  <SortAsc class="w-4 h-4" />
                  Sort
                </button>

                <div v-if="isSortModalOpen"
                  class="absolute top-full right-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[160px]">
                  <button v-for="priority in priorityOptions" :key="priority" @click="selectPriority(priority)"
                    class="w-full px-4 py-2 text-left hover:bg-gray-100">
                    {{ priority }}
                  </button>
                </div>
              </div>
            </div>


            <!-- Assignment Table -->
            <div class="bg-white rounded-lg border border-gray-200">
              <div class="grid grid-cols-[2fr,1fr,1fr,1fr,auto] gap-4 p-4 border-b border-gray-200 font-medium">
                <div>Task Name</div>
                <div>Due Date</div>
                <div>Priority</div>
                <div>Status</div>
              </div>
              <div class="divide-y divide-gray-200">
                <div v-for="task in tasks" :key="task.id"
                  class="grid grid-cols-[2fr,1fr,1fr,1fr,auto] gap-4 p-4 items-center even:bg-gray-100">
                  <div>{{ task.name }}</div>
                  <div>{{ task.dueDate }}</div>
                  <div>
                    <span :class="getPriorityClass(task.priority)" class="px-2 py-1 rounded-full text-sm">
                      {{ task.priority }}
                    </span>
                  </div>
                  <div>{{ task.status }}</div>
                  <button class="p-1 hover:bg-gray-100 rounded">
                    <MoreHorizontal class="w-5 h-5 text-gray-600" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
