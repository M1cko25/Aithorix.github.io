<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Create Task</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <XIcon class="w-5 h-5" />
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="handleSubmit" class="p-4">
                    <div class="space-y-4">
                        <!-- Task Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Title
                            </label>
                            <input id="title" v-model="taskData.title" type="text" required
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="Enter task title" />
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea id="description" v-model="taskData.description" rows="3"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="Enter task description"></textarea>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">
                                Priority
                            </label>
                            <select id="priority" v-model="taskData.priority"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <!-- Assignee -->
                        <div>
                            <label for="assignee" class="block text-sm font-medium text-gray-700">
                                Assignee
                            </label>
                            <select id="assignee" v-model="taskData.assignee"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Unassigned</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="dueDate" class="block text-sm font-medium text-gray-700">
                                Due Date
                            </label>
                            <input id="dueDate" v-model="taskData.dueDate" type="date"
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="handleSubmit"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border border-gray-300">
                            Create Task
                        </button>
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md border border-gray-300">
                            Cancel
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import { XIcon } from 'lucide-vue-next'

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    },
    columnId: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['close', 'create'])

// Sample users data - replace with your actual users
const users = ref([
    { id: '1', name: 'John Doe' },
    { id: '2', name: 'Jane Smith' },
    { id: '3', name: 'Bob Johnson' }
])

const taskData = ref({
    title: '',
    description: '',
    priority: '',
    assignee: '',
    dueDate: ''
})

const closeModal = () => {
    emit('close')
    resetForm()
}

const resetForm = () => {
    taskData.value = {
        title: '',
        description: '',
        priority: '',
        assignee: '',
        dueDate: ''
    }
}

const handleSubmit = () => {
    emit('create', {
        ...taskData.value,
        id: Date.now().toString(),
        columnId: props.columnId,
        createdAt: new Date().toISOString()
    })
    closeModal()
}
</script>