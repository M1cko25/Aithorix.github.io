<script setup>
import Sidebar from '../../Components/SideBar.vue'
import Header from '@/Components/Header.vue'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Search, Users, Filter, ArrowUpDown, Video, Star, Share2, X } from 'lucide-vue-next'
import Button from '@/Components/Button.vue'
import KanbanColumn from '@/Components/KanbanColumn.vue'
import { usePage } from '@inertiajs/vue3'
import noDataIllustration from '@/assets/noDataIllustration.svg'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import TaskModal from './ScrumComponents/TaskModal.vue'
import DeleteTaskModal from './ScrumComponents/DeleteTaskModal.vue';
import Lira from '@/Components/Lira.vue';
import MeetingModal from '@/Components/Meeting/MeetingModal.vue';

const page = usePage().props;
const isLiraOpen = ref(false);
const showMeetingModal = ref(false);
const toDoTasks = ref([])
const inProgressTasks = ref([])
const doneTasks = ref([])
const searchQuery = ref('')
const epics = ref(page.epics || [])
const backlogs = ref(page.backlogs || [])
const selectedEpic = ref(epics.value[0] || null)
const isAddingColumn = ref(false)
const newColumnTitle = ref('')

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

const columns = ref(page.columns.map(col => ({
  ...col,
  id: col.id.toString(),
  tasks: [],
  isDefault: ['To Do', 'In Progress', 'Done'].includes(col.title)
})))

const isTaskModalOpen = ref(false)
const selectedTaskToEdit = ref(null)
const isDeleteModalOpen = ref(false)
const selectedTaskToDelete = ref(null)

