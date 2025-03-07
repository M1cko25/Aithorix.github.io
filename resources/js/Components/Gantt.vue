<script setup>
import { GanttComponent as EjsGantt, ColumnsDirective as EColumns,
 ColumnDirective as EColumn, Edit, Selection, Toolbar } from '@syncfusion/ej2-vue-gantt';
 import '@syncfusion/ej2-material-theme/styles/material.css';
 import { ref, provide } from 'vue'
 import { Plus } from 'lucide-vue-next';
 import { usePage } from '@inertiajs/vue3';

const data = ref([])
const page = usePage().props;

const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return `0${d.getMonth() + 1}/0${d.getDate()}/${d.getFullYear()}`
}

const epicSubtasks = (epicId, startDate, endDate, duration) => {
  let subtasks = []
  page.backlogs.forEach(task => {
    if (task.epic_id == epicId) {
      subtasks.push({
        TaskID: task.id, 
        TaskName: task.title,
        StartDate: startDate,
        EndDate: endDate,
        Duration: duration,
        Progress: 0,
      });
    }
  })
  return subtasks;
}
page.epics.forEach(epic => {
  const startDate = new Date(epic.start_date);
  const endDate = new Date(epic.end_date);
  const duration = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)); 
  console.log(epic.progress_percent);
  data.value.push({
    TaskID: epic.id,
    TaskName: epic.name,
    StartDate: epic.start_date,
    EndDate: epic.end_date,
    Duration: duration,
    Progress: epic.progress_percent,
    subtasks : epicSubtasks(epic.id, epic.start_date, epic.end_date, duration)
  })
})
// {
//          TaskID: 1,
//          TaskName: 'Planning',
//          StartDate: new Date('02/03/2025'),
//          EndDate: new Date('02/07/2025'),
//          Progress: 20,
//          Duration: 5,
//          subtasks: [
//              { TaskID: 2, TaskName: 'Plan timeline', StartDate: new Date('02/03/2025'), EndDate: new Date('02/07/2025'), Duration: 5, Progress: 100,},
//              { TaskID: 3, TaskName: 'Plan budget', StartDate: new Date('02/03/2025'), EndDate: new Date('02/07/2025'), Duration: 5, Progress: 100, },
//              { TaskID: 4, TaskName: 'Allocate resources', StartDate: new Date('02/03/2025'), EndDate: new Date('02/07/2025'), Duration: 5, Progress: 100, },
//          ]
//      },
let taskFields = ref({
        id: 'TaskID',
        name: 'TaskName',
        startDate: 'StartDate',
        endDate: 'EndDate',
        duration: 'Duration',
        progress: 'Progress',
        child: 'subtasks',
})
const toolbarOptions = ref(['Add', 'Edit', 'Update', 'Delete', 'Cancel', 'ExpandAll', 'CollapseAll']);
const editSettings = ref({
    allowEditing: true,
    allowTaskbarEditing: true,
    allowDeleting: true,
    allowDependencyEditing: true,
    showDeleteConfirmDialog: true,
    allowAdding: true,
    mode: 'Normal'
})

const labelSettings = {
  rightLabel: '',
  taskLabel: '${taskData.TaskName}'
};


provide("gantt", [Edit, Selection, Toolbar]);
</script>

<template>
  <ejs-gantt :dataSource='data' :treeColumnIndex='1' child='subtasks' 
  :taskFields='taskFields' height="100%" :toolbar="toolbarOptions"
  :editSettings="editSettings" :labelSettings="labelSettings" :allowTaskbarEditing="true"
  :allowDragAndDrop="true" :allowResizing="true">
        <e-columns class="border border-dark">
            <e-column field='TaskID' headerText='Task ID' textAlign='Left' width=70></e-column>
            <e-column field='TaskName' headerText='Task Name' textAlign='Left' width=200></e-column>
            <e-column field='StartDate' headerText='Start Date' textAlign='Right' format='yMd' width=90></e-column>
            <e-column field='EndDate' headerText='End Date' textAlign='Right' format='yMd' width=90></e-column>
            <e-column field='Duration' headerText='Duration' textAlign='Right' width=80></e-column>
       </e-columns>
    </ejs-gantt>
</template>

<style>
.e-gantt-parent-taskbar {
  background-color: #09B1EC !important;
  outline: none !important;
}
.e-gantt-parent-progressbar {
  outline: none !important;
  background: none !important;
}
.e-gantt-child-taskbar {
  background: #D9D9D9 !important;
  outline: none !important;
}
.e-gantt-child-progressbar {
  background: none !important;
  border-top: 3px solid #09B1EC !important;
}
.e-gantt-child-taskbar .e-task-label {
  color: #000 !important;
  font-size: 0.6rem !important;
}
</style>