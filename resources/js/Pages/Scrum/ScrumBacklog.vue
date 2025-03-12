<script setup>
// Component Imports
import Header from '@/Components/Header.vue'
import Sidebar from '../../Components/SideBar.vue'
import EpicContainer from './ScrumComponents/EpicContainer.vue'
import BacklogContainer from './ScrumComponents/BacklogContainer.vue'
import SprintModal from './ScrumComponents/SprintModal.vue'
import CompletedSprintModal from './ScrumComponents/CompletedSprintModal.vue'
import DeleteTaskModal from './ScrumComponents/DeleteTaskModal.vue'
import TaskModal from './ScrumComponents/TaskModal.vue'
import EpicModal from './ScrumComponents/EpicModal.vue'
import MoveTaskModal from './ScrumComponents/MoveTaskModal.vue'
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Filter, ArrowUpDown, Trash, Replace } from 'lucide-vue-next'
import TextField from '@/Components/TextField.vue'
import noDataIllustration from '@/assets/noDataIllustration.svg'
import axios from 'axios'

const page = usePage().props
const projEpics = ref(page.epics || [])
const epics = ref([])

if (projEpics.value.length > 0) {
  epics.value = projEpics.value
    .sort((a, b) => a.order - b.order)
    .map(epic => ({
      id: epic.id,
      name: epic.name,
      isActive: epic.order === 1,
      description: epic.description,
      key: epic.key,
      status: epic.status,
      order: epic.order,
      progress: `${epic.progress_percent}%`,
      tasks: page.backlogs.filter(backlog => backlog.epic_id === epic.id) || []
    }))
}

const searchQuery = ref('')
const epicSelected = ref(epics.value[0] || {
  name: 'No Epic Created',
  tasks: [],
  epic_id: null
})
const selectedTaskToUpdate = ref([])
const isDeleteModalOpen = ref(false)
const openSprint = ref(false)
const openCompleteSprint = ref(false)
const isTaskModalOpen = ref(false)
const selectedTaskToEdit = ref(null)
const isEpicModalOpen = ref(false)
const epicToEdit = ref(null)
const isMoveModalOpen = ref(false)

const calculateTaskCounts = () => {
  const counts = {};

  page.columns.forEach(column => {
    const columnKey = column.title.toLowerCase().replace(/\s+/g, '');
    counts[columnKey] = epicSelected.value.tasks.filter(
      task => task.status === column.title
    ).length;
  });
  
  return counts;
};

const taskCounts = ref(calculateTaskCounts());

const sprint = ref(page.sprint || null)

const updateEpicOrder = (updatedEpics) => {
  epics.value = updatedEpics
}

const updateEpicSelected = (epic) => {
  epicSelected.value = epic
  selectedTaskToUpdate.value = [] 
  taskCounts.value = calculateTaskCounts()
}

const handleSprintAction = () => {
  if (epicSelected.value.status === 'On Sprint') {
    openCompleteSprint.value = true
  } else {
    openSprint.value = true
  }
}

const handleSprintCreated = (newSprint) => {
  sprint.value = newSprint
}

const formatSprintDates = computed(() => {
  if (!sprint.value?.start_date || !sprint.value?.end_date) return ''
    
    const formatDate = (date) => {
    const month = new Date(date).toLocaleString('en-US', { month: 'short' })
    const day = new Date(date).getDate()
      return `${month}. ${day}`
    }
    
  return `${formatDate(sprint.value.start_date)} - ${formatDate(sprint.value.end_date)}`
})

const showTrashButton = computed(() => selectedTaskToUpdate.value.length > 0)

watch(() => epicSelected.value.tasks, (newTasks) => {
  if (newTasks) {
    taskCounts.value = calculateTaskCounts()
  }
}, { deep: true })

const handleEditTask = (task) => {
  selectedTaskToEdit.value = task
  isTaskModalOpen.value = true
}

const handleEditEpic = (epic) => {
  epicToEdit.value = epic
  isEpicModalOpen.value = true
}

const handleEpicDeleted = (deletedEpicId) => {
  epics.value = epics.value.filter(epic => epic.id !== deletedEpicId)
  
  if (epicSelected.value.id === deletedEpicId && epics.value.length > 0) {
    const newSelectedEpic = epics.value[0]
    newSelectedEpic.isActive = true
    epicSelected.value = newSelectedEpic
    taskCounts.value = calculateTaskCounts()
  } else if (epics.value.length === 0) {
    epicSelected.value = {
      name: 'No Epic Created',
      tasks: [],
      epic_id: null
    }
    taskCounts.value = calculateTaskCounts()
  }
}

