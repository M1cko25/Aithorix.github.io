<template>
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-2">
        <div class="flex flex-col gap-2">
            <!-- Task Title -->
            <h3 class="text-sm font-medium text-gray-900">{{ task.title }}</h3>

            <!-- Task Labels -->
            <div class="flex gap-2">
                <span v-if="task.priority" class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                    {{ task.priority }}
                </span>
            </div>

            <!-- Task Footer -->
            <div class="flex justify-between items-center mt-2">
                <div class="flex items-center gap-2">
                    <!-- Add any additional task metadata here -->
                </div>
                <div v-if="task.assignee" class="flex items-center">
                    <img :src="task.assignee.avatar || '/placeholder.svg'" :alt="task.assignee.name"
                        class="w-6 h-6 rounded-full" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    task: {
        type: Object,
        required: true
    }
})
</script>