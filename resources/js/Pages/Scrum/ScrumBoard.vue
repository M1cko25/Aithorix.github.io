<script setup>
import Sidebar from '../../Components/Sidebar.vue'
import Header from '../../Components/Header.vue'
import { ref } from 'vue'
import { Search, Users, Filter, ArrowUpDown, Video, Star, Share2 } from 'lucide-vue-next'
import Button from '../../Components/Button.vue'
import KanbanColumn from '../../Components/KanbanColumn.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage().props;
const searchQuery = ref('')
const columns = ref([
  { id: 'todo', title: 'To Do', tasks: [] },
  { id: 'progress', title: 'In Progress', tasks: [] },
  { id: 'done', title: 'Done', tasks: [] }
])

const teamMembers = ref([
  { id: 1, avatar: '/placeholder.svg?height=32&width=32' },
  { id: 2, avatar: '/placeholder.svg?height=32&width=32' },
  { id: 3, avatar: '/placeholder.svg?height=32&width=32' }
])

</script>
<template>
    <Head title="Scrum Board" />
  <div class="min-h-screen bg-gray-50">
    <Sidebar/>
    <Header />
    <div class="ml-64 pt-16">
    <!-- Board Header -->
    <div class="p-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <h1 class="text-2xl font-bold">{{ page.projectName }}<span class="text-xl font-normal"> > Board</span></h1>
        <div class="flex items-center -space-x-2">
          <!-- <img 
            v-for="member in teamMembers" 
            :key="member.id"
            :src="member.avatar"
            class="w-8 h-8 rounded-full border-2 border-white"
          />
          <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600">+3</span> -->
        </div>
        <button class="p-2 text-gray-600 hover:text-gray-800">
          <Users class="w-5 h-5" />
        </button>
      </div>

      <div class="flex items-center gap-4">
        <button><Share2/></button>
        <button><Star/></button>
        <Button text="Create Meeting" variant="primary" :style="`flex px-4 py-2 items-center gap-2`">
          <Video/>
        </Button>
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
    <div class="px-6 pb-6 flex gap-6">
      <KanbanColumn 
        v-for="column in columns"
        :key="column.id"
        :title="column.title"
        :tasks="column.tasks"
      />
      <button class="w-80 h-12 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 flex items-center justify-center text-gray-600 hover:text-gray-800">
        Add Column
      </button>
    </div>
  </div>
  </div>
</template>