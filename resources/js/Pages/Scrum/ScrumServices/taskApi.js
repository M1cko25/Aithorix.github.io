import axios from 'axios'
import { taskCountsUpdate } from '../ScrumComposables/UseTasks';

export function updateTaskStatus(task, taskCounts, epicSelected, projectDetails) {
    axios.post('/scrum/backlog-status-update', {
        id: task.id,
        status: task.status,
        projectId: projectDetails.id
    })
    .then(response => {
        taskCountsUpdate(taskCounts, epicSelected);
    })
    .catch(error => {
        console.error('Error updating task status:', error);
    });
}

export function createTask(newTask, taskCounts, selectedType, epics, epicSelected, projectDetails) {
    if (newTask.trim().length > 0) {
      let taskProcessing = false;
      const activeEpic = epics.find(epic => epic.isActive);
      if (!taskProcessing) {
        axios.post('/scrum/backlog-create', {
          title: newTask,
          description: '',
          priority: 'Low',
          status: 'To Do',
          type: selectedType.name,
          epicId: activeEpic.id,
          order: activeEpic.order + 1,
          projectId: projectDetails.id
        })
        .then(response => {
          console.log(response.data);
          epicSelected.tasks.push(response.data.backlog);
          taskCountsUpdate(taskCounts, epicSelected);
          taskProcessing = false;
        })
        .catch(error => {
          console.error('Error making task:', error);
          taskProcessing = false;
        });
      }
    }
  }

export function deleteTask(epicSelected, taskCounts, selectedTaskToUpdate, projectDetails) {
    selectedTaskToUpdate.forEach(task => {
      axios.post('/scrum/backlog-delete', {
        id: task.id,
        epicId: epicSelected.id,
        title: task.title,
        projectId: projectDetails.id
      })
      .then(response => {
        if (response.data.success) {
          epicSelected.tasks = epicSelected.tasks.filter(t => t.id !== task.id);
          taskCountsUpdate(taskCounts, epicSelected);
        }
        selectedTaskToUpdate.value = [];
      });
    });
  }

