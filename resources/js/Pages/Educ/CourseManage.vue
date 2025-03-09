<script setup lang="ts">
import { Filter, Video, MoreVertical, LayoutGrid, Table, Plus, Calendar, Clock } from 'lucide-vue-next'
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref, computed } from 'vue'

//View Toggle
const viewMode = ref('table')

// Filter
const filterButton = () => {
    if (!filterOverlay.value) {
        closeAllModals()
    }
    filterOverlay.value = !filterOverlay.value
}
const filterOverlay = ref(false)
const filterOptions = [
    'Name',
    'Date Motified',
    'Type',
]
const handlefilterOption = (filter: string) => {
    console.log('Selected filter:', filter)
    filterOverlay.value = false
}

//Add Course
const addCourseButton = ref(false)
const newCourse = ref({
    title: '',
    description: '',
    Date: '',
    time: '',
    tags: [] as string[]
})
const handleAddCourse = () => {
    if (!isValidForm.value) return

    const newCourseData = {
        id: course.value.length + 1,
        ...newCourse.value,
        showMenu: false
    }

    course.value.push(newCourseData)

    // Reset form
    newCourse.value = {
        title: '',
        description: '',
        Date: '',
        time: '',
        tags: []
    }

    addCourseButton.value = false
}
const canSelectMoreTags = computed(() => {
    return newCourse.value.tags.length < 2
})

//Tag Options
const tagOptions = [
    { value: 'Low Priority', label: 'Low Priority', color: 'bg-teal-50 text-teal-700' },
    { value: 'Medium Priority', label: 'Medium Priority', color: 'bg-orange-50 text-orange-700' },
    { value: 'High Priority', label: 'High Priority', color: 'bg-red-50 text-red-700' },
    { value: 'Assignment', label: 'Assignment', color: 'bg-emerald-50 text-emerald-700' },
    { value: 'Exam', label: 'Exam', color: 'bg-indigo-50 text-indigo-700' },
    { value: 'Lab', label: 'Lab', color: 'bg-purple-50 text-purple-700' },
    { value: 'Meeting', label: 'Meeting', color: 'bg-pink-50 text-pink-700' },
    { value: 'Quiz', label: 'Quiz', color: 'bg-yellow-50 text-yellow-700' },
    { value: 'Group Activity', label: 'Group Activity', color: 'bg-fuchsia-50 text-fuchsia-700' },
    { value: 'Personal Goal', label: 'Personal Goal', color: 'bg-green-50 text-green-700' }
]
const toggleTag = (tagValue: string) => {
    const index = newCourse.value.tags.indexOf(tagValue)
    if (index === -1) {
        if (canSelectMoreTags.value) {
            newCourse.value.tags.push(tagValue)
        }
    } else {
        newCourse.value.tags.splice(index, 1)
    }
}
const isValidForm = computed(() => {
    return newCourse.value.title &&
        newCourse.value.description &&
        newCourse.value.Date &&
        newCourse.value.time &&
        newCourse.value.tags.length >= 2
})


//Course Data
interface Course {
    id: number
    title: string
    description: string
    Date: string
    time: string
    tags: string[]
    showMenu: boolean
}
const course = ref([
    {
        id: 1,
        title: "Mathematics",
        description: "the science and study of quality, structure, space, and change.",
        Date: "Dec 4, 2024",
        time: "10:00 AM",
        tags: ["High Priority", "Personal Goal"],
        showMenu: false
    }
])
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

//Menu
const selectedCourse = ref<Course | null>(null)
const menuButton = (course: Course) => {
    if (!menuOverlay.value) {
        closeAllModals()
    }
    menuOverlay.value = !menuOverlay.value
    selectedCourse.value = course
}
const menuOverlay = ref(false)
const menuOptions = [
    {
        label: 'Edit Course',
        action: 'edit',
        class: 'text-gray-900 hover:bg-gray-100'
    },
    {
        label: 'Delete Course',
        action: 'delete',
        class: 'text-red-500 hover:bg-gray-100'
    }
]
const handleMenuOption = (option: string, selectedCourse: Course) => {
    if (option === 'edit') {
        editingCourse.value = { ...selectedCourse };
        showEditModal.value = true;
    } else if (option === 'delete') {
        courseToDelete.value = selectedCourse;
        showDeleteModal.value = true;
    }
    menuOverlay.value = false;
}


// Close all modals
const closeAllModals = () => {
    filterOverlay.value = false
    menuOverlay.value = false
}

