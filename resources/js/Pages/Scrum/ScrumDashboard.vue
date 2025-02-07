<script setup>
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref } from 'vue'
import { Calendar, CheckSquare, Video, ClipboardList, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import VueApexCharts from 'vue3-apexcharts';
import { usePage } from '@inertiajs/vue3';

const page = usePage().props;
const events = ref([
  { start: '2025-02-06 10:00', end: '2025-02-06 12:00', title: 'Meeting' },
  { start: '2025-02-07 14:00', end: '2025-02-07 15:30', title: 'Call with client' },
]);

const stats = ref([
  { 
    icon: ClipboardList,
    label: 'Pending Backlogs',
    value: page.pendingBacklogs,
    period: ''
  },
  {
    icon: CheckSquare,
    label: 'Backlog Completed',
    value: page.completedBacklogs,
    period: 'at the last 7 days'
  },
  {
    icon: Video,
    label: 'Meeting Created',
    value: page.meetingCreated,
    period: 'at the last 7 days'
  },
  {
    icon: Calendar,
    label: 'Sprints',
    value: page.sprints,
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

const chartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: ['To Do', 'In Progress', 'Done'],
  colors: ['#ff6384', '#36a2eb', '#ffce56'],
  legend: {
    position: 'right',
    horizontalAlign: 'center',
  },
});

// Chart Series (Values)
const chartSeries = ref([40, 25, 35]);
const radialSeries = ref([67])
const radialOptions = ref({
  chart: {
    type: "radialBar",
  },
  plotOptions: {
    radialBar: {
      hollow: {
        size: "50%"
      },
      dataLabels: {
        showOn: "always",
        name: {
          offsetY: 0,
          show: true,
          color: "#888",
          fontSize: "15px",
          formatter: function (val) {
            return val.split('\n')  // This helps handle the line break
          }
        },
        value: {
          color: "#111",
          fontSize: "30px",
          show: false
        }
      }
    }
  },

  stroke: {
    lineCap: "round",
  },
  labels: ["9\n/12"],
})
</script>

<template>
  <Header />
  <Sidebar />
  <div class="ml-64 pt-16 p-6">
    <div class="p-12 flex items-center">
        <h1 class="text-2xl font-bold mb-6">{{ page.projectName }}<span class="text-xl font-normal"> > Dashboard</span></h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div 
        v-for="stat in stats" 
        :key="stat.label"
        class="bg-light rounded-xl p-6 shadow-sm"
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

    <div class="flex flex-col gap-6">
      <div class="flex flex-row gap-6">
        <!-- Recent Activities -->
      <div class="w-full bg-light rounded-xl p-6 shadow-sm">
        <h2 class="font-semibold mb-4 text-2xl">Recent Activities</h2>
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
      <!-- Status Overview -->
      <div class="bg-light w-full rounded-xl p-6 shadow-sm">
          <h2 class="font-semibold text-xl mb-4">Status Overview</h2>
          <p class="text-sm text-gray-500 mb-6">Get the status of project's issues</p>

          <!-- Donut Chart Placeholder -->
          <div class="relative flex items-center justify-center">
              <VueApexCharts 
                type="donut"
                :options="chartOptions"
                :series="chartSeries"
                width="100%"
              />
              <div class="absolute rounded-full w-24 h-24 shadow-xl -translate-x-14 flex flex-col justify-center items-center">
                <p class="text-xl font-bold">24</p>
                <p class="text-sm">Total Issues</p>
              </div>
            </div>
        </div>
      </div>

      <!-- Meeting Participation -->
      <div class="flex flex-row gap-6">
        <div class="bg-light w-full rounded-xl p-6 shadow-sm">
          <h2 class="font-semibold text-xl mb-4">Meeting Participation</h2>
          <p class="text-sm text-gray-500 mb-6">View all members participation in meetings</p>
          
          <div class="flex justify-between items-center mb-6">
            <select class="border rounded-lg px-3 py-2">
              <option>12/5/2024</option>
            </select>
            <select class="border rounded-lg px-3 py-2">
              <option>11am - 1pm</option>
            </select>
          </div>

          <!-- Radial Chart Placeholder -->
            <div class="relative flex flex-row items-center justify-center">
              <VueApexCharts 
                :options="radialOptions"
                :series="radialSeries"
                height="180"
              />
              <ul class="list-disc">
                <li>3 present</li>
                <li>2 absent</li>
                <li>7 late</li>
              </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>