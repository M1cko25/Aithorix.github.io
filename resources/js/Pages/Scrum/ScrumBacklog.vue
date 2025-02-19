<script setup>
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref } from 'vue'
import { Filter, ArrowUpDown, MoreHorizontal, ChevronRight, 
  Edit2, User, ClipboardList, Bookmark, Bug  } from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3';
import draggable from "vuedraggable";
import TextField from '../../Components/TextField.vue'
import axios from 'axios';

const page = usePage().props;

const searchQuery = ref('')
const newEpic = ref('')

const projEpics = ref(page.epics)
const epics = ref([])
projEpics.value.sort((a, b) => a.order - b.order).forEach(epic => {
    epics.value.push({
      epic_id: epic.id,
      name: epic.name,
      isActive: epic.order === 1 ? true : false,
      description: epic.description,
      key: epic.key,
      order: epic.order,
      progress: epic.progress_percent + '%',
      tasks: page.backlogs.filter(backlog => backlog.epic_id === epic.id) || []
    })
  })

const epicSelected = ref(epics.value[0])

const statusOptions = ['To Do', 'In Progress', 'Done']

const taskCounts = ref({
  todo: epicSelected.value.tasks.filter(task => task.status === 'To Do').length,
  inProgress: epicSelected.value.tasks.filter(task => task.status === 'In Progress').length,
  completed: epicSelected.value.tasks.filter(task => task.status === 'Done').length
})

const isCreatingEpic = ref(false);
const isCreatingTask = ref(false);

const selectEpic = (selectedEpic) => {
  epics.value.forEach(epic => {
    epic.isActive = epic === selectedEpic;
  });
  epicSelected.value = selectedEpic;
  
  // Update counts
  taskCounts.value = {
    todo: selectedEpic.tasks.filter(task => task.status === 'To Do').length,
    inProgress: selectedEpic.tasks.filter(task => task.status === 'In Progress').length,
    completed: selectedEpic.tasks.filter(task => task.status === 'Done').length
  }
}

const createEpic = ()=> {
  isCreatingEpic.value = !isCreatingEpic.value;
}

const createNewEpic = () => {
  if (newEpic.value.trim().length > 0) {
    axios.post('/scrum/epic-create', {
      name: newEpic.value,
      key: page.projectDetails.key + '-E' + (projEpics.value.length + 1),
      projectId: page.projectDetails.id
    })
    .then(response => {
      epics.value.push({ name: newEpic.value, isActive: false });
    })
    .catch(error => {
      console.error('Error making epic:', error);
    });
    newEpic.value = '';
    isCreatingEpic.value = false;   
  } else {
    isCreatingEpic.value = false;
  }
}

const taskTypes = ref([{
  name: 'Task',
  icon: ClipboardList
},{
  name: 'Bug',
  icon: Bug
},{
  name: 'Story',
  icon: Bookmark
}])

const newTask = ref('');

const isOpen = ref(false)
const selectedType = ref(taskTypes.value[0])

const selectType = (type) => {
  selectedType.value = type
  isOpen.value = false
}

const createTask = () => {
  if (newTask.value.trim().length > 0) {
    const activeEpic = epics.value.find(epic => epic.isActive);
    axios.post('/scrum/backlog-create', {
      title: newTask.value,
      description: '',
      priority: 'Low',
      status: statusOptions[0],
      type: selectedType.value.name,
      epicId: activeEpic.epic_id,
      order: activeEpic.order + 1,
      projectId: page.projectDetails.id
    })
    .then(response => {
      epics.value[activeEpic.order - 1].tasks.push({
        id: response.data.id,
        title: newTask.value,
        description: '',
        priority: 'Low',
        type: selectedType.value.name,
        status: statusOptions[0],
        assignees: [],
        epic_id: activeEpic.epic_id
      });
      newTask.value = '';
    })
    .catch(error => {
      console.error('Error making task:', error);
    });
    isCreatingTask.value = false;
  }
}

const handleDragEnd = () => {
  const updatedEpics = epics.value.map((epic, index) => ({
    ...epic,
    order: index + 1
  }));
  axios.post('/scrum/epics-reorder', {
    epics: updatedEpics
  })
  .then(response => {
    epics.value = updatedEpics;
  })
  .catch(error => {
    console.error('Error updating epic order:', error);
  });
}
</script>

