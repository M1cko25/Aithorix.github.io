<script setup>
import Header from '../../Components/Header.vue'
import Sidebar from '../../Components/Sidebar.vue';
import Button from '../../Components/Button.vue';
import Timeline from '../../Components/Timeline.vue';
import { Users, Video, Star, Share2 } from 'lucide-vue-next'
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage().props;
const activeTab = ref('timeline')

const createTimelineEntry = () => {
    // router.post('/timeline', {
    //     type: 'task',
    //     text: 'Test Timeline Entry',
    //     description: 'Testing the timeline functionality',
    //     project_id: page.projectId,
    //     details: {
    //         taskName: 'Test Task',
    //         status: 'In Progress'
    //     }
    // })
}

</script>
<template>
    <Header />
    <Sidebar />

    <Head title=" | Scrum Timeline" />
    <div class="min-h-screen bg-gray-50">
        <div class="ml-64 pt-16">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> >
                            Timeline</span></h1>

                    <div class="flex items-center -space-x-2">
                        <!-- <img v-for="member in teamMembers" :key="member.id" :src="member.avatar" class="w-8 h-8 rounded-full border-2 border-white"/><span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-sm text-gray-600">+3</span> -->
                    </div>
                    <button class="p-2 text-gray-600 hover:text-gray-800">
                        <Users class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <button>
                        <Share2 />
                    </button>
                    <button>
                        <Star />
                    </button>
                    <Button text="Create Meeting" variant="primary" :style="`flex px-4 py-2 items-center gap-2`">
                        <Video />
                    </Button>
                </div>
            </div>
            <div class="p-6">
                <!-- Replace the existing tab content section with this: -->
                <div class="flex gap-8 border-b mb-8">
                    <button @click="activeTab = 'timeline'" class="pb-2 text-gray-600"
                        :class="activeTab === 'timeline' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Timeline
                    </button>
                    <button @click="activeTab = 'gantt'" class="pb-2 text-gray-600"
                        :class="activeTab === 'gantt' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Gantt Chart
                    </button>
                </div>

                <!-- Content sections -->
                <div v-if="activeTab === 'timeline'">
                    <Timeline :projectId="page.projectId" />
                </div>
                <div v-else-if="activeTab === 'gantt'">
                    <!-- Gantt chart content here -->
                </div>

            </div>
        </div>
    </div>
</template>