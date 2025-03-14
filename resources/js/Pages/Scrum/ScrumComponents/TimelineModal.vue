<script setup>
import Modal from '@/Components/Modal.vue';
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next';

const page = usePage().props;
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    selectedSprint: {
        type: Object,
        required: true
    }, 
    sprintedtasks: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const sprintTasks = ref([]);

// Compute assignee statistics
const assigneeStats = computed(() => {
    if (!sprintTasks.value.length) return [];
    
    const stats = {};
    sprintTasks.value.forEach(task => {
        if (task.assignees && task.assignees.length > 0) {
            task.assignees.forEach(assignee => {
                if (!stats[assignee.id]) {
                    stats[assignee.id] = {
                        id: assignee.id,
                        name: assignee.name,
                        avatar: assignee.avatar,
                        taskCount: 0
                    };
                }
                stats[assignee.id].taskCount++;
            });
        }
    });
    
    return Object.values(stats).sort((a, b) => b.taskCount - a.taskCount);
});

const hasAssignees = computed(() => {
    return sprintTasks.value.some(task => task.assignees && task.assignees.length > 0);
});

const topAssignee = computed(() => {
    return assigneeStats.value[0] || null;
});

watch(() => props.selectedSprint, (newSprint) => {
    if (newSprint) {
        // Filter tasks for the selected sprint
        sprintTasks.value = props.sprintedtasks.filter(task => task.epic_id === newSprint.id);
    }
}, { immediate: true });

const handleClose = () => {
    emit('update:modelValue', false);
};

const getStatusColor = (status) => {
    switch (status) {
        case 'To Do':
            return 'bg-light-blue text-blue';
        case 'In Progress':
            return 'bg-orange-100 text-orange-600';
        case 'Done':
            return 'bg-green-100 text-green-600';
        default:
            return 'bg-gray-100 text-gray-600';
    }
};
</script>

<template>
    <Modal 
        :modelValue="modelValue"
        @update:modelValue="handleClose"
        title="Sprint Details"
        class="w-full max-w-4xl"
    >
        <div class="flex flex-col gap-6 p-4">
            <!-- Sprint Info -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold mb-2">{{ selectedSprint?.title }}</h2>
                <p class="text-gray-600">Completed {{ selectedSprint?.dated }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tasks List -->
                <div>
                    <h3 class="font-semibold mb-4">Sprint Tasks</h3>
                    <div class="space-y-3">
                        <div v-if="sprintTasks.length > 0" class="max-h-[400px] overflow-y-auto">
                            <div v-for="task in sprintTasks" 
                                :key="task.id" 
                                class="flex flex-col gap-2 p-4 border rounded-lg hover:bg-gray-50"
                            >
                                <div class="flex items-center justify-between">
                                    <p class="font-medium">{{ task.title }}</p>
                                    <span :class="['px-2 py-1 rounded-full text-sm', getStatusColor(task.status)]">
                                        {{ task.status }}
                                    </span>
                                </div>
                                
                                <!-- Task Assignees -->
                                <div v-if="task.assignees && task.assignees.length > 0" class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Assignees:</span>
                                    <div class="flex -space-x-2">
                                        <img 
                                            v-for="assignee in task.assignees" 
                                            :key="assignee.id"
                                            :src="assignee.avatar" 
                                            :alt="assignee.name"
                                            :title="assignee.name"
                                            class="w-6 h-6 rounded-full border-2 border-white"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-gray-500">
                            No tasks found for this sprint
                        </div>
                    </div>
                </div>

                <!-- Assignee Stats -->
                <div>
                    <h3 class="font-semibold mb-4">Assignment Statistics</h3>
                    <div v-if="hasAssignees" class="space-y-6">
                        <!-- Top Assignee -->
                        <div v-if="topAssignee" class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Most Active Member</p>
                            <div class="flex items-center gap-3">
                                <img :src="topAssignee.avatar" :alt="topAssignee.name" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium">{{ topAssignee.name }}</p>
                                    <p class="text-sm text-gray-600">{{ topAssignee.taskCount }} tasks assigned</p>
                                </div>
                            </div>
                        </div>

                        <!-- All Assignees List -->
                        <div class="space-y-3">
                            <div v-for="stat in assigneeStats" 
                                :key="stat.id"
                                class="flex items-center justify-between p-3 border rounded-lg"
                            >
                                <div class="flex items-center gap-3">
                                    <img :src="stat.avatar" :alt="stat.name" class="w-8 h-8 rounded-full">
                                    <span>{{ stat.name }}</span>
                                </div>
                                <span class="text-sm font-medium">{{ stat.taskCount }} tasks</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center py-8 text-gray-500">
                        <User class="w-12 h-12 mb-2" />
                        <p>No assignments in this sprint</p>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>