<template>
  <Head title="| Backlog" />
  <Header @click="isCreatingEpic = false; isCreatingTask = false"/>
  <Sidebar @click="isCreatingEpic = false; isCreatingTask = false"/>
  <div @click.self="isCreatingEpic = false; isCreatingTask = false" class="ml-64 pt-16 p-6">
    <div @click="isCreatingEpic = false; isCreatingTask = false" class="flex items-center justify-between">
      <div class="py-6 flex items-center">
        <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Backlog</span></h1>
      </div>
      <div class="flex items-center gap-2">
      </div>
    </div>

    <!-- Controls -->
    <div @click="isCreatingEpic = false; isCreatingTask = false" class="flex gap-4 mb-6">
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
      <!-- Epic List -->
      <div @click.self="isCreatingEpic = false; isCreatingTask = false" class="w-64 bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-xl font-semibold mb-4">Epic</h2>
        
        <div class="space-y-2">
          <draggable 
            v-model="epics" 
            class="space-y-2"
            item-key="name"
            :group="{ name: 'epics' }"
            @start="drag=true"
            @end="handleDragEnd"
          >
          <template #item="{ element: epic }">
            <button @click="selectEpic(epic)"
              class="w-full cursor-grab px-4 py-2 rounded-lg text-left flex items-center justify-between cursor-move"
              :class="epic.isActive ? 'bg-button text-light hover:bg-button-hover' : 'text-gray-700 hover:bg-gray-100'"
            >
              {{ epic.name }}
              <ChevronRight v-if="epic.isActive" class="w-5 h-5" />
            </button>
          </template>
          </draggable>
          <div v-if="isCreatingEpic" class="mt-4" :ref="epicInputRef">
            <input v-model="newEpic" type="text" placeholder="Set new milestone" class="w-full px-4 py-2 outline-none" />
            <button @click="createNewEpic" class="btn-cancel mt-2 w-full">Create</button>
          </div>
        </div>

        <button v-if="!isCreatingEpic" @click="createEpic" class="btn-cancel w-full mt-4 gap-4">
          <span class="text-xl">+</span> Create Epic
        </button>
      </div>

      <!-- Task List -->
      <div @click="isCreatingEpic = false" class="flex-1 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-4">
            <h2 class="text-xl font-semibold">{{ epicSelected.name }}</h2>
            <div class="text-sm text-gray-500">{{ epicSelected.tasks.length }} {{epicSelected.tasks.length > 1 ? 'backlogs' : 'backlog'}}</div>
            <button class="p-1 hover:bg-gray-100 rounded">
              <Edit2 class="w-4 h-4" />
            </button>
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 bg-light-blue text-blue-600 rounded-full">{{ taskCounts.todo }}</span>
              <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full">{{ taskCounts.inProgress }}</span>
              <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full">{{ taskCounts.completed }}</span>
            </div>
            <button class="btn-primary">
              Complete Sprint
            </button>
            <button><MoreHorizontal/></button>
          </div>
        </div>

        <div @click="isCreatingEpic = false" class="text-sm text-gray-500 mb-6">
          Dec. 1, 2024 - Dec. 14, 2024
        </div>

        <!-- Tasks -->
        <div>
          <draggable 
            v-model="epicSelected.tasks" 
            class="space-y-2"
            item-key="id"
            :group="{ name: 'epicSelected.tasks' }"
            @start="drag=true" 
            @end="drag=false"
          >
            <template #item="{ element: task }">
              <div
                class="flex cursor-grab items-center gap-4 p-4 border rounded-lg hover:bg-gray-50 cursor-move"
              >
                <input type="checkbox" class="w-5 h-5 rounded border-gray-300" />
                
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <ClipboardList v-if="task.type == 'Task'" class="w-5 h-5" />
                    <Bug v-else-if="task.type == 'Bug'" class="w-5 h-5" />
                    <Bookmark v-else-if="task.type == 'Story'" class="w-5 h-5" />
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

                <div class="w-20">
                  <div class="flex -space-x-2">
                    <img 
                      v-for="(assignee, index) in task.assignees"
                      :key="index"
                      :src="assignee"
                      class="w-8 h-8 rounded-full border-2 border-white"
                    />
                  </div>
                </div>

                <button class="p-2 hover:bg-gray-100 rounded">
                  <MoreHorizontal class="w-5 h-5" />
                </button>
              </div>
            </template>
          </draggable>
          <div v-if="isCreatingTask" class="flex flex-row gap-4 mt-4">
            <div class="relative">
              <button @click="isOpen = !isOpen" class=" flex items-center gap-2 px-4 py-2 border rounded-lg">
                <component :is="selectedType.icon" class="w-5 h-5" />
                <span>{{ selectedType.name }}</span>
              </button>

              <div v-if="isOpen" class="absolute z-10 mt-1 bg-white border rounded-lg shadow-lg">
                <button 
                  v-for="type in taskTypes" 
                  :key="type.name"
                  @click="selectType(type)"
                  class="flex flex-row w-fit items-center gap-2 px-4 py-2 hover:bg-gray-50"
                >
                  <component :is="type.icon" class="w-5 h-5" />
                  <span>{{ type.name }}</span>
                </button>
              </div>
            </div>
            <input type="text" v-model="newTask" placeholder="Add new task" class="w-full px-4 py-2 outline-none" />
            <button @click="createTask" class="btn-primary">Create</button>
          </div>
          <button v-if="!isCreatingTask" @click="()=>{isCreatingTask = true}" class="btn-cancel w-full mt-6 ">Create backlog</button>
        </div>
      </div>
    </div>
  </div>
</template>