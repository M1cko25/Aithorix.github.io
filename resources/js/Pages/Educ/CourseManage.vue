<script setup lang="ts">
import { Filter, ArrowUpDown, Video, MoreHorizontal, MoreVertical, Search, LayoutGrid, LayoutList, Table, Plus, Calendar, Clock } from 'lucide-vue-next'
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'

interface Course {
    id: number
    title: string
    description: string
    Date;
    time: string
    tags: string[]
    showMenu: boolean
}

// Filtering and Sorting
const selectedStatus = ref('all')
const statusOptions = [
    { value: 'all', label: 'All Courses' },
    { value: 'active', label: 'Active' },
    { value: 'upcoming', label: 'Upcoming' },
    { value: 'completed', label: 'Completed' }
]

const showAddModal = ref(false)

const newCourse = ref({
    title: '',
    description: '',
    Date: '',
    time: '',
    tags: [] as string[]
})

const courses: Course[] = [
    {
        id: 1,
        title: "Mathematics",
        description: "Advanced Calculus",
        Date: "February 22, 2025",
        time: "10:00 AM",
        tags: ["Meeting", "Personal Goal"],
        showMenu: false
    },
    {
        id: 2,
        title: "English",
        description: "Literature Analysis",
        Date: "February 25, 2025",
        time: "1:00 PM",
        tags: ["Assignment", "Collaborative"],
        showMenu: false
    },
    {
        id: 3,
        title: "Science",
        description: "Chemistry Lab",
        Date: "February 28, 2025",
        time: "2:30 PM",
        tags: ["Group Study", "Optional"],
        showMenu: false
    },
    {
        id: 4,
        title: "History",
        description: "World War II",
        Date: "March 5, 2025",
        time: "10:00 AM",
        tags: ["Class", "High Priority"],
        showMenu: false
    },
    {
        id: 5,
        title: "Computer Science",
        description: "Data Structures",
        Date: "March 10, 2025",
        time: "1:00 PM",
        tags: ["Lab", "Group Activity"],
        showMenu: false
    },
]

const getTagClass = (tag: string): string => {
    const classes = {
        'Class': 'bg-teal-50 text-teal-700',
        'Assignment': 'bg-emerald-50 text-emerald-700',
        'Group Study': 'bg-indigo-50 text-indigo-700',
        'Lab': 'bg-purple-50 text-purple-700',
        'Meeting': 'bg-pink-50 text-pink-700',

        'Optional': 'bg-orange-50 text-orange-700',
        'Collaborative': 'bg-yellow-50 text-yellow-700',
        'Group Activity': 'bg-fuchsia-50 text-fuchsia-700',
        'High Priority': 'bg-red-50 text-red-700',
        'Personal Goal': 'bg-green-50 text-green-700',
    }
    return classes[tag as keyof typeof classes] || 'bg-gray-50 text-gray-700'
}

const tagOptions = [
    { value: 'Class', label: 'Class', color: 'bg-teal-50 text-teal-700' },
    { value: 'Assignment', label: 'Assignment', color: 'bg-emerald-50 text-emerald-700' },
    { value: 'Group Study', label: 'Group Study', color: 'bg-indigo-50 text-indigo-700' },
    { value: 'Lab', label: 'Lab', color: 'bg-purple-50 text-purple-700' },
    { value: 'Meeting', label: 'Meeting', color: 'bg-pink-50 text-pink-700' },
    { value: 'Optional', label: 'Optional', color: 'bg-orange-50 text-orange-700' },
    { value: 'Collaborative', label: 'Collaborative', color: 'bg-yellow-50 text-yellow-700' },
    { value: 'Group Activity', label: 'Group Activity', color: 'bg-fuchsia-50 text-fuchsia-700' },
    { value: 'High Priority', label: 'High Priority', color: 'bg-red-50 text-red-700' },
    { value: 'Personal Goal', label: 'Personal Goal', color: 'bg-green-50 text-green-700' }
]
const toggleTag = (tagValue: string) => {
    const index = newCourse.value.tags.indexOf(tagValue)
    if (index === -1) {
        newCourse.value.tags.push(tagValue)
    } else {
        newCourse.value.tags.splice(index, 1)
    }
}
const isValidForm = computed(() => {
    return newCourse.value.tags.length >= 2
})

const viewMode = ref('grid')
const handleAddCourse = () => {
    // Handle course creation logic here
    showAddModal.value = false
}

const toggleMenu = (course: Course, event: Event) => {
    event.stopPropagation()
    courses.forEach(c => {
        c.showMenu = c === course ? !c.showMenu : false
    })
}
</script>

