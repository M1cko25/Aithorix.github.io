import { ref, onMounted} from 'vue'

export function taskCountsUpdate(taskCounts, epicSelected) {
    taskCounts.todo = epicSelected.tasks.filter(task => task.status === 'To Do').length;
    taskCounts.inProgress = epicSelected.tasks.filter(task => task.status === 'In Progress').length;
    taskCounts.completed = epicSelected.tasks.filter(task => task.status === 'Done').length;
}