<script setup>
import Sidebar from '../../Components/Sidebar.vue'
import Header from '../../Components/Header.vue'
import { ref, computed } from 'vue'
import { Search, Users, Filter, ArrowUpDown, Video, Star, Share2 } from 'lucide-vue-next'
import Button from '../../Components/Button.vue'
import KanbanColumn from '../../Components/KanbanColumn.vue'
import { usePage, Link } from '@inertiajs/vue3'
import Lira from '../../Components/Lira.vue'

const page = usePage().props;
const isLiraOpen = ref(false);
const toDoTasks = ref([])
const inProgressTasks = ref([]);
const doneTasks = ref([]);
const searchQuery = ref('')
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
const createTask = (colId) => {
  Object.keys(taskCreating.value).forEach(key => {
    taskCreating.value[key] = false
  })
  taskCreating.value[colId] = true
}

const TaskNum = computed(()=> {
  return toDoTasks.value.length + inProgressTasks.value.length + doneTasks.value.length + 1;
})

const taskCreating = ref({
  todo: false,
  progress: false,
  done: false
})
</script>
<template>
    <Head title="| Board" />
  <div class="min-h-screen" @click.self="taskCreating = {}">
    <Sidebar/>
    <Header />
    <div class="ml-64 pt-16">
    <!-- Board Header -->
    <div class="p-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Board</span></h1>
        <div class="flex items-center -space-x-2">
        </div>
        <button class="p-2 text-gray-600 hover:text-gray-800">
          <Users class="w-5 h-5" />
        </button>
      </div>

      <div class="flex items-center gap-4">
        <button><Share2/></button>
        <button><Star/></button>
        <Link :href="route('meeting-home')">
  <Video />
</Link>
        
      </div>
    </div>

    <!-- Board Controls -->
    <div class="px-6 pb-6 flex items-center gap-4">
      <div class="relative flex-1 max-w-md">
        <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search"
          class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <Button text="Members" :icon="Users" variant="outline" />
    </div>

    <!-- Kanban Board -->
    <div class="px-6 pb-6 flex h-full gap-6">
      <KanbanColumn 
        v-for="column in columns"
        :key="column.id"
        :title="column.title"
        :tasks="column.tasks"
        :createTask="()=> {createTask(column.id)}"
        :isCreatingTask="taskCreating[column.id]"
        @create-new-task="(task) => {
          column.tasks.push(task)
          taskCreating[column.id] = false
        }"
        :taskNum="TaskNum"
      />
      <button class="w-80 h-12 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 flex items-center justify-center text-gray-600 hover:text-gray-800">
        Add Column
      </button>
    </div>
  </div>
  </div>
  <Lira :isOpen="isLiraOpen" @update:isOpen="isLiraOpen = $event" />
</template>