const updateTaskStatus = async (task, newStatus) => {
  try {
    const response = await axios.post('/scrum/backlog-status-update', {
      id: task.id,
      status: newStatus,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      task.status = newStatus
      organizeTasksByStatus()
    }
  } catch (error) {
    console.error('Error updating task status:', error)
    organizeTasksByStatus()
  }
}

const handleTaskMove = (task, newColumnId) => {
  const column = columns.value.find(col => col.id === newColumnId)
  if (column && task.status !== column.title) {
    updateTaskStatus(task, column.title)
  }
}

const organizeTasksByStatus = () => {
  if (backlogs.value.length > 0) {
    toDoTasks.value = backlogs.value.filter(task => task.status === 'To Do')
    inProgressTasks.value = backlogs.value.filter(task => task.status === 'In Progress')
    doneTasks.value = backlogs.value.filter(task => task.status === 'Done')
  }
}

const filterTasksByEpic = () => {
  if (!selectedEpic.value) {
    columns.value.forEach(col => col.tasks = [])
    return
  }

  const filteredBacklogs = backlogs.value.filter(task => task.epic_id === selectedEpic.value.id)

  columns.value.forEach(column => {
    column.tasks = filteredBacklogs.filter(task => task.status === column.title)
  })
}

watch(selectedEpic, () => {
  filterTasksByEpic()
})

onMounted(() => {
  filterTasksByEpic()
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
  const taskCreationAreas = document.querySelectorAll('.task-creation-area')
  const createTaskButtons = document.querySelectorAll('.create-task-button')
  let shouldClose = true

  taskCreationAreas.forEach(area => {
    if (area && area.contains(event.target)) {
      shouldClose = false
    }
  })

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
  Object.keys(taskCreating.value).forEach(key => {
    taskCreating.value[key] = false
  })
  taskCreating.value[colId] = true
}

const createNewTask = async (taskData, columnId) => {
  const column = columns.value.find(col => col.id === columnId);
  if (!column) return;
  try {
    const response = await axios.post('/scrum/backlog-create', {
      title: taskData.title,
      type: taskData.type.name,
      priority: 'Low',
      status: column.title,
      epicId: selectedEpic.value.id,
      projectId: page.projectDetails.id
    })

    if (response.data.success) {
      const newTask = {
        id: response.data.backlog.id,
        title: response.data.backlog.title,
        key: response.data.backlog.key,
        type: taskData.type.name,
        status: column.title,
        priority: 'Low',
        assignees: [],
        comments: response.data.backlog.comments,
        attachments: response.data.backlog.attachments,
        epic_id: response.data.backlog.epic_id,
      }
      backlogs.value.push(newTask)
      filterTasksByEpic()
      taskCreating.value[columnId] = false
    }
  } catch (error) {
    console.error('Error creating task:', error)
  }
}
const addColumn = async () => {
  if (newColumnTitle.value.trim()) {
    try {
      const response = await axios.post('/scrum/add-column', {
        title: newColumnTitle.value.trim(),
        projectId: page.projectDetails.id
      })

      if (response.data.success) {
        const newColumn = {
          id: response.data.id.toString(),
          title: newColumnTitle.value.trim(),
          tasks: [],
          isDefault: false
        }
        columns.value.push(newColumn)
        newColumnTitle.value = ''
        isAddingColumn.value = false
      }
    } catch (error) {
      console.error('Error creating column:', error)
    }
  }
}

const removeColumn = async (columnId) => {
  const columnIndex = columns.value.findIndex(col => col.id === columnId)
  if (columnIndex !== -1 && !columns.value[columnIndex].isDefault) {
    try {
      const response = await axios.post('/scrum/delete-column', {
        columnId: columnId,
        projectId: page.projectDetails.id
      })

      if (response.data.success) {
        const tasksToMove = columns.value[columnIndex].tasks
        const toDoColumn = columns.value.find(col => col.title === 'To Do')
        if (toDoColumn && tasksToMove.length > 0) {
          for (const task of tasksToMove) {
            await updateTaskStatus(task, 'Done');
            handleTaskUpdate(task);
          }
        }
        columns.value.splice(columnIndex, 1)
      }
    } catch (error) {
      console.error('Error deleting column:', error)
    }
  }
}

const handleKeyPress = (event) => {
  if (event.key === 'Enter') {
    addColumn()
  } else if (event.key === 'Escape') {
    isAddingColumn.value = false
    newColumnTitle.value = ''
  }
}

const handleEditTask = (task) => {
    selectedTaskToEdit.value = task
    isTaskModalOpen.value = true
}

const handleDeleteTask = (task) => {
    selectedTaskToDelete.value = task;
    isDeleteModalOpen.value = true;
}

watch(isDeleteModalOpen, (newValue) => {
    if (!newValue && selectedTaskToDelete.value) {
        backlogs.value = backlogs.value.filter(t => t.id !== selectedTaskToDelete.value.id);
        filterTasksByEpic();
        selectedTaskToDelete.value = null;
    }
});

watch(selectedTaskToEdit, (newTask) => {
  if (newTask) {
    const taskIndex = backlogs.value.findIndex(t => t.id === newTask.id);
    if (taskIndex !== -1) {
      backlogs.value[taskIndex] = { ...backlogs.value[taskIndex], ...newTask };
      filterTasksByEpic();
    }
  }
});

const handleTaskUpdate = (updatedTask) => {
  const taskIndex = backlogs.value.findIndex(t => t.id === updatedTask.id);
  if (taskIndex !== -1) {
    backlogs.value[taskIndex] = { ...backlogs.value[taskIndex], ...updatedTask };
    filterTasksByEpic();
  }
}
const isSidebarOpen = ref(true);
const logoDisplayed = ref(true);
</script>

<template>
  <Head title="| Board" />
  <div class="min-h-screen" @click.self="taskCreating = {}">
    <Sidebar @sidebarCollapsed="(value) => { isSidebarOpen = value }" @logoAppear="(value) => logoDisplayed = value"/>
    <Header :logoDisplay="logoDisplayed" />
    <TaskModal
      v-model:isOpen="isTaskModalOpen"
      :task="selectedTaskToEdit"
      :epicSelected="selectedEpic"
      :comments="selectedTaskToEdit ? (page.comments?.[selectedTaskToEdit.id] || []) : []"
      @update:task="handleTaskUpdate"
    />
    <DeleteTaskModal
      v-model:isOpen="isDeleteModalOpen"
      :epicSelected="selectedEpic"
      :selectedTaskToUpdate="[selectedTaskToDelete]"
      :taskCounts="columns.reduce((acc, col) => {
        acc[col.title.toLowerCase().replace(/\s+/g, '')] = col.tasks.length;
        return acc;
      }, {})"
    />
    <MeetingModal v-model="showMeetingModal"/>
    <div class="pt-16 transition-all duration-300 ease-in-out" :class="`${isSidebarOpen ? 'ml-16' : 'ml-64'}`">
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
          <button><Share2/></button>
          <button><Star/></button>
          <button @click="showMeetingModal = true">
            <Video />
          </button>
        </div>
      </div>
      <!-- Board Controls -->
      <div class="px-6 pb-6 flex items-center gap-4">
        <div class="relative flex-1 max-w-md">
          <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
          <input v-model="searchQuery" type="text" placeholder="Search"
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="flex items-center gap-4">
          <div class="w-64">
            <select
              v-model="selectedEpic"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            >
              <option v-for="epic in [...epics].sort((a, b) => a.order - b.order)" :key="epic.id" :value="epic">
                {{ epic.name }}
              </option>
            </select>
        </div>
        <Button text="Members" :icon="Users" variant="outline" />
        </div>
      </div>

      <!-- No Epics Message -->
      <div v-if="!hasEpics" class="px-6 flex flex-col items-center justify-center py-12">
        <img :src="noDataIllustration" class="w-32 h-32 mb-4">
        <p class="text-gray-600 mb-4">No epics found in this project.</p>
        <Link :href="'/scrum/backlog?id=' + page.projectDetails.id" class="btn-primary">
          Create Epic in Backlog
        </Link>
      </div>

      <!-- Kanban Board -->
      <div v-else class="px-6 pb-6">
        <div class="flex gap-6 overflow-x-auto min-w-full kanban-container">
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
            @editTask="handleEditTask"
            @deleteTask="handleDeleteTask"
          :taskNum="TaskNum"
            class="flex-shrink-0"
          >
            <template v-if="!column.isDefault" #column-header-actions>
              <button
                @click="removeColumn(column.id)"
                class="p-1 hover:bg-gray-100 rounded-full"
              >
                <X class="w-4 h-4 text-gray-500" />
              </button>
            </template>
          </KanbanColumn>

          <!-- Add Column Button/Form -->
          <div v-if="!isAddingColumn"
            @click="isAddingColumn = true"
            class="w-80 h-12 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 flex items-center justify-center text-gray-600 hover:text-gray-800 cursor-pointer flex-shrink-0">
            Add Column
          </div>
          <div v-else class="w-80 bg-white rounded-lg p-4 border border-gray-200 flex-shrink-0">
            <input
              v-model="newColumnTitle"
              type="text"
              class="w-full px-3 py-2 border rounded-lg mb-3"
              placeholder="Enter column title"
              @keyup="handleKeyPress"
              ref="columnTitleInput"
              autofocus
            />
            <div class="flex justify-end gap-2">
              <button
                @click="isAddingColumn = false"
                class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded"
              >
                Cancel
              </button>
        <button
                @click="addColumn"
                class="btn-primary px-3 py-1"
              >
                Add
        </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <Lira :isOpen="isLiraOpen" @update:isOpen="isLiraOpen = $event" />
</template>

<style scoped>
.kanban-container {
  min-height: calc(100vh - 200px);
  padding-bottom: 1rem;
}

.overflow-x-auto {
  overflow-x: auto;
  scrollbar-width: thin;
  scrollbar-color: #CBD5E0 #F3F4F6;
}

.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #F3F4F6;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: #CBD5E0;
  border-radius: 4px;
}
</style>
