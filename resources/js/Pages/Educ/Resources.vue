<script setup lang="ts">
import { Filter, ArrowUpDown, Video, MoreHorizontal, MoreVertical, Search, LayoutGrid, LayoutList, Table, Plus, Calendar, Clock, FileText } from 'lucide-vue-next'
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'

interface Course {
    id: number
    title: string
    description: string
    type: string
    subject: string
    Date;
    showMenu: boolean
}

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
        description: "the science and study of quality, structure, space, and change.",
        type: "Document",
        subject: "Math",
        Date: "Dec 4, 2024",
        showMenu: false
    },
    {
        id: 2,
        title: "Mathematics",
        description: "the science and study of quality, structure, space, and change.",
        type: "Document",
        subject: "Math",
        Date: "Dec 4, 2024",
        showMenu: false
    },
    {
        id: 3,
        title: "Mathematics",
        description: "the science and study of quality, structure, space, and change.",
        type: "Document",
        subject: "Math",
        Date: "Dec 4, 2024",
        showMenu: false
    },
]

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

    <Head title=" | Resources" />
    <div class="p-6 bg-gray-50 min-h-screen">
        <Sidebar />
        <Header />
        <div class="ml-64 pt-16">
            <main class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="justify-between mb-6 ">
                        <div class="flex flex-1 justify-between items-center gap-4">

                            <!-- Course Management Heading -->
                            <h1 class="text-2xl font-semibold text-gray-900">Resources</h1>

                            <!-- Create Meeting Button -->

                        </div>
                        <div class="flex gap-64 items-center justify-between pt-6 pb-4">
                            <div class="flex gap-4">

                                <!-- Dropdown for Status -->
                                <!-- <select v-model="selectedStatus"
                                    class="inline-flex items-center gap-2 px-6 py-2 border rounded-md hover:bg-gray-50">
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select> -->


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

                                <!-- Filter Button -->
                                <button
                                    class="flex items-center justify-center rounded-md border h-10 w-10 border-input">
                                    <Filter class="w-4 h-4" />
                                    <span class="sr-only">Filter courses</span>
                                </button>
                            </div>

                            <div class="flex gap-4">
                                <button class="btn btn-primary">
                                    <Video class="w-6 h-6 mr-2" />
                                    Create Meeting
                                </button>

                                <!-- Add Course Button -->
                                <button @click="showAddModal = true"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 border border-black-100">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Attachment
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div v-if="viewMode === 'table'" class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b transition-colors hover:bg-muted/50">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Resources Name</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Type</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Subject</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Added On</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl w-[70px]">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="course in courses" :key="course.id"
                                    class="border-b transition-colors hover:bg-muted/50">

                                    <!-- Course -->
                                    <td class="p-4">
                                        <div class="font-medium text-lg">{{ course.title }}</div>
                                        <div class="text-m text-muted-foreground">{{ course.description }}</div>
                                    </td>

                                    <!-- Date -->
                                    <td class="p-4">
                                        <div class="text-lg">{{ course.type }}</div>
                                    </td>

                                    <!-- Time -->
                                    <td class="p-4">
                                        <div class="text-lg">{{ course.subject }}</div>
                                    </td>

                                    <!-- Tags -->
                                    <td class="p-4">
                                        <div class="text-lg">{{ course.Date }}</div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4">
                                        <div class="relative">

                                            <!-- Menu Button -->
                                            <button class="inline-flex items-center justify-center rounded-md text-sm
                                                font-medium ring-offset-background transition-colors h-10 w-10
                                                hover:bg-muted">
                                                <MoreVertical class="h-6 w-6" />
                                                <span class="sr-only">Open menu</span>
                                            </button>

                                            <!-- Menu Modal -->
                                            <!-- <div v-if="course.showMenu"
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
                                            </div> -->
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
                            <div class="p-2 flex flex-row items-start justify-between space-y-0">
                                <FileText class="h-24 w-64 pt-2" />
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

                            <div class="p-6 pt-4 space-y-4">

                                <!-- Date -->
                                <div class="flex items-center gap-4 text-m">
                                    <Calendar class="h-6 w-6 text-muted-foreground" />
                                    <span>{{ course.Date }} </span>
                                </div>

                                <!-- Time -->


                                <!-- Tags -->

                            </div>
                        </div>
                    </div>
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