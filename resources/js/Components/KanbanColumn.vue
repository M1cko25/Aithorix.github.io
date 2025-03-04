<script setup>
import { Ellipsis, ClipboardList, Bug, Bookmark, Plus } from "lucide-vue-next";
import Button from "../Components/Button.vue";
import { ref, onMounted, onUnmounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import draggable from "vuedraggable";

const page = usePage().props;
const taskTypes = ref([{
  name: 'Task',
  icon: ClipboardList
},{
  name: 'Bug',
  icon: Bug
},{
  name: 'Story',
  icon: Bookmark
}])

const props = defineProps({
    title: String,
    tasks: Array,
    isCreatingTask: Boolean,
    createTask: Function,
    columnId: String,
    taskNum: Number
});

const emit = defineEmits(['create-new-task', 'update:tasks', 'taskMoved']);

const newTaskTitle = ref("");
const isOpen = ref(false);
const selectedType = ref(taskTypes.value[0]);

const handleCreateTask = () => {
    if (newTaskTitle.value.trim().length > 0) {
        emit("create-new-task", {
            title: newTaskTitle.value.trim(),
            type: selectedType.value,
            columnId: props.columnId
        });
        newTaskTitle.value = "";
        isOpen.value = false;
    }
};

const handleKeyPress = (event) => {
    if (event.key === 'Enter' && newTaskTitle.value.trim().length > 0) {
        handleCreateTask();
    }
}

const selectType = (type) => {
    selectedType.value = type;
    isOpen.value = false;
}

// Add click outside handler for type selector
const handleTypeClickOutside = (event) => {
    const typeSelector = document.querySelector('.type-selector');
    if (typeSelector && !typeSelector.contains(event.target)) {
        isOpen.value = false;
    }
}

const handleCreateClick = (event) => {
    event.stopPropagation();
    props.createTask();
}

onMounted(() => {
    document.addEventListener('click', handleTypeClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleTypeClickOutside);
});

const handleDragAdd = (evt) => {
    const task = evt.item.__draggable_context.element;
    emit('taskMoved', task, props.columnId);
}
</script>

<template>
    <div class="w-80 bg-gray-100 rounded-lg p-4 h-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-medium">{{ title }}</h3>
            <span class="text-sm text-gray-500">{{ tasks.length }}</span>
        </div>

        <button
            @click="handleCreateClick"
            class="create-task-button w-full p-3 bg-white rounded-lg border border-gray-200 text-left text-gray-600 hover:bg-gray-50 flex items-center gap-2"
        >
            <Plus class="w-4 h-4" />
            Create Task
        </button>

        <div
            v-if="isCreatingTask"
            class="task-creation-area p-4 bg-white rounded-lg shadow-sm flex flex-col gap-3 mt-4"
            @click.stop
        >
            <input
                v-model="newTaskTitle"
                type="text"
                class="outline-none truncate px-3 py-2 border rounded-lg"
                name="title"
                placeholder="Task title"
                @keypress="handleKeyPress"
                ref="titleInput"
                @click.stop
            />
            <div class="flex items-center justify-between">
                <div class="relative z-20 type-selector" @click.stop>
                    <button @click.stop="isOpen = !isOpen" class="flex items-center gap-2 px-4 py-2 border rounded-lg">
                        <component :is="selectedType.icon" class="w-5 h-5" />
                        <span>{{ selectedType.name }}</span>
                    </button>

                    <div v-if="isOpen" class="absolute z-10 mt-1 bg-white border rounded-lg shadow-lg">
                        <button 
                            v-for="type in taskTypes" 
                            :key="type.name"
                            @click.stop="selectType(type)"
                            class="flex flex-row w-full items-center gap-2 px-4 py-2 hover:bg-gray-50"
                        >
                            <component :is="type.icon" class="w-5 h-5" />
                            <span>{{ type.name }}</span>
                        </button>
                    </div>
                </div>
                <Button @click.stop="handleCreateTask" class="btn-primary">Create</Button>
            </div>
        </div>

        <draggable
            :list="tasks"
            group="tasks"
            item-key="id"
            class="mt-4 space-y-3 min-h-60"
            :force-fallback="true"
            :animation="150"
            ghost-class="ghost-card"
            drag-class="drag-card"
            @add="handleDragAdd"
        >
            <template #item="{ element: task }">
                <div class="p-4 cursor-grab bg-white rounded-lg shadow-sm">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-row justify-between items-center">
                            <div class="flex flex-row items-center gap-2">
                                <ClipboardList
                                    v-if="task.type == 'Task'"
                                    class="w-4 h-4"
                                />
                                <Bug
                                    v-else-if="task.type == 'Bug'"
                                    class="w-4 h-4"
                                />
                                <Bookmark
                                    v-else-if="task.type == 'Story'"
                                    class="w-4 h-4"
                                />
                                <h1 class="truncate">{{ task.title }}</h1>
                            </div>
                            <button><Ellipsis /></button>
                        </div>
                        <p class="text-xs px-2 py-1 bg-blue w-fit rounded-full text-light">
                            {{ page.projectDetails.key + "-" + task.id }}
                        </p>
                        <div v-if="task.assignees && task.assignees.length > 0" class="flex -space-x-2">
                            <img
                                v-for="assignee in task.assignees" 
                                :key="assignee.id"
                                :src="assignee.avatar"
                                :alt="assignee.name"
                                class="w-6 h-6 rounded-full border-2 border-white"
                            />
                        </div>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>
<style scoped>
.ghost-card {
    opacity: 0.5;
    background: #F3F4F6;
    border: 2px dashed #9CA3AF;
    user-select: none;
}

.drag-card {
    opacity: 0.9;
    transform: rotate(3deg);
    user-select: none;
}

/* Add this class to prevent text selection in the entire kanban column */
.w-80 {
    user-select: none;
}
</style>