const handleTaskUpdate = (updatedTask) => {
  // Find and update the task in backlogs
  const backlogIndex = page.backlogs.findIndex(b => b.id === updatedTask.id);
  if (backlogIndex !== -1) {
    // Create new object references to trigger reactivity
    page.backlogs = [
      ...page.backlogs.slice(0, backlogIndex),
      { ...updatedTask },
      ...page.backlogs.slice(backlogIndex + 1)
    ];
  }

  // Find and update the task in the selected epic's tasks
  if (epicSelected.value && epicSelected.value.tasks) {
    const taskIndex = epicSelected.value.tasks.findIndex(t => t.id === updatedTask.id);
    if (taskIndex !== -1) {
      epicSelected.value.tasks = [
        ...epicSelected.value.tasks.slice(0, taskIndex),
        { ...updatedTask },
        ...epicSelected.value.tasks.slice(taskIndex + 1)
      ];
    }
  }

  // Update the task in the epics array as well
  const epicIndex = epics.value.findIndex(e => e.id === updatedTask.epic_id);
  if (epicIndex !== -1) {
    const taskIndex = epics.value[epicIndex].tasks.findIndex(t => t.id === updatedTask.id);
    if (taskIndex !== -1) {
      epics.value[epicIndex].tasks = [
        ...epics.value[epicIndex].tasks.slice(0, taskIndex),
        { ...updatedTask },
        ...epics.value[epicIndex].tasks.slice(taskIndex + 1)
      ];
    }
  }
}

// Add watch for task updates from TaskModal
watch(() => selectedTaskToEdit.value, (newTask) => {
  if (newTask) {
    handleTaskUpdate(newTask);
  }
}, { deep: true });

const handleMoveTask = (tasks) => {
  selectedTaskToUpdate.value = tasks
  isMoveModalOpen.value = true
}

const handleTasksMoved = ({ tasks, targetEpic }) => {
  // Remove tasks from current epic's list
  tasks.forEach(task => {
    const index = epicSelected.value.tasks.findIndex(t => t.id === task.id)
    if (index > -1) {
      epicSelected.value.tasks.splice(index, 1)
    }
  })
  
  // Find the target epic in epics array and add tasks to it
  const targetEpicIndex = epics.value.findIndex(e => e.id === targetEpic.id)
  if (targetEpicIndex !== -1) {
    // Update each task's epic_id
    const updatedTasks = tasks.map(task => ({
      ...task,
      epic_id: targetEpic.id
    }))
    
    // Add tasks to target epic
    if (!epics.value[targetEpicIndex].tasks) {
      epics.value[targetEpicIndex].tasks = []
    }
    epics.value[targetEpicIndex].tasks.push(...updatedTasks)
  }
  
  // Update task counts for current epic
  taskCounts.value = {
    todo: epicSelected.value.tasks.filter(task => task.status === 'To Do').length,
    inProgress: epicSelected.value.tasks.filter(task => task.status === 'In Progress').length,
    completed: epicSelected.value.tasks.filter(task => task.status === 'Done').length
  }
  
  // Clear selected tasks
  selectedTaskToUpdate.value = []
}

const handleDeleteTask = () => {
  if (!selectedTaskToUpdate.value.length) return;
  
  const tasksToDelete = selectedTaskToUpdate.value;
  const deletePromises = tasksToDelete.map(task => 
    axios.post('/scrum/backlog-delete', {
      id: task.id,
      epicId: task.epic_id,
      title: task.title,
      projectId: page.projectDetails.id
    }).then(() => {
      const index = epicSelected.value.tasks.findIndex(t => t.id === task.id);
      if (index !== -1) {
        epicSelected.value.tasks.splice(index, 1);
      }
    })
  );

  Promise.all(deletePromises)
    .then(() => {
      // Remove tasks from epicSelected.tasks
      tasksToDelete.forEach(task => {
        const index = epicSelected.value.tasks.findIndex(t => t.id === task.id);
        if (index !== -1) {
          epicSelected.value.tasks.splice(index, 1);
        }
      });

      // Update task counts
      taskCounts.value = calculateTaskCounts();
      
      // Clear selected tasks
      selectedTaskToUpdate.value = [];
      isDeleteModalOpen.value = false;
    })
    .catch(error => {
      console.error('Error deleting tasks:', error);
    });
}
const isSidebarOpen = ref(true);
const logoDisplayed = ref(true);
</script>

