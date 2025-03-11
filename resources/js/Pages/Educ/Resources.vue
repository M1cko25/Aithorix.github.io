<script setup lang="ts">
import { Filter, ArrowUpDown, Video, MoreHorizontal, MoreVertical, Search, LayoutGrid, LayoutList, Table, Plus, Calendar, Clock, FileText, PaperclipIcon, XIcon, UploadCloudIcon, TrashIcon } from 'lucide-vue-next'
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue'
import { ref, onMounted, onUnmounted, computed } from 'vue'

interface Course {
    id: number;
    title: string;
    description: string;
    type: string;
    subject: string;
    Date: string;
    time: string;
    showMenu: boolean;
}

const showAddModal = ref(false)

const newCourse = ref({
    title: '',
    description: '',
    Date: '',
    time: '',
    tags: [] as string[]
})

const courses = ref<Course[]>([
    {
        id: 1,
        title: "Mathematics",
        description: "the science and study of quality, structure, space, and change.",
        type: "Document",
        subject: "Math",
        Date: "Dec 4, 2024",
        time: "10:00 AM",
        showMenu: false
    },
]);

const isValidForm = computed(() => {
    return newCourse.value.tags.length >= 2
})

const viewMode = ref('grid')

const closeAllModals = () => {
    filterOverlay.value = false
}
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
const isFilterModalOpen = ref(false)

const handleStatusOption = (status: string) => {
    console.log('Selected status:', status)
    isFilterModalOpen.value = false
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
const toggleMoreModal = () => {
    closeAllModals()
    isMoreModalOpen.value = !isMoreModalOpen.value
}
const isMoreModalOpen = ref(false)

const handleMoreOption = (option: string) => {
    console.log('Selected option:', option)
    isMoreModalOpen.value = false
}

const isOpen = ref(false);
const isDragging = ref(false);
const selectedFiles = ref([]);
const fileInput = ref<HTMLInputElement | null>(null);

// Methods

const removeFile = (index) => {
    selectedFiles.value = selectedFiles.value.filter((_, i) => i !== index);
};

const uploadFiles = () => {
    // Here you would typically implement the actual file upload logic
    // For example, using FormData and fetch/axios to send to a server

    console.log('Files to upload:', selectedFiles.value);

    // Example implementation:
    // const formData = new FormData();
    // selectedFiles.value.forEach(file => {
    //   formData.append('files', file);
    // });

    // fetch('/api/upload', {
    //   method: 'POST',
    //   body: formData
    // })
    // .then(response => response.json())
    // .then(data => {
    //   console.log('Upload successful', data);
    //   isOpen.value = false;
    //   selectedFiles.value = [];
    // })
    // .catch(error => {
    //   console.error('Upload failed', error);
    // });

    // For this example, we'll just simulate a successful upload
    setTimeout(() => {
        isOpen.value = false;
        selectedFiles.value = [];
        alert('Files uploaded successfully!');
    }, 1000);
};
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
                                <button @click="isOpen = true"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 border border-black-100">
                                    <PaperclipIcon class="mr-2 h-4 w-4" />
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
                                    <button @click="toggleMoreModal"
                                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors h-10 w-10 hover:bg-muted">
                                        <MoreVertical class="h-6 w-6" />
                                    </button>

                                    <!-- Menu Modal -->
                                    <div v-if="isMoreModalOpen"
                                        class="absolute top-full left-0 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[200px]">
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
        <Teleport to="body">
            <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="isOpen = false"></div>

                <!-- Modal content -->
                <div class="relative w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Add Attachment</h3>
                    </div>

                    <form @submit.prevent="" class="space-y-6 py-4">
                        <div class="space-y-4">
                            <div class="">

                                <!-- Title -->
                                <label class="text-m font-medium leading-none">Resources Name</label>
                                <p class="text-sm text-muted-foreground">The name of your file as it will appear to
                                    students.</p>
                            </div>
                            <input v-model="newCourse.title"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder="" />

                            <!-- Description -->
                            <div class="">
                                <label class="text-m font-medium leading-none">Description</label>
                                <p class="text-sm text-muted-foreground">A brief description of the file content.</p>
                            </div>
                            <textarea v-model="newCourse.description"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder=""></textarea>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <!-- Date -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium leading-none">Type:</label>
                                    <input
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background">
                                    </input>
                                </div>

                                <!-- Time -->
                                <div class="space-y-2">
                                    <label class="text-sm font-medium leading-none">Subject:</label>
                                    <input
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background" />
                                </div>
                            </div>
                            <div class="grid gap-4">
                                <!-- Tags -->

                            </div>
                        </div>
                    </form>
                    <div class="mb-4">

                        <!-- File input -->
                        <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-lg cursor-pointer"
                            :class="[isDragging ? 'border-primary bg-primary/5' : 'border-gray-300 hover:border-primary']">
                            <UploadCloudIcon class="w-10 h-10 mb-2 text-gray-400" />
                            <p class="mb-1 text-sm font-medium text-gray-700">
                                Drag files here or click to browse
                            </p>
                            <p class="text-xs text-gray-500">
                                Supports JPG, PNG, PDF, and other common file types
                            </p>
                            <input ref="fileInput" type="file" multiple class="hidden" />
                        </div>
                    </div>

                    <!-- Selected files list -->
                    <div v-if="selectedFiles.length > 0" class="mb-4">
                        <h4 class="mb-2 text-sm font-medium">Selected Files</h4>
                        <ul class="space-y-2 max-h-40 overflow-y-auto">
                            <li v-for="(index) in selectedFiles" :key="index"
                                class="flex items-center justify-between p-2 text-sm bg-gray-50 rounded-md">
                                <div class="flex items-center gap-2 truncate">
                                    <FileText class="w-4 h-4 text-gray-500" />
                                    <span class="truncate"></span>
                                </div>
                                <button @click="removeFile(index)" class="text-gray-500 hover:text-red-500">
                                    <TrashIcon class="w-4 h-4" />
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex justify-end gap-2">
                        <button @click="isOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button @click="uploadFiles" :disabled="selectedFiles.length === 0" class="btn-primary">
                            Upload
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>