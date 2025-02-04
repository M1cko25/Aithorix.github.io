<script setup>
import { ref } from 'vue'
import { 
  Home, 
  Briefcase, 
  Star,
  LayoutDashboard,
  LayoutList,
  Clock,
  Package,
  Rocket,
  CalendarDays,
  Search,
  Filter
} from 'lucide-vue-next'
import { route } from '../../../vendor/tightenco/ziggy/src/js'

const props = defineProps({
  projectItems: {
    type: Array,
    default: [
    { icon: LayoutDashboard, text: 'Dashboard', path: '/scrum/dashboard', active: false },
    { icon: LayoutList, text: 'Board', path: '/scrum/board', active: true },
    { icon: Clock, text: 'Timeline', path: '/timeline', active: false },
    { icon: Package, text: 'Backlog', path: '/backlog', active: false },
    { icon: Rocket, text: 'Upgrade Plan', path: '/upgrade', active: false },
    ]
  }
})

const searchQuery = ref('')
const currentProject = ref('Scrum Project')

const menuItems = [
  { icon: Home, text: 'Home', path: '/home' },
  { icon: Briefcase, text: 'My Work', path: '/work' },
  { icon: Star, text: 'Starred', path: '/starred' },
]

const activateLink = (projItem) => {
  props.projectItems.forEach(item => {
    if(projItem == item.text) {
      item.active = true;
    } else {
      item.active = false;
    }
  });
}
</script>

<template>
  <aside class="w-64 bg-white border-r h-screen fixed left-0 top-0">
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
          <Link :href="item.path" class="flex items-center gap-3 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.text }}
          </Link>
        </li>
      </ul>
    </nav>

    <!-- Project Section -->
    <div class="mt-6">
      <div class="px-4 mb-2">
        <select 
          v-model="currentProject"
          class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option>Scrum Project</option>
        </select>
      </div>

      <ul class="space-y-1 px-2">
        <li v-for="item in props.projectItems" :key="item.text">
          <Link
            :href="item.path" @click="activateLink(item.text)"
            class="flex items-center gap-3 px-4 py-2 rounded-lg"
            :class="item.active ? 'bg-blue text-light hover:bg-button-hover' : 'text-gray-700'"
          >
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.text }}
          </Link>
        </li>
      </ul>
    </div>

    <!-- Meeting Summaries -->
    <div class="mt-6 px-4">
      <a href="/meetings" class="flex items-center gap-3 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
        <CalendarDays class="w-5 h-5" />
        Meeting Summaries
      </a>
    </div>
  </aside>
</template>