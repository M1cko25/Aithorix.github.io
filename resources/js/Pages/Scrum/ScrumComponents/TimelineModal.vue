<script setup>
import Modal from '@/Components/Modal.vue';
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

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

watch(() => props.selectedSprint, (newSprint) => {
    if (newSprint) {
        // Filter tasks for the selected sprint
        sprintTasks.value = props.sprintedtasks.filter(task => task.epic_id === newSprint.id);
    }
}, { immediate: true });

const handleClose = () => {
    emit('update:modelValue', false);
};
</script>

<template>
    <Modal 
        :modelValue="modelValue"
        @update:modelValue="handleClose"
        title="Sprint Details"
    >
        <div class="w-full h-full flex justify-between items-center">
            <div class="w-1/2 max-h-1/2 overflow-y-auto">
                <p class="font-semibold mb-4">Sprint Tasks</p>
                <div v-if="sprintTasks.length > 0">
                    <ul class="space-y-2">
                        <li v-for="task in sprintTasks" 
                            :key="task.id" 
                            class="flex flex-row gap-2 items-center py-3 px-4 border rounded-lg hover:bg-gray-50"
                        >
                            <div class="w-3 h-3 bg-success rounded-full"></div>
                            <div class="flex flex-col">
                                <p class="font-medium">{{ task.title }}</p>
                                <p class="text-sm text-gray-600">Status: {{ task.status }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-else class="text-center py-6 text-gray-500">
                    No tasks found for this sprint
                </div>
            </div>
            <div class="w-full h-96">
                <div class="flex flex-col gap-2 border border-dark h-full">
                    <img v-for="assignee in sprintedtasks.assignees" :key="assignee.id" 
                    :src="assignee.avatar" alt=""
                    class="w-10 h-10 rounded-full object-cover">
                </div>
            </div>
        </div>
    </Modal>
</template>