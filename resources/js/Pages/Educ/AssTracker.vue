<script setup>
import { ref } from 'vue'
import Sidebar from '../../Components/Sidebar.vue'
import Header from '../../Components/Header.vue'
import { Head } from '@inertiajs/vue3'
import {
    Filter,
    SortAsc,
    MoreVertical,

} from 'lucide-vue-next'

// Only one modal can be open at a time
const closeAllModals = () => {
    isFilterModalOpen.value = false
    isSortModalOpen.value = false
}

const toggleFilterModal = () => {
    if (!isFilterModalOpen.value) {
        closeAllModals()
    }
    isFilterModalOpen.value = !isFilterModalOpen.value
}

const toggleSortModal = () => {
    if (!isSortModalOpen.value) {
        closeAllModals()
    }
    isSortModalOpen.value = !isSortModalOpen.value
}

// Separate the modal states
const isFilterModalOpen = ref(false)
const isSortModalOpen = ref(false)

const filterOptions = ref({
    status: ''
})

const sortOptions = ref({
    priority: ''
})

const statusOptions = [
    'All',
    'Completed',
    'In Progress',
    'Pending',
    'Not Started'
]

const priorityOptions = [
    'All',
    'High',
    'Medium',
    'Low'
]

// const selectedStatus = ref([])
// const selectedPriority = ref([])

const selectStatus = (status) => {
    filterOptions.value.status = status
    applyFilters()
}

const selectPriority = (priority) => {
    sortOptions.value.priority = priority
    applySort()
}
const tasks = ref([
    {
        id: 1,
        name: 'Mathematics',
        description: 'the science and study of quality.',
        dueDate: 'Dec 4, 2024',
        priority: 'High Priority',
        status: 'Completed'
    },
    // {
    //     id: 2,
    //     name: 'History Paper',
    //     dueDate: 'Dec 5, 2024',
    //     priority: 'Medium',
    //     status: 'In Progress'
    // },
    // {
    //     id: 3,
    //     name: 'Chemistry Lab Report',
    //     dueDate: 'Dec 3, 2024',
    //     priority: 'Low',
    //     status: 'Pending'
    // },
    // {
    //     id: 4,
    //     name: 'Physics Exam',
    //     dueDate: 'Dec 10, 2024',
    //     priority: 'High',
    //     status: 'Not Started'
    // },
    // {
    //     id: 5,
    //     name: 'Final Exam',
    //     dueDate: 'Dec 12, 2024',
    //     priority: 'High',
    //     status: 'Pending'
    // },
    // {
    //     id: 6,
    //     name: 'Biology Class',
    //     dueDate: 'Dec 11, 2024',
    //     priority: 'Medium',
    //     status: 'In Progress'
    // },
    // {
    //     id: 7,
    //     name: 'Algebra Assignment',
    //     dueDate: 'Dec 15, 2024',
    //     priority: 'Medium',
    //     status: 'Not Started'
    // }
])

// const toggleFilterModal = () => {
//   isFilterModalOpen.value = !isFilterModalOpen.value
// }

// const toggleSortModal = () => {
//   isSortModalOpen.value = !isSortModalOpen.value
// }

const applyFilters = () => {
    const filteredTasks = tasks.value.filter(task => {
        if (filterOptions.value.status === '' || filterOptions.value.status === 'All') return true
        return task.status === filterOptions.value.status
    })
    tasks.value = filteredTasks
    closeAllModals()
}

const applySort = () => {
    const sortedTasks = [...tasks.value].sort((a, b) => {
        if (sortOptions.value.priority === '' || sortOptions.value.priority === 'All') return 0
        if (a.priority === sortOptions.value.priority) return -1
        if (b.priority === sortOptions.value.priority) return 1
        return 0
    })
    tasks.value = sortedTasks
    closeAllModals()
}

const getPriorityClass = (priority) => {
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
    return classes[priority]
}

const toggleMoreModal = () => {
    closeAllModals()
    isMoreModalOpen.value = !isMoreModalOpen.value
}
const isMoreModalOpen = ref(false)

const handleMoreOption = (option) => {
    console.log('Selected option:', option)
    isMoreModalOpen.value = false
}
const moreButton = [
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
</script>

<template>

    <Head title=" | Assignment Tracker" />

    <div class="p-6 bg-gray-50 min-h-screen">
        <Sidebar />
        <Header />
        <div class="ml-64 pt-16">
            <main class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="justify-between mb-6">
                        <div class="flex flex-1 justify-between items-center gap-4">
                            <h1 class="text-2xl font-semibold">Assignment Tracker</h1>
                        </div>

                        <div class="flex gap-4 pt-6 pb-4">

                            <!-- Filter Button and Popup -->
                            <div class="relative">
                                <button @click="toggleFilterModal"
                                    class="flex items-center gap-2 px-6 py-2 border rounded-md hover:bg-gray-50">
                                    <Filter class="w-4 h-4" />
                                    Filter
                                </button>

                                <div v-if="isFilterModalOpen"
                                    class="absolute top-full right-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[160px]">
                                    <button v-for="status in statusOptions" :key="status" @click="selectStatus(status)"
                                        class="w-full px-4 py-2 text-left hover:bg-gray-100">
                                        {{ status }}
                                    </button>
                                </div>
                            </div>
                            <!-- Sort Button and Popup -->
                            <div class="relative">
                                <button @click="toggleSortModal"
                                    class="flex items-center gap-2 px-6 py-2 border rounded-md hover:bg-gray-50">
                                    <SortAsc class="w-4 h-4" />
                                    Sort
                                </button>

                                <div v-if="isSortModalOpen"
                                    class="absolute top-full right-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[160px]">
                                    <button v-for="priority in priorityOptions" :key="priority"
                                        @click="selectPriority(priority)"
                                        class="w-full px-4 py-2 text-left hover:bg-gray-100">
                                        {{ priority }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border w-full">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b transition-colors hover:bg-muted/50">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Task Name</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Due Date</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Priority</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-xl w-[70px]">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="task in tasks" :key="task.id"
                                    class="border-b transition-colors hover:bg-muted/50">
                                    <td class="p-4">
                                        <div class="font-medium text-lg">{{ task.name }}</div>
                                        <div class="text-m text-muted-foreground">{{ task.description }}</div>
                                    </td>

                                    <!-- Date -->
                                    <td class="p-4">
                                        <div class="text-lg">{{ task.dueDate }}</div>
                                    </td>

                                    <!-- Time -->
                                    <td class="p-4">
                                        <span :class="getPriorityClass(task.priority)"
                                            class="px-2.5 py-1 text-m font-medium rounded-md">
                                            {{ task.priority }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-lg">{{ task.status }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="relative">

                                            <!-- Menu Button -->
                                            <button @click="toggleMoreModal" class="inline-flex items-center justify-center rounded-md text-sm
                                                font-medium ring-offset-background transition-colors h-10 w-10
                                                hover:bg-muted">
                                                <MoreVertical class="h-6 w-6" />
                                            </button>

                                            <!-- Menu Modal -->
                                            <div v-if="isMoreModalOpen"
                                                class="absolute top-full right-10 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[200px]">
                                                <div class="p-2">
                                                    <button v-for="item in moreButton" :key="item.action"
                                                        @click="handleMoreOption(item.action)"
                                                        class="w-full px-4 py-2 text-left hover:bg-gray-100"
                                                        :class="item.class">
                                                        {{ item.label }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
