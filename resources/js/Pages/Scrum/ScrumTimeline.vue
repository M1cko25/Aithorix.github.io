<script setup>
import Sidebar from '../../Components/Sidebar.vue';
import Header from '../../Components/Header.vue';
import Button from '../../Components/Button.vue';
import { Users, Video, Star, Share2, X, Calendar, CheckCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Gantt from '../../Components/Gantt.vue';

const page = usePage().props;
const activeTab = ref('timeline');

// Retrieve epics from page props
const epics = ref(
    page.epics.map(epic => ({
        ...epic,
        formattedDate: new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        }).format(new Date(epic.created_at))
    })) || []
);

// Modal State
const isModalOpen = ref(false);
const selectedEpic = ref(null);

// Open Modal
const openModal = (epic) => {
    selectedEpic.value = epic;
    isModalOpen.value = true;
};

// Close Modal when clicking outside
const closeModal = (event) => {
    if (event.target.id === "modal-overlay") {
        isModalOpen.value = false;
        selectedEpic.value = null;
    }
};
</script>

<template>
    <Sidebar />
    <Header />
    <Head title=" | Timeline" />

    <div class="min-h-screen overflow-y-auto bg-gray-200">
        <div class="ml-64 pt-16">
            <!-- Header Section -->
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold flex items-center gap-2">
                        <Calendar class="w-6 h-6 text-blue-600" />
                        {{ page.projectDetails.name }}
                    </h1>
                    <button class="p-2 text-gray-600 hover:text-gray-800">
                        <Users class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <button>
                        <Share2 class="w-5 h-5" />
                    </button>
                    <button>
                        <Star class="w-5 h-5" />
                    </button>
                    <Button text="Create Meeting" variant="primary" :style="`flex px-4 py-2 items-center gap-2`">
                        <Video class="w-5 h-5" />
                    </Button>
                </div>
            </div>

            <!-- Tabs -->
            <div>
                <div class="flex gap-8 border-b border-gray-300 mb-4">
                    <button @click="activeTab = 'timeline'" class="ml-6 pb-2 text-gray-600 flex items-center gap-2"
                        :class="activeTab === 'timeline' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Timeline
                    </button>
                    <button @click="activeTab = 'gantt'" class="pb-2 text-gray-600 flex items-center gap-2"
                        :class="activeTab === 'gantt' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        <Calendar class="w-5 h-5" /> Gantt Chart
                    </button>
                </div>

                <!-- Epics List UI (Only show in timeline tab) -->
                <div v-if="activeTab === 'timeline'" class="p-6 bg-gray-200 shadow-md rounded-lg">
                    <div v-if="epics.length > 0" class="space-y-2">
                        <div v-for="epic in epics" :key="epic.id"
                            class="p-4 bg-gray-200 rounded-lg flex justify-between items-center shadow-none border border-gray-200 hover:bg-gray-300 transition">
                            <div>
                                <h3 class="text-lg font-semibold text-blue-600 flex items-center gap-2">
                                    <CheckCircle class="w-5 h-5 text-green-600" /> You have completed {{ epic.name }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Created at: {{ epic.formattedDate }}
                                </p>
                            </div>
                            <button @click="openModal(epic)" class="text-blue-600 hover:underline">
                                See Details
                            </button>
                        </div>
                    </div>
                    <p v-else class="text-red-500 text-lg flex items-center gap-2">
                        <X class="w-5 h-5" /> No epics found.
                    </p>
                </div>

                <!-- Conditional Rendering -->
                <div v-if="activeTab === 'gantt'" class="w-full h-full">
                    <Gantt></Gantt>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" id="modal-overlay"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"
        @click="closeModal">
        <div class="bg-white p-6 rounded-2xl shadow-lg w-[40%] transform transition-all scale-95 hover:scale-100 duration-300" @click.stop>
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b pb-3">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <CheckCircle class="w-6 h-6 text-green-600" /> {{ selectedEpic?.name }}
                </h2>
                <button @click="isModalOpen = false" class="text-gray-500 hover:text-gray-800 transition">
                    <X class="w-6 h-6" />
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <p class="text-gray-700 leading-relaxed">
                    <strong class="text-gray-900">Description:</strong> 
                    {{ selectedEpic?.description || "No description available for this epic." }}
                </p>
                <p class="text-gray-500 text-sm mt-2">
                    <strong>Created at:</strong> {{ selectedEpic?.formattedDate }}
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 flex justify-end">
                <button @click="isModalOpen = false" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
