<script setup>
import Header from '../Components/Header.vue'
import SideBar from '../Components/SideBar.vue'
import { ref } from 'vue'
import { MoreVertical, Plus } from 'lucide-vue-next'
import TextField from '../Components/TextField.vue'

const d = new Date();
const today = d.toLocaleDateString('en-US', { 
    month: 'short',
    day: 'numeric',
    year: 'numeric'
}).replace(',', '.');
const currentDate = ref(today)

const stats = ref([
  { label: 'Total Project', value: '1' },
  { label: 'Total Tasks', value: '3' },
  { label: 'Assigned Tasks', value: '1' },
  { label: 'Completed Tasks', value: '1' }
])

const myWorkTabs = ref(['Upcoming', 'Overdue', 'Completed'])
const activeMyWorkTab = ref('Upcoming')

const tasks = ref()

const projects = ref()
const isSidebarOpen = ref(true);
const meetings = ref()
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
        v-for="stat in stats"
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
              <p class="text-sm text-gray-500">{{ task.dateRange }}</p>
            </div>
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
            v-for="project in projects"
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
              <img
                v-for="(member, index) in project.members"
                :key="index"
                :src="member"
                class="w-8 h-8 rounded-full border-2 border-white"
              />
              <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600 border-2 border-white">
                +3
              </span>
            </div>
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
        </div>
      </div>
    </div>
  </div>
</template>