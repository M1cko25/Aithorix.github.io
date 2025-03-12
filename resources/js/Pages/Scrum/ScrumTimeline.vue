<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Header from '@/Components/Header.vue';
import Button from '@/Components/Button.vue';
import { Users, Video, Star, Share2, Upload, FilePenLine, ClipboardPlus, MessageCircle } from 'lucide-vue-next'
import { ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Gantt from '@/Components/Gantt.vue'
import AddTaskModal from './ScrumComponents/AddTaskModal.vue'
import DeleteTaskModal from './ScrumComponents/DeleteTaskModal.vue'

const page = usePage().props;
const activeTab = ref('timeline')
const taskNum = ref(0);
const sprint_tasks = ref(page.sprintTasks);
const sprints = ref(page.sprints);
const data = ref([])
const isSidebarOpen = ref(true);

const activities = ref([
    {
        id: 1,
        title: "You upload a file",
        time: "10:00 AM",
        type: "upload",
    }, 
    {
        id: 2,
        title: "You commented to a task",
        time: "11:00 AM",
        type: "comment",
    }, {
        id: 3,
        title: "You created a new task",
        time: "12:00 PM",
        type: "create",
    }, {
        id: 4,
        title: "You update a task",
        time: "1:00 PM",
        type: "update",
    }
])

const sprintTasks = (sprintId) => {
  let subtasks = []
  sprint_tasks.value.forEach(task => {
    if (task.sprint_id == sprintId) {
      page.backlogs.forEach(backlog => {
        if (backlog.id == task.backlog_id) {
          subtasks.push({
            TaskID: taskNum.value += 1,
            TaskName: backlog.title + " (" + backlog.key + ")",
            StartDate: task.start_date,
            EndDate: task.end_date,
            Status: backlog.status,
            Progress: backlog.status == 'Done' ? 100 : backlog.status == 'In Progress' ? 50 : backlog.status == 'To Do' ? 0 : 100,
            sprintTaskId: task.id,
          })
        }
      })
    }
  })
  return subtasks;
}

[...sprints.value].sort((a, b) => a.order - b.order).forEach(epic => {
 data.value.push({
    TaskID: taskNum.value += 1,
    TaskName: epic.name,
    StartDate: epic.start_date,
    EndDate: epic.end_date,
    Status: epic.status,
    Progress: epic.progress_percent || 0,
    isSubtask: false,
    subtasks: sprintTasks(epic.id)
  })
})

const rowSelected = ref('');
const sprintSelected = computed(() => {
  const sprint = page.sprints.find(sprint => sprint.name === rowSelected.value);
  if (sprint && sprint.status == "Active") {
    return {
      ...sprint,
      project_id: page.projectDetails.id
    };
  }
  return null;
});
const taskSelected = computed(() => {
  const task = page.backlogs.find(backlog => backlog.title + " (" + backlog.key + ")" == rowSelected.value);
  if (task ) {
    const sprint = page.sprints.find(sprint => sprint.epic_id == task.epic_id);
    if (sprint && sprint.status == "Active") {
      return {
        ...task,
        project_id: page.projectDetails.id
      };
    }
  }
  return null;
})

const isAddTaskModalOpen = ref(false)
const isDeleteTaskModalOpen = ref(false)

const handleTaskAdded = (newTask) => {
  console.log('New task received:', newTask);
  
  // Find the sprint in data
  const sprintIndex = data.value.findIndex(item => item.TaskName === sprintSelected.value.name);
  console.log('Sprint index:', sprintIndex);
  
  if (sprintIndex !== -1) {
    // Create a new array for subtasks if it doesn't exist
    if (!data.value[sprintIndex].subtasks) {
      data.value[sprintIndex].subtasks = [];
    }
    
    // Add the new task to the sprint's subtasks
    const newSubtask = {
      TaskID: taskNum.value += 1,
      TaskName: newTask.backlog.title + " (" + newTask.backlog.key + ")",
      StartDate: newTask.start_date,
      EndDate: newTask.end_date,
      Status: newTask.backlog.status,
      Progress: 0,
      sprintTaskId: newTask.id
    };
    
    console.log('Adding new subtask:', newSubtask);
    
    // Create a new array reference for data to trigger reactivity
    data.value = [...data.value];
    data.value[sprintIndex].subtasks.push(newSubtask);
    sprint_tasks.value.push(newTask);
  }
}

const handleDeleteTask = () => {
  if (!taskSelected.value) return;
  
  axios.post('/scrum/backlog-delete', {
    id: taskSelected.value.id,
    epicId: taskSelected.value.epic_id,
    title: taskSelected.value.title,
    projectId: page.projectDetails.id
  })
  .then(() => {
    // Find the sprint in data
    const sprintIndex = data.value.findIndex(item => 
      item.subtasks && item.subtasks.some(task => 
        task.TaskName === taskSelected.value.title + " (" + taskSelected.value.key + ")"
      )
    );
    
    if (sprintIndex !== -1) {
      // Find and remove the task from subtasks
      const taskIndex = data.value[sprintIndex].subtasks.findIndex(task => 
        task.TaskName === taskSelected.value.title + " (" + taskSelected.value.key + ")"
      );
      
      if (taskIndex !== -1) {
        data.value[sprintIndex].subtasks.splice(taskIndex, 1);
        // Create a new array reference to trigger reactivity
        data.value = [...data.value];
      }
    }
    
    isDeleteTaskModalOpen.value = false;
    rowSelected.value = '';
  })
  .catch(error => {
    console.error('Error deleting task:', error);
  });
}
const logoDisplayed = ref(true);
</script>
<template>
    <Sidebar @sidebarCollapsed="(value) => { isSidebarOpen = value }" @logoAppear="(value) => logoDisplayed = value"/>
    <Header :logoDisplay="logoDisplayed"/>
    <AddTaskModal
        v-model:isOpen="isAddTaskModalOpen"
        :sprint="sprintSelected"
        @taskAdded="handleTaskAdded"
    />
    <DeleteTaskModal
        v-model:isOpen="isDeleteTaskModalOpen"
        :task="taskSelected"
        @confirm="handleDeleteTask"
    />
    <Head title=" | Timeline" />
    <div class="min-h-screen overflow-y-auto">
        <div class="transition-all duration-300 ease-in-out pt-16" :class="`${isSidebarOpen ? 'ml-16' : 'ml-64'}`">
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
                                    <Upload v-if="activity.type === 'upload'" class="w-5 h-5"/>
                                    <ClipboardPlus v-if="activity.type === 'create'" class="w-5 h-5" />
                                    <FilePenLine v-if="activity.type === 'update'" class="w-5 h-5" />
                                    <MessageCircle v-if="activity.type === 'comment'" class="w-5 h-5" />
                                    <div class="">{{ activity.title }}</div>
                                </div>
                            </div>
                            <div class="self-stretch justify-start items-center inline-flex">
                                <div class="self-stretch px-1 justify-start items-center gap-2.5 flex">
                                    <div class="w-px self-stretch bg-success"></div>
                                </div>
                                <div class="px-10 py-3 justify-between w-full items-start flex overflow-hidden">
                                    <div class="">{{ activity.time }}</div>
                                    <Link href="#" class="text-blue underline">See Details</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="activeTab == 'gantt'" class="w-full h-full flex flex-col gap-4">
                    <div class="w-full flex items-center justify-end">
                        <button 
                            v-if="sprintSelected" 
                            class="btn-primary"
                            @click="isAddTaskModalOpen = true"
                        >
                            Add Task
                        </button>
                        <button 
                            v-if="taskSelected" 
                            class="btn-primary ml-2"
                            @click="isDeleteTaskModalOpen = true"
                        >
                            Remove Task
                        </button>
                    </div>
                    <div class="h-full w-full">
                        <Gantt 
                        :data="data"
                        :key="data.length"
                        @rowSelected="(value) => rowSelected = value" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>