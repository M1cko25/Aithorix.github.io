<script setup>
import { ref, watch } from 'vue'
import {
  Home,
  Briefcase,
  Star,
  LayoutDashboard,
  Activity,
  Calendar,
  Logs,
  File,
  Rocket,
  CalendarDays,
  Search,
  Filter,
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  Menu
} from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
  projectItems: {
    type: Array,
    default: [
      { id: 'dashboard', icon: LayoutDashboard, text: 'Dashboard', path: '/educ/dashboard?id=', active: true },
      { id: 'course', icon: Activity, text: 'Course', path: '/educ/course-management?id=', active: false },
      { id: 'calendar', icon: Calendar, text: 'Calendar', path: '/educ/calendar-view?id=', active: false },
      { id: 'journal', icon: Logs, text: 'Journal', path: '/educ/research-journal?id=', active: false },
      { id: 'resources', icon: File, text: 'Repository', path: '/educ/repository?id=', active: false },
      { id: 'upgrade', icon: Rocket, text: 'Upgrade Plan', path: '/educ/upgrade-plan?id=', active: false },
    ]
  },
})

const isCollapsed = ref(false);
const isProjectSectionCollapsed = ref(false)
const projects = ref(page.props.projects.project)
const searchQuery = ref('')
const currentProject = ref('Scrum Project')

const menuItems = ref([
  { icon: Home, text: 'Home', path: '/home', isActive: true },
  { icon: Briefcase, text: 'My Work', path: '/work', isActive: false },
  { icon: Star, text: 'Starred', path: '/starred', isActive: false },
])

const activeStates = ref(new Map())
const projectStates = ref(new Map())

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}

const toggleProjectSection = () => {
  isProjectSectionCollapsed.value = !isProjectSectionCollapsed.value
}

const isItemActive = (projectId, itemPath) => {
  const key = `${projectId}-${itemPath}`
  return activeStates.value.get(key) || false
}

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

</script>

<template>
  <div class="relative transition-all duration-300 ease-in-out" :class="isCollapsed ? 'w-16' : 'md:w-64 w-64'">
    <button @click="toggleSidebar"
      class="absolute z-50 -right-3 top-16 bg-white border rounded-full p-1 shadow-md hover:bg-gray-50">
      <component :is="isCollapsed ? ChevronRight : ChevronLeft" class="w-4 h-4" />
    </button>
    <aside :class="[
      'fixed top-0 bottom-0 z-10 transition-all duration-300 ease-in-out bg-light border-r',
      isCollapsed ? 'w-16 pt-16' : 'md:w-64 w-64'
    ]">
      <div class="p-4" v-if="!isCollapsed">
        <div class="relative">
          <input v-model="searchQuery" type="text" placeholder="Search"
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
          <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
          <Filter class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" />
        </div>
      </div>

      <!-- Main Navigation -->
      <nav class="px-2">
        <ul class="space-y-1">
          <li v-for="item in menuItems" :key="item.text">
            <Link :href="item.path" :title="isCollapsed ? item.text : ''"
              class="flex items-center gap-3 px-4 py-2 text-gray-700 rounded-lg hover:bg-neutral"
              :class="item.isActive ? 'bg-blue text-light hover:bg-button-hover' : ''">
            <component :is="item.icon" class="w-5 h-5" />
            <span v-if="!isCollapsed">{{ item.text }}</span>
            </Link>
          </li>
        </ul>
      </nav>

      <!-- Project Section -->
      <div class="mt-6">
        <div class="px-4 mb-2 border-b border-neutral mx-2 flex items-center justify-between cursor-pointer"
          @click="toggleProjectSection">
          <span v-if="!isCollapsed">Projects</span>
          <component :is="isCollapsed ? Menu : (isProjectSectionCollapsed ? ChevronRight : ChevronDown)"
            class="w-4 h-4" />
        </div>

        <div v-show="!isProjectSectionCollapsed || isCollapsed">
          <div v-for="project in projects" :key="project.id" class="w-full my-2">
            <button @click="toggleDown(project.id)" class="flex w-full flex-row px-4 justify-between items-center"
              :title="isCollapsed ? project.name : ''">
              <p class="text-sm truncate" :class="{ 'w-full text-center': isCollapsed }">
                {{ isCollapsed ? project.name.charAt(0) : project.name }}
              </p>
              <ChevronDown v-if="!isCollapsed" class="w-4 h-4"
                :class="{ 'transform rotate-180 transition-transform duration-300': projectStates.get(project.id) }" />
            </button>

            <Transition name="list">
              <ul v-show="isProjectOpen(project.id)" class="space-y-1 px-2">
                <li v-for="item in props.projectItems" :key="item.text">
                  <Link :href="item.path + project.id" :title="isCollapsed ? item.text : ''"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg"
                    :class="isItemActive(project.id, item.path) ? 'bg-blue text-light hover:bg-button-hover' : 'text-dark'">
                  <component :is="item.icon" class="w-5 h-5" />
                  <span v-if="!isCollapsed">{{ item.text }}</span>
                  </Link>
                </li>
              </ul>
            </Transition>
          </div>
        </div>
      </div>

      <!-- Meeting Summaries -->
      <div class="mt-6 px-4">
        <a href="/meetings" class="flex items-center gap-3 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100"
          :title="isCollapsed ? 'Meeting Summaries' : ''">
          <CalendarDays class="w-5 h-5" />
          <span v-if="!isCollapsed">Meeting Summaries</span>
        </a>
      </div>
    </aside>
  </div>
  <!-- Overlay for mobile -->
  <div v-if="!isCollapsed" class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-30" @click="toggleSidebar"></div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
  max-height: 300px;
  overflow: hidden;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-10px);
}

/* Scrollbar styles */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Ensure content is scrollable */
aside {
  overflow-y: auto;
  overflow-x: hidden;
}
</style>
