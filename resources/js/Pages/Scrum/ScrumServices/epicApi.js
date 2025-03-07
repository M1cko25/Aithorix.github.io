import { ref, onMounted } from 'vue';
import axios from "axios";

export function updateEpicStatus(epics, epicSelected, projectDetails) {
    return axios.post('/scrum/epic-status-update', {
        epicId: epicSelected.epic_id,
        status: epicSelected.status === 'On Sprint' ? 'Completed' : 'On Sprint',
        projectId: projectDetails.id
    })
    .then(response => {
        // Update the status in the local state
        epicSelected.status = epicSelected.status === 'On Sprint' ? 'Completed' : 'On Sprint';
        
        // Update the status in the epics array
        if (epics.value) {
            const epicIndex = epics.value.findIndex(e => e.epic_id === epicSelected.epic_id);
            if (epicIndex !== -1) {
                epics.value[epicIndex].status = epicSelected.status;
            }
        }
        return response;
    })
    .catch(error => {
        console.error('Error updating epic status:', error);
        throw error;
    });
}

export function createNewEpic(epics, epicProcessing, newEpic, projEpics, projectDetails) {
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
        epic.isActive = false;
      });

      const newEpicObj = {
        epic_id: response.data.id,
        name: newEpic,
        isActive: true,
        description: '',
        key: projectDetails.key + '-E' + (projEpics.length + 1),
        order: projEpics.length + 1,
        progress: '0%',
        tasks: [],
        start_date: response.data.start_date,
        end_date: response.data.end_date,
        status: 'Pending'
      };

      epics.push(newEpicObj);
      projEpics.push({
        ...response.data,
        start_date: response.data.start_date,
        end_date: response.data.end_date
      });
      
      return newEpicObj;
    });
  }
  return Promise.resolve(null);
}


