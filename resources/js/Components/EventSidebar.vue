<script setup>
import { ref, watch } from 'vue'
import { 
  Home, 
  Briefcase, 
  Star,
  LayoutDashboard,
  Kanban,
  ChartGantt,
  Logs,
  Rocket,
  CalendarDays,
  Search,
  Filter,
  ChevronDown,
  Wallet,
  Users,
  Calendar
} from 'lucide-vue-next'
import { usePage, Link } from '@inertiajs/vue3'

// Add this after your existing props definition
const page = usePage()
const props = defineProps({
  projectItems: {
    type: Array,
    default: [
      { id: 'dashboard', icon: LayoutDashboard, text: 'Dashboard', path: '/eventplanning/eventdashboard?id=', active: false },
      { id: 'schedule', icon: Calendar, text: 'Schedule', path: '/eventplanning/schedule?id=', active: false },
      { id: 'vendor', icon: Users, text: 'Vendor', path: '/eventplanning/vendor?id=', active: false },
      { id: 'budget', icon: Wallet, text: 'Budget Overview', path: '/eventplanning/budgetoverview?id=', active: false },
      { id: 'upgrade', icon: Rocket, text: 'Upgrade Plan', path: '/upgrade', active: false },
    ]
  }
})

const searchQuery = ref('')

const menuItems = ref([
  { icon: Home, text: 'Home', path: '/home', isActive: true },
  { icon: Briefcase, text: 'My Work', path: '/work', isActive: false },
  { icon: Star, text: 'Starred', path: '/starred', isActive: false },
])

const activeStates = ref(new Map())

const isItemActive = (projectId, itemPath) => {
  const key = `${projectId}-${itemPath}`
  return activeStates.value.get(key) || false
}

watch(
  () => page.url,
  (newUrl) => {
    if (page.props.projects?.project?.length) {
      page.props.projects.project.forEach(project => {
        props.projectItems.forEach(item => {
          const key = `${project.id}-${item.path}`
          const isActive = newUrl === item.path + project.id || newUrl.includes(item.path + project.id)
          activeStates.value.set(key, isActive)
        })
      })
    }
    menuItems.value.forEach(item => {
        if (item.path == newUrl) {
          item.isActive = true
        } else {
          item.isActive = false
        }
      })
  },
  { immediate: true }
)
const projectStates = ref(new Map())

const toggleDown = (projectId) => {
  projectStates.value.set(projectId, !isProjectOpen(projectId))
}

const isProjectOpen = (projectId) => {
  const toggleState = projectStates.value.get(projectId)
  if (toggleState !== undefined) {
    return toggleState
  }
  return page.url.includes(projectId)
}
watch(
  () => page.props.projects,
  (newProjects) => {
    if (newProjects?.project) {
      newProjects.project.forEach(project => {
        projectStates.value.set(project.id, page.url.includes(project.id))
      })
    }
  },
  { immediate: true, deep: true }
)

const navigationItems = [
  { 
    name: 'Schedule', 
    path: '/event-planning/schedule',
    icon: 'Calendar' // You'll need to import this icon
  },
  { 
    name: 'Budget', 
    path: '/event-planning/budget',
    icon: 'DollarSign' // You'll need to import this icon
  },
  { 
    name: 'Vendors', 
    path: '/event-planning/vendors',
    icon: 'Users' // You'll need to import this icon
  }
  // Add other navigation items as needed
]
</script>

<template>
  <aside class="md:w-64 absolute md:left-0 -left-full bg-light border-r h-screen fixed top-0">
    <!-- Search Section -->
    <div class="p-4">
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search"
          class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
        />
        <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
        <Filter class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" />
      </div>
    </div>

    <!-- Main Navigation -->
    <nav class="px-2">
      <ul class="space-y-1">
        <li v-for="item in menuItems" :key="item.text">
          <Link 
            :href="item.path" 
            class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors duration-200 font-medium"
            :class="item.isActive ? 'bg-blue text-light hover:bg-button' : ''">
          >
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.text }}
          </Link>
        </li>
      </ul>
    </nav>

    <!-- Project Section -->
    <div class="mt-6">
      <div class="px-4 py-2 border-t border-b border-gray-200 mx-2 font-medium text-gray-600">
        Projects
      </div>  
      <div v-for="project in page.props.projects.project" :key="project.id" class="w-full my-2">
        <button 
          @click="toggleDown(project.id)" 
          class="flex w-full flex-row px-5 py-2 justify-between items-center hover:bg-gray-50 rounded-lg transition-colors duration-200"
        >
          <p class="text-base font-medium">{{ project.name }}</p>
          <ChevronDown 
            :class="{ 
              'transform rotate-180 transition-transform duration-300': projectStates.get(project.id),
              'transition-transform duration-300': !projectStates.get(project.id)
            }"
            class="w-5 h-5 text-gray-500"
          />
        </button>
        <Transition name="list">
          <ul v-show="isProjectOpen(project.id)" class="space-y-1 px-2 mt-1">
            <li v-for="item in props.projectItems" :key="item.text">
              <Link
                :href="item.path + project.id"
                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors duration-200"
                :class="isItemActive(project.id, item.path) ? 'bg-blue text-light hover:bg-button-hover' 
              : 'text-dark'">
                <component :is="item.icon" class="w-5 h-5" />
                {{ item.text }}
              </Link>
            </li>
          </ul>
        </Transition>
      </div>
    </div>

    <!-- Meeting Summaries -->
    <div class="mt-6 px-4 pb-6">
      <a 
        href="/meetings" 
        class="flex items-center gap-3 px-4 py-2.5 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium"
      >
        <CalendarDays class="w-5 h-5" />
        Meeting Summaries
      </a>
    </div>
  </aside>
</template>

<style>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
  max-height: 300px; /* Adjust based on your content height */
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
}
</style>