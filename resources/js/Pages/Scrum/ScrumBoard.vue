<script setup>
import Sidebar from '../../Components/Sidebar.vue'
import Header from '../../Components/Header.vue'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Search, Users, Filter, ArrowUpDown, Video, Star, Share2 } from 'lucide-vue-next'
import Button from '../../Components/Button.vue'
import KanbanColumn from '../../Components/KanbanColumn.vue'
import { usePage } from '@inertiajs/vue3'
import graphics from '../../graphics'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage().props

const toDoTasks = ref([])
const inProgressTasks = ref([])
const doneTasks = ref([])
const searchQuery = ref('')
const epics = ref(page.epics || [])
const backlogs = ref(page.backlogs || [])

const columns = ref([
  { id: 'todo', title: 'To Do', tasks: toDoTasks },
  { id: 'progress', title: 'In Progress', tasks: inProgressTasks },
  { id: 'done', title: 'Done', tasks: doneTasks }
])

const teamMembers = ref([
  { id: 1, avatar: '/placeholder.svg?height=32&width=32' },
  { id: 2, avatar: '/placeholder.svg?height=32&width=32' },
  { id: 3, avatar: '/placeholder.svg?height=32&width=32' }
])

const taskCreating = ref({
  todo: false,
  progress: false,
  done: false
})

const updateTaskStatus = async (task, newStatus) => {
  try {
    const response = await axios.post('/scrum/backlog-status-update', {
      id: task.id,
      status: newStatus,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      task.status = newStatus
      // Re-organize tasks after successful status update
      organizeTasksByStatus()
    }
  } catch (error) {
    console.error('Error updating task status:', error)
    // Revert the task to its original column if there's an error
    organizeTasksByStatus()
  }
}

const handleTaskMove = (task, newColumnId) => {
  let newStatus
  switch (newColumnId) {
    case 'todo':
      newStatus = 'To Do'
      break
    case 'progress':
      newStatus = 'In Progress'
      break
    case 'done':
      newStatus = 'Done'
      break
  }
  
  if (newStatus && task.status !== newStatus) {
    console.log('Moving task to:', newStatus) // Debug log
    updateTaskStatus(task, newStatus)
  }
}

const organizeTasksByStatus = () => {
  if (backlogs.value.length > 0) {
    // Update the tasks in each column
    toDoTasks.value = backlogs.value.filter(task => task.status === 'To Do')
    inProgressTasks.value = backlogs.value.filter(task => task.status === 'In Progress')
    doneTasks.value = backlogs.value.filter(task => task.status === 'Done')
  }
}

onMounted(() => {
  organizeTasksByStatus()
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const hasEpics = computed(() => {
  return epics.value && epics.value.length > 0
})

const TaskNum = computed(() => {
  return toDoTasks.value.length + inProgressTasks.value.length + doneTasks.value.length + 1
})

const handleClickOutside = (event) => {
  // Only handle clicks if we're not clicking the create task button or inside the task creation area
  const taskCreationAreas = document.querySelectorAll('.task-creation-area')
  const createTaskButtons = document.querySelectorAll('.create-task-button')
  let shouldClose = true

  // Check if click was inside task creation area
  taskCreationAreas.forEach(area => {
    if (area && area.contains(event.target)) {
      shouldClose = false
    }
  })

  // Check if click was on create task button
  createTaskButtons.forEach(button => {
    if (button && button.contains(event.target)) {
      shouldClose = false
    }
  })

  if (shouldClose) {
    Object.keys(taskCreating.value).forEach(key => {
      taskCreating.value[key] = false
    })
  }
}

const createTask = (colId) => {
  // First close any other open task creators
  Object.keys(taskCreating.value).forEach(key => {
    taskCreating.value[key] = false
  })
  // Then open the selected one
  taskCreating.value[colId] = true
}

const createNewTask = async (taskData, columnId) => {
  let status;
  switch (columnId) {
    case 'todo':
      status = 'To Do'
      break
    case 'progress':
      status = 'In Progress'
      break
    case 'done':
      status = 'Done'
      break
  }

  try {
    const response = await axios.post('/scrum/backlog-create', {
      title: taskData.title,
      type: taskData.type.name,
      priority: 'Low',
      status: status,
      epicId: epics.value[0].id,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      const newTask = {
        id: response.data.id,
        title: taskData.title,
        type: taskData.type.name,
        status: status,
        priority: 'Low',
        assignees: [],
        epic_id: epics.value[0].id
      }
      
      backlogs.value.push(newTask)
      organizeTasksByStatus()
      
      // Reset task creation state
      taskCreating.value[columnId] = false
    }
  } catch (error) {
    console.error('Error creating task:', error)
  }
}
</script>

<template>
  <Head title="| Board" />
  <div class="min-h-screen" @click.self="taskCreating = {}">
    <Sidebar />
    <Header />
    <div class="ml-64 pt-16">
      <!-- Board Header -->
      <div class="p-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Board</span></h1>
          <div class="flex items-center -space-x-2"></div>
          <button class="p-2 text-gray-600 hover:text-gray-800">
            <Users class="w-5 h-5" />
          </button>
        </div>

        <div class="flex items-center gap-4">
          <button><Share2 /></button>
          <button><Star /></button>
          <Button text="Create Meeting" variant="primary" :style="`flex px-4 py-2 items-center gap-2`">
            <Video />
          </Button>
        </div>
      </div>

      <!-- Board Controls -->
      <div class="px-6 pb-6 flex items-center gap-4">
        <div class="relative flex-1 max-w-md">
          <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
          <input v-model="searchQuery" type="text" placeholder="Search"
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
        </div>
        <Button text="Members" :icon="Users" variant="outline" />
      </div>

      <!-- No Epics Message -->
      <div v-if="!hasEpics" class="px-6 flex flex-col items-center justify-center py-12">
        <img :src="graphics.noDataIllustration" class="w-32 h-32 mb-4">
        <p class="text-gray-600 mb-4">No epics found in this project.</p>
        <Link :href="'/scrum/backlog?id=' + page.projectDetails.id" class="btn-primary">
          Create Epic in Backlog
        </Link>
      </div>

      <!-- Kanban Board -->
      <div v-else class="px-6 pb-6 flex h-full gap-6">
        <KanbanColumn 
          v-for="column in columns" 
          :key="column.id" 
          :title="column.title" 
          :tasks="column.tasks"
          :columnId="column.id"
          :createTask="() => createTask(column.id)" 
          :isCreatingTask="taskCreating[column.id]"
          @create-new-task="(task) => createNewTask(task, column.id)" 
          @update:tasks="(newTasks) => column.tasks = newTasks"
          @taskMoved="handleTaskMove"
          :taskNum="TaskNum" 
        />
        <button
          class="w-80 h-12 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 flex items-center justify-center text-gray-600 hover:text-gray-800">
          Add Column
        </button>
      </div>
    </div>
  </div>
</template>