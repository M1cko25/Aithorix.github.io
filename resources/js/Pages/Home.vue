<script setup>
import Header from '../Components/Header.vue'
import SideBar from '../Components/SideBar.vue'
import { ref, computed } from 'vue'
import { MoreVertical, Plus } from 'lucide-vue-next'
import TextField from '../Components/TextField.vue'

const d = new Date();
const today = d.toLocaleDateString('en-US', { 
    month: 'short',
    day: 'numeric',
    year: 'numeric'
}).replace(',', '.');
const currentDate = ref(today)

const props = defineProps({
  stats: {
    type: Object,
    required: true
  },
  tasks: {
    type: Array,
    required: true
  },
  projectCards: {
    type: Array,
    required: true
  },
  meetings: {
    type: Array,
    required: true
  }
});

const formattedStats = computed(() => [
  { label: 'Total Projects', value: props.stats.totalProjects },
  { label: 'Total Tasks', value: props.stats.totalTasks },
  { label: 'Assigned Tasks', value: props.stats.assignedTasks },
  { label: 'Completed Tasks', value: props.stats.completedTasks }
]);

const myWorkTabs = ref(['Upcoming', 'Overdue', 'Completed'])
const activeMyWorkTab = ref('Upcoming')
const isSidebarOpen = ref(true);
const logoDisplayed = ref(true);
</script>

<template>
  <Header :logoDisplay="logoDisplayed"/>
  <SideBar @sidebarCollapsed="(value) => { isSidebarOpen = value }" @logoAppear="(value) => logoDisplayed = value" />
  
  <div class="pt-16 p-6 transition-all duration-300 ease-in-out" :class="`${isSidebarOpen ? 'ml-16' : 'ml-64'}`">
    <div class="my-8">
      <div class="flex flex-row items-center justify-between">
        <h1 class="text-3xl font-bold">HOME</h1>
        <TextField type="search" placeholder="Search Anything" />
      </div>
      <p class="text-gray-600">{{ currentDate }}</p>
      <h2 class="text-2xl font-bold mt-4">Good Morning, {{ $page.props.auth.user.name }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div
        v-for="stat in formattedStats"
        :key="stat.label"
        class="bg-white rounded-xl p-6 shadow-sm"
      >
        <h3 class="text-gray-600 mb-2">{{ stat.label }}</h3>
        <p class="text-3xl font-bold">{{ stat.value }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold">My Work</h3>
          <button class="p-2 hover:bg-gray-100 rounded-lg">
            <MoreVertical class="w-5 h-5" />
          </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b mb-6">
          <button
            v-for="tab in myWorkTabs"
            :key="tab"
            @click="activeMyWorkTab = tab"
            class="px-4 py-2 -mb-px"
            :class="activeMyWorkTab === tab ? 'border-b-2 border-blue-500 font-medium' : 'text-gray-500'"
          >
            {{ tab }}
          </button>
        </div>
        <div class="space-y-4">
          <div
            v-for="task in tasks"
            :key="task.title"
            class="flex items-start gap-4 p-4 border rounded-lg"
          >
            <input type="checkbox" class="mt-1" />
            <div>
              <h4 class="font-medium">{{ task.title }}</h4>
              <p class="text-sm text-gray-500">{{ task.project }} - {{ task.dateRange }}</p>
            </div>
          </div>
          <div v-if="tasks.length === 0" class="text-center text-gray-500 py-4">
            No tasks found
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold">Projects</h3>
          <button class="p-2 hover:bg-gray-100 rounded-lg">
            <Plus class="w-5 h-5" />
          </button>
        </div>

        <div class="space-y-4">
          <div
            v-for="project in projectCards"
            :key="project.name"
            class="flex items-center justify-between p-4 border rounded-lg"
          >
            <div>
              <h4 class="font-medium">{{ project.name }}</h4>
              <span :class="['px-2 py-1 rounded-full text-sm mt-2 inline-block', project.tagColor]">
                {{ project.tag }}
              </span>
            </div>
            <div class="flex -space-x-2">
              <template v-if="project.members.length > 0">
                <template v-for="(member, index) in project.members.slice(0, 3)" :key="member.id">
                  <img 
                    v-if="member.avatar" 
                    :src="member.avatar" 
                    :alt="member.name"
                    class="w-8 h-8 rounded-full border-2 border-white"
                  />
                  <div 
                    v-else 
                    class="w-8 h-8 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-sm font-medium text-blue-800"
                  >
                    {{ member.name.charAt(0).toUpperCase() }}
                  </div>
                </template>
                <span 
                  v-if="project.members.length > 3" 
                  class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600 border-2 border-white"
                >
                  +{{ project.members.length - 3 }}
                </span>
              </template>
              <span v-else class="text-sm text-gray-500">No members yet</span>
            </div>
          </div>
          <div v-if="projectCards.length === 0" class="text-center text-gray-500 py-4">
            No projects found
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold">Upcoming Meetings</h3>
          <button class="p-2 hover:bg-gray-100 rounded-lg">
            <MoreVertical class="w-5 h-5" />
          </button>
        </div>

        <div class="space-y-4">
          <div
            v-for="meeting in meetings"
            :key="meeting.title"
            class="flex items-center gap-4 p-4 border rounded-lg"
          >
            <img :src="meeting.icon" class="w-10 h-10 rounded-lg" />
            <div>
              <h4 class="font-medium">{{ meeting.title }}</h4>
              <p class="text-sm text-gray-500">{{ meeting.time }}</p>
            </div>
          </div>
          <div v-if="meetings.length === 0" class="text-center text-gray-500 py-4">
            No upcoming meetings
          </div>
        </div>
      </div>
    </div>
  </div>
</template>