<template>
  <Head title="| Backlog" />
  <Header :logoDisplay="logoDisplayed"/>
  <Sidebar @sidebarCollapsed="(value) => { isSidebarOpen = value }" @logoAppear="(value) => logoDisplayed = value"/>
  
  <DeleteTaskModal 
    v-model:isOpen="isDeleteModalOpen"
    :task="selectedTaskToUpdate.length === 1 ? selectedTaskToUpdate[0] : null"
    :tasks="selectedTaskToUpdate"
    @confirm="handleDeleteTask"
  />
  
  <SprintModal 
    v-model:isOpen="openSprint"
    :epicSelected="epicSelected"
    :epics="epics"
    @sprintCreated="handleSprintCreated"
  />

  <CompletedSprintModal 
    v-model:isOpen="openCompleteSprint"
    :epicSelected="epicSelected"
    :epics="epics"
  />

  <TaskModal
    v-model:isOpen="isTaskModalOpen"
    :task="selectedTaskToEdit || {}"
    :epicSelected="epicSelected"
    context="backlog"
    @update:task="handleTaskUpdate"
  />
  <EpicModal
    v-model:isOpen="isEpicModalOpen"
    :epic="epicToEdit"
    @epicDeleted="handleEpicDeleted"
  />

  <MoveTaskModal
    v-model:isOpen="isMoveModalOpen"
    :tasks="selectedTaskToUpdate"
    :current-epic-id="epicSelected.id"
    :epics="page.epics"
    @tasks-moved="handleTasksMoved"
  />

  <div class="pt-16 p-6 mb-5 transition-all duration-300 ease-in-out" :class="`${isSidebarOpen ? 'ml-16' : 'ml-64'}`">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="py-6 flex items-center">
        <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Backlog</span></h1>
      </div>
    </div>

    <!-- Controls -->
    <div class="flex gap-4 mb-6">
      <div class="flex-1 max-w-md">
        <TextField v-model="searchQuery" type="search" placeholder="Search" class="w-full" />
      </div>
      <button class="btn-cancel">
        <Filter class="w-5 h-5" />
        Filter
      </button>
      <button class="btn-cancel">
        <ArrowUpDown class="w-5 h-5" />
        Sort
      </button>
    </div>

    <!-- Two Column Layout -->
    <div class="flex gap-6">
      <EpicContainer 
        :epic="epics"
        :epicSelected="epicSelected"
        :projEpics="projEpics"
        :taskCounts="taskCounts"
        @updateEpicOrder="updateEpicOrder"
        @updateEpicSelected="updateEpicSelected"
        @editEpic="handleEditEpic"
      />

      <!-- Task List -->
      <div class="flex-1 bg-white rounded-xl shadow-sm p-6">
        <div v-if="epics.length > 0" class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-4">
            <h2 class="text-xl font-semibold">{{ epicSelected.name }}</h2>
            <div class="text-sm text-gray-500">{{ epicSelected.tasks.length }} {{epicSelected.tasks.length > 1 ? 'backlogs' : 'backlog'}}</div>
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span 
                v-for="column in page.columns" 
                :key="column.id" 
                class="px-3 py-1 rounded-full"
                :class="{
                  'bg-light-blue text-blue-600': column.title === 'To Do',
                  'bg-orange-100 text-orange-600': column.title === 'In Progress',
                  'bg-green-100 text-green-600': column.title === 'Done',
                  'bg-gray-100 text-gray-600': !['To Do', 'In Progress', 'Done'].includes(column.title)
                }"
              >
                {{ taskCounts[column.title.toLowerCase().replace(/\s+/g, '')] || 0 }}
              </span>
            </div>
            <button 
                class="btn-primary"
              @click="handleSprintAction"
            >
                {{ epicSelected.status === 'On Sprint' ? 'Complete Sprint' : 'Start Sprint' }}
            </button>
          </div>
        </div>

        <div v-if="epics.length > 0" class="text-sm text-gray-500 flex w-full justify-between mb-6">
          <p>{{ epicSelected.status === 'On Sprint' ? formatSprintDates : '' }}</p>
          <div class="flex flex-row gap-2">
            <button v-if="selectedTaskToUpdate.length > 1" @click="isMoveModalOpen = true" class="btn-cancel">
              <Replace class="w-5 h-5" />
              Move to Epic
            </button>
          <button v-if="showTrashButton" @click="isDeleteModalOpen = true" class="btn-cancel text-error">
            <Trash class="w-5 h-5" />
            Delete Selected ({{ selectedTaskToUpdate.length }})
          </button>
          </div>
                </div>

        <BacklogContainer 
          v-if="epics.length > 0"
          :epicSelected="epicSelected"
          :selectedTaskToUpdate="selectedTaskToUpdate"
          :taskCounts="taskCounts"
          :epics="epics"
          @delModalOpen="isDeleteModalOpen = $event"
          @editTask="handleEditTask"
          @move-task="handleMoveTask"
        />
        
        <div v-else-if="epics.length == 0" class="flex flex-col gap-4 justify-center items-center mt-6">
          <img :src="noDataIllustration" class="w-20 h-20">
          <p>Try Create some epic to get started</p>
        </div>
      </div>
    </div>
  </div>
</template>
