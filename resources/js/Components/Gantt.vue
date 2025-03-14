<script setup>
import { GanttComponent as EjsGantt, ColumnsDirective as EColumns,
 ColumnDirective as EColumn, Edit, Selection, Toolbar, Resize, DayMarkers, Gantt } from '@syncfusion/ej2-vue-gantt';
 import '@syncfusion/ej2-material-theme/styles/material.css';
import { ref, provide, watch } from 'vue'
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import TaskModal from '../Pages/Scrum/ScrumComponents/TaskModal.vue'

const page = usePage().props;

const props = defineProps({
  data: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['rowSelected']);

const ganttData = ref(props.data);

// Watch for changes in props.data
watch(() => props.data, (newData) => {
  ganttData.value = [...newData];
}, { deep: true });

const taskFields = ref({
             id: 'TaskID',
             name: 'TaskName',
             startDate: 'StartDate',
             endDate: 'EndDate',
             progress: 'Progress',
  status: 'Status',
             child: 'subtasks',
  duration: 'Duration'
     })

const toolbarOptions = ref(['Edit', 'Update', 'Cancel', 'ExpandAll', 'CollapseAll']);
     const editSettings = ref({
  AllowEditing: true,
         allowDeleting: true,
         allowDependencyEditing: true,
         showDeleteConfirmDialog: true,
         allowAdding: true,
  allowResizing: true,
  allowTaskbarEditing: true,
  allowDragAndDrop: true,
  allowSelection: true,
  mode: 'Auto'
     })

     const labelSettings = {
  rightLabel: '${Progress}%',
        taskLabel: '${taskData.TaskName}'
    };

const rowSelected = (args) => {
  emit('rowSelected', args.data.TaskName);
}

const taskbarEdited = (args) => {
    const task = page.backlogs.find(backlog => backlog.title + " (" + backlog.key + ")" == args.data.TaskName);
    axios.post('/scrum/sprint-task-update', {
      taskId: task.id,
      start_date: args.data.StartDate,
      end_date: args.data.EndDate,
      duration: args.data.Duration,
      projectId: page.projectDetails.id,
      epicId: task.epic_id
    }).catch(error => {
      console.log(error);
    })
}

const isTaskModalOpen = ref(false)
const selectedTask = ref(null)

const recordDoubleClick = (args) => {
  const task = page.backlogs.find(backlog => backlog.title + " (" + backlog.key + ")" == args.rowData.TaskName);
  if (task) {
    selectedTask.value = task;
    isTaskModalOpen.value = true;
  }
}

const handleTaskUpdate = (updatedTask) => {
  // Find the sprint in data
  const sprintIndex = ganttData.value.findIndex(item => 
    item.subtasks && item.subtasks.some(task => 
      task.TaskName === updatedTask.title + " (" + updatedTask.key + ")"
    )
  );
  
  if (sprintIndex !== -1) {
    // Find and update the task in subtasks
    const taskIndex = ganttData.value[sprintIndex].subtasks.findIndex(task => 
      task.TaskName === selectedTask.value.title + " (" + selectedTask.value.key + ")"
    );
    
    if (taskIndex !== -1) {
      // Update task data
      ganttData.value[sprintIndex].subtasks[taskIndex] = {
        ...ganttData.value[sprintIndex].subtasks[taskIndex],
        TaskName: updatedTask.title + " (" + updatedTask.key + ")",
        Status: updatedTask.status,
        Progress: updatedTask.status === 'Done' ? 100 : updatedTask.status === 'In Progress' ? 50 : 0
      };
      
      // Create a new array reference to trigger reactivity
      ganttData.value = [...ganttData.value];
    }
  }
}

const taskbarEditing = (args) => {
  const taskSprint = page.backlogs.find(backlog => backlog.title + " (" + backlog.key + ")" == args.data.TaskName);

  if (taskSprint) {
    const sprint = page.sprints.find(sprint => sprint.epic_id == taskSprint.epic_id);
    if (sprint.status != "Active") {
      args.cancel = true;
    }
  }
}

provide("gantt", [Edit, Selection, Toolbar, Resize, DayMarkers]);
Gantt.Inject(Edit, Selection, Toolbar, Resize, DayMarkers);

</script>

<template>
  <TaskModal
    v-model:isOpen="isTaskModalOpen"
    :task="selectedTask"
    :epicSelected="page.epics.find(epic => epic.id === selectedTask?.epic_id)"
    @update:task="handleTaskUpdate"
  />
  <ejs-gantt 
    :key="JSON.stringify(ganttData)"
    :dataSource="ganttData" 
    :treeColumnIndex="1" 
    child="subtasks"
    :taskFields="taskFields" 
    height="800"
    :toolbar="toolbarOptions"
    :editSettings="editSettings" 
    :labelSettings="labelSettings" 
    :allowDragAndDrop="true" 
    :allowResizing="true"
    :allowTaskbarEditing="true"
    :allowEditing="true"
    :highlightWeekends="true"
    :workWeek="['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']"
    @taskbarEdited="taskbarEdited"
    @rowSelected="rowSelected"
    @recordDoubleClick="recordDoubleClick"
    @taskbarEditing="taskbarEditing"
  >
      <e-columns class="border border-dark">
      <e-column field='TaskID' headerText='#' textAlign='Left' width=70></e-column>
      <e-column field='TaskName' headerText='Sprints' textAlign='Left' width=200></e-column>
            <e-column field='StartDate' headerText='Start Date' textAlign='Right' format='yMd' width=90></e-column>
      <e-column field='EndDate' headerText='End Date' textAlign='Right' format='yMd' width=90></e-column>
      <e-column field='Status' headerText='Status' textAlign='Right' width=80></e-column>
      <e-column field='Progress' headerText='Progress' textAlign='Right' width=80></e-column>
       </e-columns>
    </ejs-gantt>
</template>

<style>
.e-gantt-parent-taskbar {
  background-color: #09B1EC !important;
  outline: none !important;
}
.e-gantt-parent-progressbar {
  background-color: #0077B6 !important;
  outline: none !important;
}
.e-gantt-child-taskbar {
  background: #D9D9D9 !important;
  outline: none !important;
}
.e-gantt-child-progressbar {
  background: #09B1EC !important;
}
.e-gantt-child-taskbar .e-task-label {
  color: #000 !important;
  font-size: 0.6rem !important;
}
.e-gantt .e-gantt-chart .e-gantt-parent-taskbar .e-task-label {
  color: white !important;
}
</style>