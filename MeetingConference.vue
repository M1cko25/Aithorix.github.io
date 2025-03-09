<template>
  <div class="meet-container">
    <!-- Main Content Area -->
    <div class="main-content" :class="{ 'with-sidebar': isSidePanelOpen }">
      <!-- Video Grid -->
      <div class="video-grid">
        <div v-for="n in 4" :key="n" class="video-tile">
          <div class="participant-name">Participant {{ n }}</div>
          <div class="video-placeholder">
            <span class="material-icons">account_circle</span>
          </div>
        </div>
      </div>

      <!-- Meeting Info Overlay -->
      <div class="meeting-info-overlay">
        <div class="meeting-title">{{ meetingTitle }}</div>
        <div class="meeting-code">Meeting code: {{ meetingCode }}</div>
      </div>

      <!-- Task Progress Overlay -->
      <div class="task-progress-overlay">
        <div class="progress-header">
          <span class="material-icons">task_alt</span>
          <span>Tasks Progress</span>
        </div>
        <div class="progress-bar">
          <div 
            class="progress-fill"
            :style="{ width: `${taskProgress}%` }"
          ></div>
        </div>
        <div class="progress-stats">
          {{ completedTasks }}/{{ totalTasks }} tasks completed
        </div>
      </div>
    </div>

    <!-- Side Panel -->
    <div class="side-panel" v-show="isSidePanelOpen">
      <div class="panel-tabs">
        <button 
          class="tab-btn" 
          :class="{ active: activeTab === 'tasks' }"
          @click="activeTab = 'tasks'"
        >
          <span class="material-icons">task</span>
          Tasks
        </button>
        <button 
          class="tab-btn" 
          :class="{ active: activeTab === 'chat' }"
          @click="activeTab = 'chat'"
        >
          <span class="material-icons">chat</span>
          Chat
        </button>
        <button 
          class="tab-btn" 
          :class="{ active: activeTab === 'participants' }"
          @click="activeTab = 'participants'"
        >
          <span class="material-icons">people</span>
          Participants
        </button>
      </div>

      <!-- Tasks Panel -->
      <div v-if="activeTab === 'tasks'" class="panel-content">
        <div class="task-list">
          <div 
            v-for="task in tasks" 
            :key="task.id"
            class="task-item"
            :class="{ completed: task.completed }"
          >
            <div class="task-content">
              <input 
                type="checkbox" 
                class="task-checkbox"
                v-model="task.completed"
              >
              <span class="task-text">{{ task.text }}</span>
            </div>
            <div class="task-actions">
              <span class="task-date">{{ task.dueDate }}</span>
              <button class="delete-btn" @click="removeTask(task.id)">
                <span class="material-icons">delete</span>
              </button>
            </div>
          </div>
        </div>

        <div class="add-task">
          <input 
            type="text" 
            v-model="newTask"
            @keyup.enter="addTask"
            placeholder="Add new task..."
          >
          <button @click="addTask">Add</button>
        </div>
      </div>

      <!-- Other panels remain the same -->
    </div>

    <!-- Bottom Controls Bar -->
    <div class="controls-bar">
      <!-- Controls remain the same -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// State
const isSidePanelOpen = ref(true)
const activeTab = ref('tasks')
const meetingTitle = ref('Project Review Meeting')
const meetingCode = ref(generateMeetingCode())
const newTask = ref('')
const tasks = ref([
  { id: 1, text: 'Review project timeline', completed: false, dueDate: '2024-03-06' },
  { id: 2, text: 'Discuss resource allocation', completed: false, dueDate: '2024-03-07' },
  { id: 3, text: 'Review budget status', completed: false, dueDate: '2024-03-08' },
  { id: 4, text: 'Set next meeting date', completed: false, dueDate: '2024-03-09' }
])

// Computed
const completedTasks = computed(() => tasks.value.filter(task => task.completed).length)
const totalTasks = computed(() => tasks.value.length)
const taskProgress = computed(() => (completedTasks.value / totalTasks.value) * 100 || 0)

// Methods
function addTask() {
  if (newTask.value.trim()) {
    tasks.value.push({
      id: Date.now(),
      text: newTask.value.trim(),
      completed: false,
      dueDate: new Date().toISOString().split('T')[0]
    })
    newTask.value = ''
  }
}

function removeTask(taskId) {
  tasks.value = tasks.value.filter(task => task.id !== taskId)
}

function generateMeetingCode() {
  return Math.random().toString(36).substring(2, 8).toUpperCase()
}
</script>

<style scoped>
/* Base styles */
.meet-container {
  height: 100vh;
  width: 100vw;
  display: flex;
  background-color: #202124;
  color: #fff;
  overflow: hidden;
}

.main-content {
  flex: 1;
  position: relative;
  transition: margin-right 0.3s ease;
}

.main-content.with-sidebar {
  margin-right: 320px;
}

/* Video Grid */
.video-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 16px;
  padding: 16px;
  height: calc(100vh - 72px);
}

.video-tile {
  background: #3c4043;
  border-radius: 12px;
  aspect-ratio: 16/9;
  position: relative;
  overflow: hidden;
}

.video-placeholder {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.video-placeholder .material-icons {
  font-size: 64px;
  color: #9aa0a6;
}

/* Task List */
.task-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  margin-bottom: 8px;
  background: #3c4043;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.task-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.task-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.delete-btn {
  background: none;
  border: none;
  color: #9aa0a6;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background: #ea4335;
  color: white;
}

/* Side Panel */
.side-panel {
  position: fixed;
  right: 0;
  top: 0;
  width: 320px;
  height: 100vh;
  background: #303134;
  border-left: 1px solid #3c4043;
  display: flex;
  flex-direction: column;
}

.panel-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
}

/* Add other existing styles... */

/* New utility classes */
.material-icons {
  font-size: 20px;
  line-height: 1;
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #303134;
}

::-webkit-scrollbar-thumb {
  background: #5f6368;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #747678;
}
</style> 