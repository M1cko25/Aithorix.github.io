<script setup>
import Sidebar from '../../Components/Sidebar.vue';
import Header from '../../Components/Header.vue';
import Button from '../../Components/Button.vue';
import { Users, Video, Star, Share2, Upload, FilePenLine, ClipboardPlus, MessageCircle, CheckCircle } from 'lucide-vue-next'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Gantt from '../../Components/Gantt.vue'

const page = usePage().props;
const activeTab = ref('timeline')

const activities = ref([
    {
        id: 1,
        title: "You Completed Task 1",
        time: "10:00 AM",
        type: "upload",
        epic: "User Authentication Epic"
    }, 
    {
        id: 2,
        title: "You Completed Task 2",
        time: "11:00 AM",
        type: "comment",
        epic: "Dashboard Development Epic"
    }, {
        id: 3,
        title: "You Completed Task 3",
        time: "12:00 PM",
        type: "create",
        epic: "Payment Integration Epic"
    }, {
        id: 4,
        title: "You Completed Task 4",
        time: "1:00 PM",
        type: "update",
        epic: "Performance Optimization Epic"
    }
])

</script>
<template>
    <Sidebar />
    <Header/>
    <Head title=" | Timeline" />
    <div class="min-h-screen overflow-y-auto">
        <div class="ml-64 pt-16">
            <div class="p-6 flex items-center justify-between">
                <div class=" flex items-center gap-4">
                    <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> >
                            Timeline</span></h1>

                    <div class="flex items-center -space-x-2">
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
            <div>
                <div class="flex gap-8 border-b border-dark-gray mb-8">
                    <button @click="activeTab = 'timeline'" class="ml-6 pb-2 text-gray-600"
                        :class="activeTab === 'timeline' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Timeline
                    </button>
                    <button @click="activeTab = 'gantt'" class="pb-2 text-gray-600"
                        :class="activeTab === 'gantt' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Gantt Chart
                    </button>
                </div>

                <div v-if="activeTab == 'timeline'" v-for="activity in activities" :key="activity.id" class="flex flex-col gap-6">
                    <div class="py-2 px-6 h-fit justify-start items-start gap-2 inline-flex">
                        <div  class="flex flex-col w-full justify-between items-center inline-flex">
                            <div class="self-stretch justify-start items-center gap-6 inline-flex">
                                <div class="w-2.5 h-2.5 bg-success rounded-full"></div>
                                <div class="justify-center items-center gap-2.5 flex">
                                    <CheckCircle class="w-5 h-5 text-green-500" />
                                    <div class="">{{ activity.title }}</div>
                                </div>
                            </div>
                            <div class="self-stretch justify-start items-center inline-flex">
                                <div class="self-stretch px-1 justify-start items-center gap-2.5 flex">
                                    <div class="w-px self-stretch bg-success"></div>
                                </div>
                                <div class="px-10 py-3 justify-between w-full items-start flex overflow-hidden">
                                    <div class="flex flex-col">
                                        <div class="font-medium">{{ activity.epic }}</div>
                                        <div class="text-sm text-gray-500">{{ activity.time }}</div>
                                    </div>
                                    <Link href="#" class="text-blue underline">See Details</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab == 'gantt'" class="w-full h-full">
                  <Gantt></Gantt>
                </div>
            </div>
        </div>
    </div>
</template>