<template>

    <Head title=" | Course Management" />
    <div class="p-6 bg-gray-50 min-h-screen">
        <Sidebar />
        <Header />
        <div class="ml-64 pt-16">
            <main class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="justify-between mb-6 ">
                        <div class="flex flex-1 justify-between items-center gap-4">

                            <!-- Course Management Heading -->
                            <h1 class="text-2xl font-semibold text-gray-900">Course Management</h1>

                            <!-- Create Meeting Button -->
                            <button class="btn btn-primary">
                                <Video class="w-6 h-6 mr-2" />
                                Create Meeting
                            </button>
                        </div>
                        <div class="flex items-center justify-between pt-6 pb-4">
                            <div class="flex gap-4">
                                <!-- Dropdown for Status -->
                                <select v-model="selectedStatus"
                                    class="inline-flex items-center gap-2 px-6 py-2 border rounded-md hover:bg-gray-50">
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>

                                <!-- Filter Button -->
                                <button
                                    class="flex items-center justify-center rounded-md border h-10 w-10 border-input">
                                    <Filter class="w-4 h-4" />
                                    <span class="sr-only">Filter courses</span>
                                </button>

                                <div class="flex items-center gap-2 rounded-lg border px-3">

                                    <!-- Card View Toggle -->
                                    <button class="inline-flex items-center justify-center h-10 w-10 rounded-md"
                                        :class="viewMode === 'grid' ? 'bg-secondary text-secondary-foreground' : 'hover:bg-muted'"
                                        @click="viewMode = 'grid'">
                                        <LayoutGrid class="h-4 w-4" />
                                        <span class="sr-only">Card view</span>
                                    </button>

                                    <!-- Table View Toggle -->
                                    <button class="inline-flex items-center justify-center h-10 w-10 rounded-md"
                                        :class="viewMode === 'table' ? 'bg-secondary text-secondary-foreground' : 'hover:bg-muted'"
                                        @click="viewMode = 'table'">
                                        <Table class="h-4 w-4" />
                                        <span class="sr-only">Table view</span>
                                    </button>
                                </div>

                                <!-- Add Course Button -->
                                <button @click="showAddModal = true"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 border border-black-100">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Course
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div v-if="viewMode === 'table'" class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b transition-colors hover:bg-muted/50">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Course</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Date</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Time</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Tags</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl w-[70px]">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="course in courses" :key="course.id"
                                    class="border-b transition-colors hover:bg-muted/50">

                                    <!-- Course -->
                                    <td class="p-4">
                                        <div>
                                            <div class="font-medium text-lg">{{ course.title }}</div>
                                            <div class="text-m text-muted-foreground">{{ course.description }}</div>
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <Calendar class="h-4 w-4 text-muted-foreground" />
                                            <span class="text-lg">{{ course.Date }}</span>
                                        </div>
                                    </td>

                                    <!-- Time -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <Clock class="h-4 w-4 text-muted-foreground" />
                                            <span class="text-lg">{{ course.time }}</span>
                                        </div>
                                    </td>

                                    <!-- Tags -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="space-y-2">
                                                <span v-for="tag in course.tags" :key="tag"
                                                    class="px-2.5 py-1 text-m font-medium rounded-md"
                                                    :class="getTagClass(tag)">
                                                    {{ tag }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4">
                                        <div class="relative">

                                            <!-- Menu Button -->
                                            <button @click="course.showMenu = !course.showMenu"
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 w-10 hover:bg-muted">
                                                <MoreVertical class="h-6 w-6" />
                                                <span class="sr-only">Open menu</span>
                                            </button>

                                            <!-- Menu Modal -->
                                            <div v-if="course.showMenu"
                                                class="menu-content absolute right-0 z-10 mt-2 w-56 rounded-md border bg-popover text-popover-foreground shadow-md">
                                                <div @click.self="course.showMenu = false" class="fixed inset-0"></div>
                                                <div
                                                    class="absolute right-0 z-10 mt-2 w-56 rounded-md border bg-popover text-popover-foreground shadow-md">
                                                    <div class="p-2">
                                                        <div class="px-2 py-1.5 text-sm font-semibold">Actions</div>
                                                        <button
                                                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent">
                                                            View Course
                                                        </button>
                                                        <button
                                                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent">
                                                            View Resources
                                                        </button>
                                                        <div class="h-px my-1 -mx-1 bg-muted"></div>
                                                        <button
                                                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent">
                                                            Edit Course
                                                        </button>
                                                        <button
                                                            class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none text-red-500 hover:bg-accent">
                                                            Delete Course
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Card View -->
                    <div v-else
                        :class="viewMode === 'grid' ? 'grid gap-4 md:grid-cols-2 lg:grid-cols-3' : 'flex flex-col gap-4'">
                        <div v-for="course in courses" :key="course.id"
                            class="rounded-lg border bg-card text-card-foreground shadow-sm">
                            <div class="p-6 flex flex-row items-start justify-between space-y-0">
                                <div class="space-y-1">

                                    <!-- Course Title -->
                                    <h3 class="font-bold text-2xl leading-none tracking-tight">{{ course.title }}</h3>

                                    <!-- Course Description -->
                                    <p class="text-lg text-muted-foreground">{{ course.description }}</p>
                                </div>
                                <div class="relative">

                                    <!-- Menu Button -->
                                    <button @click="course.showMenu = !course.showMenu"
                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 w-10 hover:bg-muted">
                                        <MoreVertical class="h-6 w-6" />
                                        <span class="sr-only">Open menu</span>
                                    </button>

                                    <!-- Menu Modal -->
                                    <div v-if="course.showMenu"
                                        class="absolute right-0 z-10 mt-2 w-56 rounded-md border bg-popover text-popover-foreground shadow-md">
                                        <div class="p-2">
                                            <div class="px-2 py-1.5 text-sm font-semibold">Actions</div>
                                            <button
                                                class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent">
                                                View Course
                                            </button>
                                            <button
                                                class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent">
                                                View Resources
                                            </button>
                                            <div class="h-px my-1 -mx-1 bg-muted"></div>
                                            <button
                                                class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent">
                                                Edit Course
                                            </button>
                                            <button
                                                class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none text-red-500 hover:bg-accent">
                                                Delete Course
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 pt-0 space-y-4">

                                <!-- Date -->
                                <div class="flex items-center gap-4 text-m">
                                    <Calendar class="h-6 w-6 text-muted-foreground" />
                                    <span>{{ course.Date }} </span>
                                </div>

                                <!-- Time -->
                                <div class="flex items-center gap-4 text-m">
                                    <Clock class="h-6 w-6 text-muted-foreground" />
                                    <span>{{ course.time }} </span>
                                </div>

                                <!-- Tags -->
                                <div class="space-y-2">
                                    <div class="text-m font-medium pb-3">Tags</div>
                                    <span v-for="tag in course.tags" :key="tag"
                                        class="px-3 py-1 text-s font-medium rounded-md mr-2 mb-2 inline-block"
                                        :class="getTagClass(tag)">
                                        {{ tag }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card View -->
                    <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="course in courses" :key="course.id" class="bg-white p-4 rounded-lg border">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ course.title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ course.time }}</p>
                                </div>
                                <button class="p-1 hover:bg-gray-100 rounded">
                                    <MoreHorizontal class="w-5 h-5 text-gray-500" />
                                </button>
                            </div>

                            <p class="text-sm text-gray-600 mt-3">{{ course.description }}</p>

                            <div class="flex gap-2 mt-4">
                                <span v-for="tag in course.tags" :key="tag"
                                    class="px-2.5 py-1 text-xs font-medium rounded-md" :class="getTagClass(tag)">
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </div> -->

                </div>
            </main>
        </div>

        <!-- Add Course Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm">
            <div
                class="fixed left-[50%] top-[50%] z-50 w-full max-w-[800px] translate-x-[-50%] translate-y-[-50%] border bg-white p-6 shadow-lg rounded-lg">
                <div class="flex flex-col text-center sm:text-left">
                    <h2 class="text-lg font-semibold">Create New Course</h2>
                    <p class="text-sm">Create a new course by filling out the information
                        below.
                    </p>
                </div>
                <form @submit.prevent="" class="space-y-6 py-4">
                    <div class="space-y-4">
                        <div class="">

                            <!-- Title -->
                            <label class="text-m font-medium leading-none">Course Title</label>
                            <p class="text-sm text-muted-foreground">The name of your course as it will appear to
                                students.</p>
                        </div>
                        <input v-model="newCourse.title"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Mathematics" />

                        <!-- Description -->
                        <div class="">
                            <label class="text-m font-medium leading-none">Description</label>
                            <p class="text-sm text-muted-foreground">A brief description of the course content and
                                objectives.</p>
                        </div>
                        <textarea v-model="newCourse.description"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Advanced Calculus and its applications..."></textarea>

                        <div class="grid gap-4 sm:grid-cols-2">

                            <!-- Date -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium leading-none">Date</label>
                                <input v-model="newCourse.Date" type="date"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background">
                                </input>
                            </div>
                            <!-- Time -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium leading-none">Time</label>
                                <input v-model="newCourse.time" type="time"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" />
                            </div>
                        </div>
                        <div class="grid gap-4">
                            <!-- Tags -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium leading-none">Tags (Select at least 2)</label>
                                <div class="grid grid-cols-5 gap-2 mt-2">
                                    <div v-for="tag in tagOptions" :key="tag.value" :class="[
                                        tag.color,
                                        'flex items-center p-2 rounded-md cursor-pointer transition-colors',
                                        newCourse.tags.includes(tag.value) ? 'ring-2 ring-offset-2' : ''
                                    ]" @click="toggleTag(tag.value)">
                                        <input type="checkbox" :checked="newCourse.tags.includes(tag.value)"
                                            class="mr-2" />
                                        <span class="text-xs">{{ tag.label }}</span>
                                    </div>
                                </div>

                                <!-- Error message if less than 2 tags -->
                                <p v-if="newCourse.tags.length < 2" class="text-sm text-red-500 mt-1">
                                    Please select at least 2 tags
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showAddModal = false"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="!isValidForm">
                            Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>