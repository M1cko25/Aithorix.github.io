<script setup>
import { GanttComponent as EjsGantt, ColumnsDirective as EColumns,
 ColumnDirective as EColumn, Edit, Selection, Toolbar } from '@syncfusion/ej2-vue-gantt';
 import '@syncfusion/ej2-material-theme/styles/material.css';
 import { ref, provide } from 'vue'
 import { Plus } from 'lucide-vue-next'

const data = ref([{
         TaskID: 1,
         TaskName: 'Planning',
         StartDate: new Date('02/03/2025'),
         EndDate: new Date('02/07/2025'),
         Progress: 20,
         Duration: 5,
         subtasks: [
             { TaskID: 2, TaskName: 'Plan timeline', StartDate: new Date('02/03/2025'), EndDate: new Date('02/07/2025'), Duration: 5, Progress: 100,},
             { TaskID: 3, TaskName: 'Plan budget', StartDate: new Date('02/03/2025'), EndDate: new Date('02/07/2025'), Duration: 5, Progress: 100, },
             { TaskID: 4, TaskName: 'Allocate resources', StartDate: new Date('02/03/2025'), EndDate: new Date('02/07/2025'), Duration: 5, Progress: 100, },
         ]
     },
     {
         TaskID: 6,
         TaskName: 'Design',
         StartDate: new Date('02/10/2025'),
         EndDate: new Date('02/14/2025'),
         Duration: 3,
         Progress: 26,
         subtasks: [
             { TaskID: 7, TaskName: 'Software Specification', StartDate: new Date('02/10/2025'), EndDate: new Date('02/12/2025'), Duration: 3, Progress: 60, },
             { TaskID: 8, TaskName: 'Develop prototype', StartDate: new Date('02/10/2025'), EndDate: new Date('02/12/2025'), duration: 3, Progress: 100,},
             { TaskID: 9, TaskName: 'Get approval from customer', startDate: new Date('02/13/2025'), EndDate: new Date('02/14/2025'), Duration: 2, Progress: 100, },
             { TaskID: 10, TaskName: 'Design Documentation', startDate: new Date('02/13/2025'), endDate: new Date('02/14/2025'), duration: 2, Progress: 100, },
         ]
     }, {
      TaskID: 11,
         TaskName: 'Implementation',
         StartDate: new Date('02/17/2025'),
         EndDate: new Date('02/27/2025'),
         Duration: 11,
         Progress: 50,
         subtasks: [
             { TaskID: 12, TaskName: 'Software Development', StartDate: new Date('02/17/2025'), EndDate: new Date('02/27/2025'), Duration: 11, Progress: 50, },
             { TaskID: 13, TaskName: 'Prepare documentation', StartDate: new Date('02/17/2025'), EndDate: new Date('02/27/2025'), Duration: 11, Progress: 50, },
         ]
     }, {
      TaskID: 14,
         TaskName: 'Quality Assurance',
         StartDate: new Date('02/17/2025'),
         EndDate: new Date('02/27/2025'),
         Duration: 11,
         Progress: 50,
         subtasks: [
             { TaskID: 15, TaskName: 'Bug fix', StartDate: new Date('02/17/2025'), EndDate: new Date('02/27/2025'), Duration: 11, Progress: 50, },
             { TaskID: 16, TaskName: 'Follow-up', StartDate: new Date('02/17/2025'), EndDate: new Date('02/27/2025'), Duration: 11, Progress: 50, },
         ]
     }
    ])
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
//     const addSubTask = (parentTask) => {
//   const newTaskID = tasks.value.length + 1;
//   tasks.value.push({
//     TaskID: newTaskID,
//     TaskName: `New Task ${newTaskID}`,
//     StartDate: new Date(),
//     Duration: 3,
//     parentID: parentTask.TaskID, // Assign as a subtask
//   });
// };

provide("gantt", [Edit, Selection, Toolbar]);
</script>

<template>
  <ejs-gantt :dataSource='data' :treeColumnIndex='1' child='subtasks' 
  :taskFields='taskFields' height="100%" :toolbar="toolbarOptions"
  :editSettings="editSettings" :labelSettings="labelSettings" :allowTaskbarEditing="true"
  :allowDragAndDrop="true" :allowResizing="true">
        <e-columns class="border border-dark">
            <e-column field='TaskName' headerText='Task Name' textAlign='Left' width=200></e-column>
            <e-column field='StartDate' headerText='Start Date' textAlign='Right' format='yMd' width=90></e-column>
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