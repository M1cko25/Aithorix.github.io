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

const searchQuery = ref('')
const currentProject = ref('Scrum Project')

const menuItems = [
  { icon: Home, text: 'Home', path: '/home' },
  { icon: Briefcase, text: 'My Work', path: '/work' },
  { icon: Star, text: 'Starred', path: '/starred' },
]

const projectItems = [
  { icon: LayoutDashboard, text: 'Dashboard', path: '/dashboard' },
  { icon: LayoutList, text: 'Board', path: '/board', active: true },
  { icon: Clock, text: 'Timeline', path: '/timeline' },
  { icon: Package, text: 'Backlog', path: '/backlog' },
  { icon: Rocket, text: 'Upgrade Plan', path: '/upgrade' },
]
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
          <a :href="item.path" class="flex items-center gap-3 px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.text }}
          </a>
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
        <li v-for="item in projectItems" :key="item.text">
          <a 
            :href="item.path" 
            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100"
            :class="item.active ? 'bg-blue-50 text-blue-600' : 'text-gray-700'"
          >
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.text }}
          </a>
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