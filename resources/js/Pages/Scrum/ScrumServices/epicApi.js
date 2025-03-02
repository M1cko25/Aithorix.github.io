import { ref, onMounted } from 'vue';
import axios from "axios";

export function updateEpicStatus(epics, epicSelected, projectDetails) {
    const newStatus = epicSelected.status === 'On Sprint' ? 'Completed' : 'On Sprint';
    
    return axios.post('/scrum/epic-status-update', {
        epicId: epicSelected.epic_id,
        status: newStatus,
        projectId: projectDetails.id
    })
    .then(response => {
        epicSelected.status = newStatus;
        const epicIndex = epics.value.findIndex(e => e.epic_id === epicSelected.epic_id);
        if (epicIndex !== -1) {
            epics.value[epicIndex].status = newStatus;
        }
        return response;
    })
    .catch(error => {
        console.error('Error updating epic status:', error);
        throw error;
    });
}

export function createNewEpic(epics, epicProcessing, epicSelected, newEpic, projEpics, projectDetails) {
  epicProcessing = true;
  if (newEpic.trim().length > 0) {
    return axios.post('/scrum/epic-create', {
      name: newEpic,
      key: projectDetails.key + '-E' + (projEpics.length + 1),
      projectId: projectDetails.id
    }, {
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      epics.forEach(epic => {
        if (epic.isActive) {
          epic.isActive = false;
        }
      });
      const newEpicObj = {
        epic_id: response.data.id,
        name: newEpic,
        isActive: true,
        description: '',
        key: response.data.key,
        order: projEpics.length + 1,
        progress: '0%',
        tasks: []
      }
    epics.push(newEpicObj);
    projEpics.push(response.data);
    });
  }
}


