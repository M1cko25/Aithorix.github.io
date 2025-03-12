<script setup>
import Sidebar from '../../Components/SideBar.vue';
import Header from '@/Components/Header.vue';
import Button from '@/Components/Button.vue';
import { Users, Video, Star, Share2, CircleCheck } from 'lucide-vue-next'
import { ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Gantt from '@/Components/Gantt.vue'
import AddTaskModal from './ScrumComponents/AddTaskModal.vue'
import DeleteTaskModal from './ScrumComponents/DeleteTaskModal.vue'
import noData from '@/assets/NoDataIllustration.svg'
import TimelineModal from './ScrumComponents/TimelineModal.vue'
import { is } from 'date-fns/locale';

const page = usePage().props;
const activeTab = ref('gantt')
const taskNum = ref(0);
const sprint_tasks = ref(page.sprintTasks);
const backlogs = ref(page.backlogs);
const sprints = ref(page.sprints);
const data = ref([])
const isSidebarOpen = ref(true);

const activities = ref([]);

sprints.value.forEach(sprint => {
  if (sprint.status == "Completed") {
    activities.value.push({
      id: sprint.id,
      title: sprint.name,
      time: sprint.dated,
    })
  }
})

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
const isDetailsModalOpen = ref(false);
const activitySelected = ref(null);

const handleTimelineModalClose = () => {
    isDetailsModalOpen.value = false;
    activitySelected.value = null;
};

const handleSeeDetails = (activity) => {
    activitySelected.value = activity;
    isDetailsModalOpen.value = true;
};
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

    <TimelineModal 
    v-model="isDetailsModalOpen"
    :selectedSprint="activitySelected"
    :sprintedtasks="backlogs"
    @update:modelValue="handleTimelineModalClose"
/>

     />
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
                <div v-if="sprints.length > 0 && sprints.find(sprint => sprint.status == 'Completed')" class="flex gap-8 border-b border-dark-gray mb-8">
                    <button @click="activeTab = 'gantt'" class="pb-2 text-gray-600"
                        :class="activeTab === 'gantt' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Gantt Chart
                    </button>
                    <button @click="activeTab = 'timeline'" class="ml-6 pb-2 text-gray-600"
                        :class="activeTab === 'timeline' ? 'border-b-2 border-gray-900 text-gray-900' : ''">
                        Timeline
                    </button>
                </div>

                <div v-if="activeTab == 'gantt' && sprints.length > 0" class="w-full h-full flex flex-col gap-4">
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

                <div v-if="activeTab == 'timeline'" v-for="activity in activities" :key="activity.id" class="flex flex-col gap-6">
                    <div class="py-2 px-6 h-fit justify-start items-start gap-2 inline-flex">
                        <div  class="flex flex-col w-full justify-between items-center inline-flex">
                            <div class="self-stretch justify-start items-center gap-6 inline-flex">
                                <div class="w-2.5 h-2.5 bg-success rounded-full"></div>
                                <div class="justify-center items-center gap-2.5 flex">
                                    <CircleCheck class="w-4 h-4" />
                                    <div class="">{{ activity.title }}</div>
                                </div>
                            </div>
                            <div class="self-stretch justify-start items-center inline-flex">
                                <div class="self-stretch px-1 justify-start items-center gap-2.5 flex">
                                    <div class="w-px self-stretch bg-success"></div>
                                </div>
                                <div class="px-10 py-3 justify-between w-full items-start flex overflow-hidden">
                                    <div class="">{{ activity.time }}</div>
                                    <button 
                                        @click="handleSeeDetails(activity)" 
                                        class="text-blue underline"
                                    >
                                        See Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="sprints == 0" class="flex flex-col items-center justify-center h-full ">
                  <img :src="noData" alt="No Data" class="w-32 h-32">
                  <p class="text-dark text-md">No Sprints available</p>
                  <p class="text-dark text-md">Start a sprint to get started</p>
                  <Link class="btn-primary mt-4" :href="`/scrum/backlog?id=` + page.projectDetails.id">Start Sprint</Link>
                </div>
            </div>
        </div>
    </div>
</template>