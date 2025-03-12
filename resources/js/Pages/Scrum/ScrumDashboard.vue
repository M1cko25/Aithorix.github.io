<script setup>
import Header from '@/Components/Header.vue'
import Sidebar from '../../Components/SideBar.vue'
import { ref } from 'vue'
import { Calendar, CheckSquare, Video, ClipboardList, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import 'vue-cal/dist/vuecal.css';
import VueApexCharts from 'vue3-apexcharts';
import { usePage } from '@inertiajs/vue3';
import graphics from '../../graphics';
import Lira from '@/Components/Lira.vue';

const page = usePage().props;

const stats = ref([
  { 
    icon: ClipboardList,
    label: 'Pending Backlogs',
    value: page.toDoBacklogs + page.progressBacklogs,
    period: ''
  },
  {
    icon: CheckSquare,
    label: 'Backlog Completed',
    value: page.completedBacklogs,
    period: 'at the last 7 days'
  },
  {
    icon: Calendar,
    label: 'Backlog Created',
    value: page.backlogCreated,
    period: 'in the last 7 days'
  },
  {
    icon: Video,
    label: 'Meeting Created',
    value: page.meetingCreated,
    period: 'at the last 7 days'
  }
])

const activities = page.activities;
const isLiraOpen = ref(false);

const chartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: ['To Do', 'In Progress', 'Done'],
  colors: ['#E4080A', '#30FAFA', '#7DDA58'],
  legend: {
    position: 'right',
    horizontalAlign: 'center',
  },
});

const totalIssues = ref(page.toDoBacklogs + page.progressBacklogs + page.completedBacklogs);
// Chart Series (Values)
const chartSeries = ref([page.toDoBacklogs, page.progressBacklogs, page.completedBacklogs]);
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
            return val.split('\n')
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
  labels: [page.onTime + page.late + "\n/" + page.totalMembers],
})

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: '2-digit',
    day: '2-digit',
    year: 'numeric'
  });
}


const meetingDates = ref(page.meetings.map((meeting) => meeting.date))
const isSidebarOpen = ref(true);
const logoDisplayed = ref(true);
</script>

<template>
  <Header :logoDisplay="logoDisplayed"/>
  <Sidebar @sidebarCollapsed="(value) => { isSidebarOpen = value }" @logoAppear="(value) => logoDisplayed = value"/>
  <div class="md:pt-16 md:p-6 p-2 transition-all duration-300 ease-in-out"  :class="`${isSidebarOpen ? 'ml-16' : 'ml-64'}`" id="content">
    <div class="py-6 flex items-center">
        <h1 class="text-2xl font-bold ">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Dashboard</span></h1>
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
      <div class="flex md:flex-row flex-col gap-6">
        <!-- Recent Activities -->
      <div v-if="activities.length > 0" class="w-full h-80 bg-light rounded-xl p-6 shadow-sm">
        <h2 class="font-semibold mb-4 text-2xl">Recent Activities</h2>
        <p class="text-sm text-gray-500 mb-6">View all the activities that is made in the project</p>
        
        <div class="space-y-6 overflow-y-scroll h-48">
          <div v-for="(activity, index) in activities" :key="index" class="flex gap-4">
            <img v-if="activity.user.avatar" :src="activity.user.avatar" :alt="activity.user.name" class="w-8 h-8 rounded-full" />
            <div v-else class="w-8 h-8 rounded-full bg-blue flex items-center justify-center lg:text-sm text-xs text-light">
              <p>{{ activity.user.name.slice(0,2).toUpperCase() }}</p>
            </div>
            <div>
              <p class="text-sm">
                <span class="font-medium">{{ activity.user.name }}</span>
                {{ activity.action }}
                <span>{{ activity.description }}</span>
                in
                <span class="text-blue">{{ activity.update }}</span>
              </p>
              <p class="text-xs text-gray-500 mt-1">{{ activity.date }}</p>
            </div>
          </div>
        </div>
      </div>
        <!-- Status Overview -->
        <div class="bg-light w-full rounded-xl p-6 shadow-sm">
          <div v-if="totalIssues > 0">
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
                  <p class="text-xl font-bold">{{ totalIssues }}</p>
                <p class="text-sm">Total Issues</p>
              </div>
            </div>
          </div>
          <div v-else class="flex flex-col justify-center h-full items-center gap-5">
            <img :src="graphics.noDataIllustration" class="w-20 h-20">
            <h1 class="font-bold">No activity yet</h1>
            <p>Try creating few backlogs</p>
          </div>
        </div>
      </div>

      <!-- Meeting Participation -->
      <div v-if="meetingDates.length > 0" class="flex flex-row gap-6">
        <div class="bg-light w-full rounded-xl p-6 shadow-sm">
          <h2 class="font-semibold text-xl mb-4">Meeting Participation</h2>
          <p class="text-sm text-gray-500 mb-6">View all members participation in meetings</p>
          
          <div class="flex justify-between items-center mb-6">
            <select
            class="border rounded-lg px-3 py-2">
              <option v-for="date in meetingDates" :key="date">{{ formatDate(date) }}</option>
            </select>
            <!-- <select class="border rounded-lg px-3 py-2">
              <option v-for="times in meetingTimes" :key="times">{{ times }}</option>
            </select> -->
          </div>

          <!-- Radial Chart Placeholder -->
            <div class="relative flex flex-row items-center justify-center">
              <VueApexCharts 
                :options="radialOptions"
                :series="radialSeries"
                height="180"
              />
              <ul class="list-disc">
                  <li>{{ page.onTime }} on time</li>
                  <li>{{ page.late }} late</li>
                  <li>{{ page.absent }} absent</li>
              </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
  <Lira :isOpen="isLiraOpen" @update:isOpen="isLiraOpen = $event" />
</template>