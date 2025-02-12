<script setup>
import { ref, onMounted } from 'vue'
import { XIcon, FileIcon, CheckIcon } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

const activeTab = ref('timeline')

const timelineItems = ref([])

const fetchTimelineItems = () => {
    router.get(`/timeline/{projectId}`, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (response) => {
            timelineItems.value = response.data
        }
    })
}

onMounted(() => {
    fetchTimelineItems()
})

const toggleDetails = (index) => {
    timelineItems.value[index].showDetails = !timelineItems.value[index].showDetails
}

</script>

<template>
    <div>
        <!-- Tabs -->


        <!-- Timeline Content -->
        <div v-if="activeTab === 'timeline'" class="relative">
            <!-- Vertical Line -->
            <div class="absolute left-[5px] top-0 bottom-0 w-[2px] bg-green-500"></div>

            <!-- Timeline Items -->
            <div class="space-y-8">
                <div v-for="(item, index) in timelineItems" :key="index">
                    <div class="relative pl-10">
                        <!-- Green Dot -->
                        <div class="absolute left-0 top-[10px] w-3 h-3 rounded-full bg-green-500"></div>

                        <!-- Content -->
                        <div class="flex justify-between items-start">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-gray-700">
                                    {{ item.text }}
                                </div>
                                <div class="text-gray-500">{{ item.timestamp }}</div>
                            </div>
                            <button @click="toggleDetails(index)" class="text-blue-600 hover:underline text-sm">
                                See Details
                            </button>
                        </div>
                    </div>

                    <!-- Details Panel -->
                    <div v-if="item.showDetails" class="mt-2 ml-10 p-4 bg-gray-50 rounded-md">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ item.detailTitle }}</h4>
                                    <p class="text-sm text-gray-500">{{ item.timestamp }}</p>
                                </div>
                                <button @click="toggleDetails(index)" class="text-gray-400 hover:text-gray-600">
                                    <XIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="text-sm text-gray-600">
                                {{ item.description }}
                            </div>

                            <div v-if="item.type === 'file'"
                                class="flex items-center gap-2 p-2 bg-white rounded border">
                                <FileIcon class="w-5 h-5 text-gray-400" />
                                <span class="text-sm text-gray-700">{{ item.fileName }}</span>
                            </div>

                            <div v-if="item.type === 'comment'" class="p-3 bg-white rounded border">
                                <p class="text-sm text-gray-600">{{ item.comment }}</p>
                            </div>

                            <div v-if="item.changes" class="space-y-2">
                                <h5 class="text-sm font-medium text-gray-700">Changes made:</h5>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li v-for="change in item.changes" :key="change" class="flex items-center gap-2">
                                        <CheckIcon class="w-4 h-4 text-green-500" />
                                        {{ change }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
