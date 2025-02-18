<script setup>
import { Ellipsis, ClipboardList, Bug, Bookmark, Plus } from "lucide-vue-next";
import Button from "../Components/Button.vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import draggable from "vuedraggable";

const page = usePage().props;

const props = defineProps({
    title: String,
    tasks: Array,
    isCreatingTask: Boolean,
    createTask: Function,
    collumn: String,
    taskNum: Number
});

const taskTypes = ["story", "bug", "task"];
const newTaskTitle = ref("");
const selectedType = ref("task");

const emit = defineEmits(["create-new-task", "update:tasks"]);

const handleCreateTask = () => {
    if (newTaskTitle.value.length > 0) {
        emit("create-new-task", {
        title: newTaskTitle.value,
        type: selectedType.value,
        key: page.projectDetails.key + "-" + props.taskNum,
        collumn: props.collumn,
    });
    newTaskTitle.value = "";
    }
};
</script>

<template>
    <div class="w-80 bg-gray-100 rounded-lg p-4 h-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-medium">{{ title }}</h3>
            <span class="text-sm text-gray-500">{{ tasks.length }}</span>
        </div>

        <button
            @click="createTask"
            class="w-full p-3 bg-white rounded-lg border border-gray-200 text-left text-gray-600 hover:bg-gray-50 flex items-center gap-2"
        >
            <Plus class="w-4 h-4" />
            Create Task
        </button>

        <draggable
            v-bind="tasks"
            :list="tasks"
            group="tasks"
            item-key="id"
            class="mt-4 space-y-3"
            @end="$emit('update:tasks', tasks)"
        >
            <template #item="{ element: task }">
                <div class="p-4 bg-white rounded-lg shadow-sm">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-row justify-between items-center">
                            <div class="flex flex-row items-center gap-2">
                                <ClipboardList
                                    v-if="task.type == 'task'"
                                    class="w-4 h-4"
                                />
                                <Bug
                                    v-else-if="task.type == 'bug'"
                                    class="w-4 h-4"
                                />
                                <Bookmark
                                    v-if="task.type == 'story'"
                                    class="w-4 h-4"
                                />
                                <h1 class="truncate">{{ task.title }}</h1>
                            </div>
                            <button><Ellipsis /></button>
                        </div>
                        <p
                            class="text-xs px-2 py-1 bg-blue w-fit rounded-full text-light"
                        >
                            {{ task.key }}
                        </p>
                    </div>
                </div>
            </template>
        </draggable>

        <div
            v-if="isCreatingTask"
            class="p-4 bg-white rounded-lg shadow-sm flex flex-col gap-3"
        >
            <input
                v-model="newTaskTitle"
                type="text"
                class="outline-none truncate"
                name="title"
                placeholder="Task to be done"
            />
            <div class="flex items-center justify-between">
                <select v-model="selectedType">
                    <option v-for="type in taskTypes" :value="type">
                        {{ type }}
                    </option>
                </select>
                <Button :click="handleCreateTask" class="btn-primary"
                    >Create</Button
                >
            </div>
        </div>
    </div>
</template>
