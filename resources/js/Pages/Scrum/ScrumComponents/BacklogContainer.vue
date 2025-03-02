<script setup>
import { ref, watch, computed } from 'vue'
import { updateTaskStatus, createTask, deleteTask } from '../ScrumServices/taskApi';
import { Edit2, MoreHorizontal, ClipboardList, Bookmark, Bug } from 'lucide-vue-next'
import draggable from "vuedraggable";
import { usePage } from '@inertiajs/vue3';
import Modal from '../../../Components/Modal.vue';
import Overlay from '../../../Components/Overlay.vue';

const page = usePage().props;
const props = defineProps({
    epicSelected: {
        type: Object,
        required: true
    },
    selectedTaskToUpdate: {
        type: Array,
        required: true
    },
    taskCounts: {
        type: Object,
        required: true
    },
    epics: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['updateCounts', 'delModalOpen', 'updateTaskToUpdate'])

const isOpen = ref(false);
const newTask = ref('');
const isCreatingTask = ref(false);
const statusOptions = ['To Do', 'In Progress', 'Done']
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

const overlayPosition = ref({ x: 0, y: 0 })
const selectedType = ref(taskTypes.value[0])
const selectType = (type) => {
  selectedType.value = type
  isOpen.value = false
}
const isOverlayOpen = ref(false);
const drag = ref(false)

const handleTaskSelection = (event, task) => {
  const selectedTasks = [...props.selectedTaskToUpdate]
  
  if (event.target.checked) {
    if (!selectedTasks.some(t => t.id === task.id)) {
      selectedTasks.push(task)
    }
  } else {
    const index = selectedTasks.findIndex(t => t.id === task.id)
    if (index > -1) {
      selectedTasks.splice(index, 1)
    }
  }
  
  // Clear and repopulate the array to ensure reactivity
  props.selectedTaskToUpdate.splice(0, props.selectedTaskToUpdate.length)
  selectedTasks.forEach(task => props.selectedTaskToUpdate.push(task))
}

const TaskOverlayButtons = ref([
  {
    text: 'Edit Task',
    function: () => {
      isOverlayOpen.value = false
    }
  },
  {
    text: 'Delete Task',
    function: () => {
      emit('delModalOpen', true)
      isOverlayOpen.value = false
    }
  },
  {
    text: 'Open Task',
    function: () => {   
      isOverlayOpen.value = false
    }
  }
])
</script>
<template>
    <Overlay
    :isOpen="isOverlayOpen" 
    :buttons="TaskOverlayButtons"
    :position="overlayPosition"
    @close="isOverlayOpen = false"
  />
    <div>
        <draggable
        v-model="props.epicSelected.tasks" 
        class="space-y-2"
        item-key="id"
        :group="{ name: 'tasks' }"
        @start="drag=true" 
        @end="drag=false"
        >
        <template #item="{ element: task }">
            <div
            class="flex cursor-grab items-center gap-4 p-4 border rounded-lg hover:bg-gray-50 cursor-move"
            >
            <input 
            type="checkbox" 
            class="w-5 h-5 rounded border-gray-300"
            @change="(event) => handleTaskSelection(event, task)"
            :checked="props.selectedTaskToUpdate.some(t => t.id === task.id)"
            />
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
                @change="updateTaskStatus(task, props.taskCounts, props.epicSelected, page.projectDetails)">
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
            <button class="p-2 hover:bg-gray-100 rounded" @click="(event) => {
                isOverlayOpen = true
                const rect = event.currentTarget.getBoundingClientRect()
                overlayPosition = {
                    x: rect.x - 270,
                    y: rect.y + rect.height,
                }
                event.stopPropagation()
                props.selectedTaskToUpdate.splice(0)
                props.selectedTaskToUpdate.push(task)
                }">
                <MoreHorizontal class="w-5 h-5" />
            </button>
            </div>
        </template>
        </draggable>
        <div v-if="isCreatingTask" class="flex flex-row gap-4 mt-4 relative">
        <div class="relative z-20">
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
        <div class="relative z-20 flex w-full flex-row">
            <input type="text" v-model="newTask" placeholder="Add new task" class="w-full px-4 py-2 outline-none" />
            <button @click="()=> {
                createTask(newTask, statusOptions, props.taskCounts,
                selectedType, props.epics, props.epicSelected, 
                page.projectDetails)
                newTask = '';
                isCreatingTask = false;
            }" class="btn-primary">Create</button>
        </div>
        <div class="fixed inset-0 z-10" @click="isCreatingTask = false"></div>
        </div>
        <button v-if="!isCreatingTask && props.epics.length > 0" @click="isCreatingTask = true" class="btn-cancel w-full mt-6 ">Create backlog</button>
    </div>
</template>