//Edit Modal
const showEditModal = ref(false)
const editingCourse = ref({
    id: 0,
    title: '',
    description: '',
    Date: '',
    time: '',
    tags: [] as string[]
})
const toggleEditTag = (tagValue: string) => {
    const index = editingCourse.value.tags.indexOf(tagValue)
    if (index === -1) {
        if (canSelectMoreEditTags.value) {
            editingCourse.value.tags.push(tagValue)
        }
    } else {
        editingCourse.value.tags.splice(index, 1)
    }
}
const updateCourse = () => {
    const index = course.value.findIndex(c => c.id === editingCourse.value.id)
    if (index !== -1) {
        course.value[index] = { ...editingCourse.value, showMenu: false }
    }
    showEditModal.value = false
}
const canSelectMoreEditTags = computed(() => {
    return editingCourse.value.tags.length < 2
})

//Delete Modal
const showDeleteModal = ref(false)
const courseToDelete = ref<Course | null>(null)
const deleteCourse = () => {
    if (courseToDelete.value) {
        course.value = course.value.filter(c => c.id !== courseToDelete.value?.id)
        showDeleteModal.value = false
        courseToDelete.value = null
    }
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
                        </div>

                        <div class="flex gap-64 items-center justify-between pt-6 pb-4">
                            <div class="flex gap-4">
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
                                <div class="relative">
                                    <button @click="filterButton"
                                        class="flex items-center justify-center rounded-md border h-10 w-10 border-input">
                                        <Filter class="w-4 h-4" />
                                    </button>

                                    <!-- Filter Overlay -->
                                    <div v-if="filterOverlay"
                                        class="absolute top-full right-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[160px]">
                                        <button v-for="filter in filterOptions" :key="filter"
                                            @click="handlefilterOption(filter)"
                                            class="w-full px-4 py-2 text-left hover:bg-gray-100">
                                            {{ filter }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button class="btn btn-primary">
                                    <Video class="w-6 h-6 mr-2" />
                                    Create Meeting
                                </button>

                                <!-- Add Course Button -->
                                <button @click="addCourseButton = true"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 border border-black-100">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Course
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div v-if="viewMode === 'table'" class="rounded-lg border">
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
                                <tr v-for="course in course" :key="course.id"
                                    class="border-b transition-colors hover:bg-muted/50">

                                    <!-- Course -->
                                    <td class="p-4">
                                        <div class="font-medium text-lg">{{ course.title }}</div>
                                        <div class="text-m text-muted-foreground">{{ course.description }}</div>
                                    </td>

                                    <!-- Date -->
                                    <td class="p-4">
                                        <div class="text-lg">{{ course.Date }}</div>
                                    </td>

                                    <!-- Time -->
                                    <td class="p-4">
                                        <div class="text-lg">{{ course.time }}</div>
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
                                            <button @click="menuButton(course)"
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 w-10 hover:bg-muted">
                                                <MoreVertical class="h-6 w-6" />
                                            </button>



                                            <!-- Menu Overlay -->
                                            <div v-if="menuOverlay"
                                                class="absolute top-full right-10 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[200px]">
                                                <div class="p-2">
                                                    <button @click="handleMenuOption('edit', course)"
                                                        class="w-full px-4 py-2 text-left hover:bg-gray-100 text-gray-900">
                                                        Edit Course
                                                    </button>
                                                    <button @click="handleMenuOption('delete', course)"
                                                        class="w-full px-4 py-2 text-left hover:bg-gray-100 text-red-500">
                                                        Delete Course
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Edit Course Modal -->
                                            <div v-if="showEditModal"
                                                class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm">
                                                <div
                                                    class="fixed left-[50%] top-[50%] z-50 w-full max-w-[800px] translate-x-[-50%] translate-y-[-50%] border bg-white p-6 shadow-lg rounded-lg">
                                                    <div class="flex flex-col text-center sm:text-left">
                                                        <h2 class="text-lg font-semibold">Edit Course</h2>
                                                        <p class="text-sm">Update course information below.</p>
                                                    </div>
                                                    <form @submit.prevent="updateCourse" class="space-y-6 py-4">
                                                        <!-- Same form fields as Add Course but with v-model="editingCourse.[field]" -->
                                                        <input v-model="editingCourse.title"
                                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" />
                                                        <textarea v-model="editingCourse.description"
                                                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2"></textarea>
                                                        <div class="grid gap-4 sm:grid-cols-2">
                                                            <input v-model="editingCourse.Date" type="date"
                                                                class="flex h-10 w-full rounded-md border" />
                                                            <input v-model="editingCourse.time" type="time"
                                                                class="flex h-10 w-full rounded-md border" />
                                                        </div>

                                                        <!-- Tags selection -->
                                                        <div class="space-y-2">
                                                            <label class="text-sm font-medium leading-none">Tags (Select
                                                                exactly 2)</label>
                                                            <div class="grid grid-cols-5 gap-2 mt-2">
                                                                <div v-for="tag in tagOptions" :key="tag.value" :class="[
                                                                    tag.color,
                                                                    'flex items-center p-2 rounded-md transition-colors',
                                                                    editingCourse.tags.includes(tag.value) ? 'ring-2 ring-offset-2' : '',
                                                                    (!canSelectMoreEditTags && !editingCourse.tags.includes(tag.value)) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                                                ]" @click="toggleEditTag(tag.value)">
                                                                    <input type="checkbox"
                                                                        :checked="editingCourse.tags.includes(tag.value)"
                                                                        :disabled="!canSelectMoreEditTags && !editingCourse.tags.includes(tag.value)"
                                                                        class="mr-2" />
                                                                    <span class="text-xs">{{ tag.label }}</span>
                                                                </div>
                                                            </div>
                                                            <p v-if="editingCourse.tags.length < 2"
                                                                class="text-sm text-red-500 mt-1">
                                                                Please select {{ 2 - editingCourse.tags.length }} more
                                                                tag{{ 2 - editingCourse.tags.length !== 1 ? 's' : '' }}
                                                            </p>
                                                        </div>

                                                        <div class="flex justify-end space-x-4">
                                                            <button type="button" @click="showEditModal = false"
                                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground">Cancel</button>
                                                            <button type="submit" class="btn-primary"
                                                                :disabled="editingCourse.tags.length < 2">Update
                                                                Course</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Delete Course Modal -->
                                            <div v-if="showDeleteModal"
                                                class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm">
                                                <div
                                                    class="fixed left-[50%] top-[50%] z-50 w-full max-w-[400px] translate-x-[-50%] translate-y-[-50%] border bg-white p-6 shadow-lg rounded-lg">
                                                    <div class="flex flex-col text-center sm:text-left">
                                                        <h2 class="text-lg font-semibold">Delete Course</h2>
                                                        <p class="text-sm text-gray-600 mt-2">Are you sure you want to
                                                            delete this course? This action cannot be undone.</p>
                                                    </div>
                                                    <div class="flex justify-end space-x-4 mt-6">
                                                        <button @click="showDeleteModal = false"
                                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                                                            Cancel
                                                        </button>
                                                        <button @click="deleteCourse" class="btn-primary">
                                                            Delete
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
                        <div v-for="course in course" :key="course.id"
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
                                    <button @click="menuButton(course)"
                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 w-10 hover:bg-muted">
                                        <MoreVertical class="h-6 w-6" />
                                    </button>

                                    <!-- Menu Overlay -->
                                    <div v-if="menuOverlay"
                                        class="absolute top-full left-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[200px]">
                                        <div class="p-2">
                                            <button @click="handleMenuOption('edit', course)"
                                                class="w-full px-4 py-2 text-left hover:bg-gray-100 text-gray-900">
                                                Edit Course
                                            </button>
                                            <button @click="handleMenuOption('delete', course)"
                                                class="w-full px-4 py-2 text-left hover:bg-gray-100 text-red-500">
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
                </div>
            </main>
        </div>

        <!-- Add Course Modal -->
        <div v-if="addCourseButton" class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm">
            <div
                class="fixed left-[50%] top-[50%] z-50 w-full max-w-[800px] translate-x-[-50%] translate-y-[-50%] border bg-white p-6 shadow-lg rounded-lg">
                <div class="flex flex-col text-center sm:text-left">
                    <h2 class="text-lg font-semibold">Create New Course</h2>
                    <p class="text-sm">Create a new course by filling out the information
                        below.
                    </p>
                </div>
                <form @submit.prevent="handleAddCourse" class="space-y-6 py-4">
                    <div class="space-y-4">
                        <div>

                            <!-- Title -->
                            <label class="text-m font-medium leading-none">Course Title</label>
                            <p class="text-sm text-muted-foreground">The name of your course as
                                it will appear to
                                students.</p>
                        </div>
                        <input v-model="newCourse.title"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Mathematics" />

                        <!-- Description -->
                        <div>
                            <label class="text-m font-medium leading-none">Description</label>
                            <p class="text-sm text-muted-foreground">A brief description of the
                                course content and
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
                                <label class="text-sm font-medium leading-none">Tags (Select exactly 2)</label>
                                <div class="grid grid-cols-5 gap-2 mt-2">
                                    <div v-for="tag in tagOptions" :key="tag.value" :class="[
                                        tag.color,
                                        'flex items-center p-2 rounded-md transition-colors',
                                        newCourse.tags.includes(tag.value) ? 'ring-2 ring-offset-2' : '',
                                        (!canSelectMoreTags && !newCourse.tags.includes(tag.value)) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                    ]" @click="toggleTag(tag.value)">
                                        <input type="checkbox" :checked="newCourse.tags.includes(tag.value)"
                                            :disabled="!canSelectMoreTags && !newCourse.tags.includes(tag.value)"
                                            class="mr-2" />
                                        <span class="text-xs">{{ tag.label }}</span>
                                    </div>
                                </div>
                                <p v-if="newCourse.tags.length < 2" class="text-sm text-red-500 mt-1">
                                    Please select {{ 2 - newCourse.tags.length }} more tag{{ 2 - newCourse.tags.length
                                        !== 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="addCourseButton = false"
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