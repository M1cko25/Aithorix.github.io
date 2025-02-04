<script setup>
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref } from 'vue'
import { Calendar, CheckSquare, Video, ClipboardList, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const stats = ref([
  { 
    icon: ClipboardList,
    label: 'Task Created',
    value: 12,
    period: 'at the last 7 days'
  },
  {
    icon: CheckSquare,
    label: 'Task Completed',
    value: 5,
    period: 'at the last 7 days'
  },
  {
    icon: Video,
    label: 'Meeting Created',
    value: 4,
    period: 'at the last 7 days'
  },
  {
    icon: Calendar,
    label: 'Task Due',
    value: 3,
    period: 'in the last 7 days'
  }
])

const activities = ref([
  {
    user: { name: 'Mico Jake', avatar: '/placeholder.svg?height=32&width=32' },
    action: 'created a new task named',
    task: 'Task 1',
    destination: 'To Do',
    time: '1 day ago'
  },
  {
    user: { name: 'Mico Jake', avatar: '/placeholder.svg?height=32&width=32' },
    action: 'created a new task named',
    task: 'Task 2',
    destination: 'To Do',
    time: '1 day ago'
  },
  {
    user: { name: 'Andrei Odango', avatar: '/placeholder.svg?height=32&width=32' },
    action: 'change the status of',
    task: 'Task 1',
    destination: 'In Progress',
    time: '1 day ago'
  }
])
let currentDate = ref(24)

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

const d = new Date();
let currentMonth = months[d.getMonth()];
const calendar = ref({
  month: currentMonth,
  days: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
  dates: Array.from({ length: 31 }, (_, i) => i + 1)
})

const meetingStats = ref({
  total: 12,
  onTime: 5,
  late: 4,
  absent: 3
})

const statusOverview = ref({
  total: 24,
  todo: 12,
  inProgress: 7,
  done: 5
})

const selectDate = (dateSelected)=>{
    // currentDate = dateSelected;
    let date = new Date(d.getFullYear(), d.getMonth(), 29).getDate();
console.log(date);
}
</script>

<template>
  <Header />
  <Sidebar />
  <div class="ml-64 pt-16 p-6">
    <div class="p-12 flex items-center">
        <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div 
        v-for="stat in stats" 
        :key="stat.label"
        class="bg-white rounded-xl p-6 shadow-sm"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-gray-600 mb-2">{{ stat.label }}</p>
            <p class="text-3xl font-bold">{{ stat.value }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ stat.period }}</p>
          </div>
          <component 
            :is="stat.icon"
            class="w-6 h-6 text-gray-400"
          />
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Activities -->
      <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm">
        <h2 class="font-semibold mb-4">Recent Activities</h2>
        <p class="text-sm text-gray-500 mb-6">View all the activities that is made in the project</p>
        
        <div class="space-y-6">
          <div v-for="(activity, index) in activities" :key="index" class="flex gap-4">
            <img :src="activity.avatar" :alt="activity.user.name" class="w-8 h-8 rounded-full" />
            <div>
              <p class="text-sm">
                <span class="font-medium">{{ activity.user.name }}</span>
                {{ activity.action }}
                <span class="text-blue-500">"{{ activity.task }}"</span>
                in
                <span class="text-blue-500">{{ activity.destination }}</span>
              </p>
              <p class="text-xs text-gray-500 mt-1">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Calendar -->
      <div class="bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-semibold">{{ calendar.month }}</h2>
          <div class="flex gap-2">
            <button class="p-1 hover:bg-gray-100 rounded">
              <ChevronLeft class="w-4 h-4" />
            </button>
            <button class="p-1 hover:bg-gray-100 rounded">
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center mb-2">
          <div v-for="day in calendar.days" :key="day" class="text-xs text-gray-500">
            {{ day }}
          </div>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center">
          <button 
            v-for="date in calendar.dates" 
            :key="date" @click="selectDate(date)"
            class="aspect-square flex items-center justify-center text-sm rounded-full hover:bg-gray-100"
            :class="date === currentDate ? 'bg-button text-white hover:bg-blue' : ''"
          >
            {{ date }}
          </button>
        </div>
      </div>

      <!-- Meeting Participation -->
      <div class="bg-white rounded-xl p-6 shadow-sm">
        <h2 class="font-semibold mb-4">Meeting Participation</h2>
        <p class="text-sm text-gray-500 mb-6">View all members participation in meetings</p>
        
        <div class="flex justify-between items-center mb-6">
          <select class="border rounded-lg px-3 py-2">
            <option>12/5/2024</option>
          </select>
          <select class="border rounded-lg px-3 py-2">
            <option>11am - 1pm</option>
          </select>
        </div>

        <!-- Donut Chart Placeholder -->
        <div class="relative w-48 h-48 mx-auto">
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
              <div class="text-3xl font-bold">9</div>
              <div class="text-sm text-gray-500">/12</div>
            </div>
          </div>
        </div>

        <div class="space-y-2 mt-6">
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="text-sm">5 on time</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-orange-500"></div>
            <span class="text-sm">4 late attendees</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <span class="text-sm">3 not present</span>
          </div>
        </div>
      </div>

      <!-- Status Overview -->
      <div class="bg-white rounded-xl p-6 shadow-sm">
        <h2 class="font-semibold mb-4">Status Overview</h2>
        <p class="text-sm text-gray-500 mb-6">Get the status of project's issues</p>

        <!-- Donut Chart Placeholder -->
        <div class="relative w-48 h-48 mx-auto">
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
              <div class="text-3xl font-bold">24</div>
              <div class="text-sm text-gray-500">Total Issues</div>
            </div>
          </div>
        </div>

        <div class="space-y-2 mt-6">
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="text-sm">To Do (12)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-orange-500"></div>
            <span class="text-sm">In Progress (7)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
            <span class="text-sm">Done (5)</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>