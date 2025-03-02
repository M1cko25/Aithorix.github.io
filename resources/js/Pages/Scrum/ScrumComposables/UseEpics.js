import { ref, onMounted} from 'vue';
import { taskCountsUpdate } from './UseTasks';
import { updateEpicStatus } from '../ScrumServices/epicApi';

export function selectEpic(epics, taskCounts, epicSelected, selectedEpic) {
    epics.forEach(epic => {
      epic.isActive = epic === selectedEpic;
    });
    epicSelected = selectedEpic;
    taskCountsUpdate(taskCounts, selectedEpic);
  }

export function startSprint() {
    if (epicSelected.value.tasks.length > 0) {
      let aSprintIsActive = false;
      epics.value.forEach(epic => {
        if (epic.status === 'On Sprint') {
          aSprintIsActive = true;
        }
      });
      if (aSprintIsActive) {
        alert('Only one sprint can be active at a time.');
        openSprint.value = false;
      } else {
        router.post('/scrum/start-sprint', form)
        updateEpicStatus();
        openSprint.value = false;
      }
    } else {
      alert('Please add tasks to the epic before starting the sprint.');
    }
  }