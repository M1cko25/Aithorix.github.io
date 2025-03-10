<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import Daily from "@daily-co/daily-js";
import axios from "axios";
import { usePage } from '@inertiajs/vue3';

const page = usePage().props;
const meetingName = ref(page.meetingName || page.params?.name);

const callFrame = ref(null);
const meetingUrl = ref("");
const videoContainer = ref(null);
const isLoading = ref(false);
const meetingJoined = ref(false);

const createDailyRoom = async () => {
  try {
    // Check if we already have a room URL in sessionStorage
    const storedUrl = sessionStorage.getItem(`meeting_${meetingName.value}`);
    if (storedUrl) {
      return storedUrl;
    }

    // If no stored URL, create a new room
    const response = await axios.post("/daily/create-room", {
      name: meetingName.value
    });

    if (response.data.error) {
      throw new Error(response.data.error);
    }
    
    // Store the URL for future use (page reloads)
    sessionStorage.setItem(`meeting_${meetingName.value}`, response.data.url);
    return response.data.url;
  } catch (error) {
    console.error("Error creating Daily room:", error);
    throw new Error("Failed to create meeting room");
  }
};

const initializeCallFrame = (container) => {
  return Daily.createFrame(container, {
    iframeStyle: {
      width: '100%',
      height: '100%',
      border: '0',
      borderRadius: '0',
    },
    showLeaveButton: true,
    showFullscreenButton: true,
    showLocalVideo: true,
    showParticipantsBar: true,
    theme: {
      colors: {
        accent: '#6366F1',
        accentText: '#FFFFFF',
        background: '#1F2937',
        backgroundAccent: '#374151',
        baseText: '#FFFFFF',
      }
    }
  });
};

const startMeeting = async () => {
  try {
    isLoading.value = true;

    // Get or create room URL
    const url = await createDailyRoom();
    meetingUrl.value = url;

    // Then initialize the call frame
    if (videoContainer.value) {
      // Destroy existing frame if it exists
      if (callFrame.value) {
        callFrame.value.destroy();
      }

      // Create new frame
      const frame = initializeCallFrame(videoContainer.value);
      callFrame.value = frame;

      // Set up event listeners before joining
      frame.on('left-meeting', () => {
        handleLeftMeeting();
      });

      frame.on('error', (evt) => {
        handleError(evt);
      });

      frame.on('joined-meeting', () => {
        console.log('Successfully joined the meeting');
        meetingJoined.value = true;
        sessionStorage.setItem(`meeting_joined_${meetingName.value}`, 'true');
      });

      // Join the meeting
      await frame.join({ 
        url: meetingUrl.value,
        showLeaveButton: true,
        showFullscreenButton: true
      });

      console.log('Join command sent');
    }
  } catch (error) {
    console.error("Error in startMeeting:", error);
    alert(error.message || "Failed to start meeting. Please try again.");
  } finally {
    isLoading.value = false;
  }
};

const handleLeftMeeting = () => {
  try {
    if (callFrame.value) {
      callFrame.value.destroy();
      callFrame.value = null;
    }
    // Clear session storage
    sessionStorage.removeItem(`meeting_${meetingName.value}`);
    sessionStorage.removeItem(`meeting_joined_${meetingName.value}`);
    
    // Redirect back to meeting home
    window.location.href = route('meeting.home');
  } catch (error) {
    console.error('Error in handleLeftMeeting:', error);
  }
};

const handleError = (error) => {
  console.error('Daily.co error:', error);
  // Only show alert if it's not a connection error during reload
  if (!error.toString().includes('connection')) {
    alert('An error occurred in the meeting. Please try rejoining.');
  }
};

const handleTaskUpdate = (updatedTask) => {
  // Find and update the task in the backlogs array
  const taskIndex = page.backlogs.findIndex(t => t.id === updatedTask.id);
  if (taskIndex !== -1) {
    // Create a new object with all the updated properties
    page.backlogs[taskIndex] = {
      ...page.backlogs[taskIndex],
      ...updatedTask
    };
    
    // Create a new array reference to trigger reactivity
    page.backlogs = [...page.backlogs];
  }
  
  // Update the task in the sprint tasks if it exists
  if (sprintTasks.value) {
    const sprintTaskIndex = sprintTasks.value.findIndex(t => t.backlog_id === updatedTask.id);
    if (sprintTaskIndex !== -1) {
      sprintTasks.value[sprintTaskIndex].backlog = {
        ...sprintTasks.value[sprintTaskIndex].backlog,
        ...updatedTask
      };
      // Create a new array reference to trigger reactivity
      sprintTasks.value = [...sprintTasks.value];
    }
  }
};

// Add watch for task updates
watch(() => props.task, (newTask) => {
  if (newTask) {
    handleTaskUpdate(newTask);
  }
}, { deep: true });

// Clean up on component unmount
onUnmounted(() => {
  try {
    if (callFrame.value) {
      callFrame.value.destroy();
      callFrame.value = null;
    }
  } catch (error) {
    console.error('Error cleaning up:', error);
  }
});

onMounted(() => {
  if (!meetingName.value) {
    console.error('No meeting name provided');
    alert('Invalid meeting configuration');
    window.location.href = route('meeting.home');
    return;
  }

  // Check if we were already in a meeting (page reload)
  const wasInMeeting = sessionStorage.getItem(`meeting_joined_${meetingName.value}`);
  if (wasInMeeting) {
    meetingJoined.value = true;
  }
  
  // Start or rejoin meeting
  startMeeting();
});
</script>

<template>
  <div class="min-h-screen bg-gray-900">
    <div class="p-0">
      <div v-if="!meetingJoined" class="absolute w-full top-0 mb-4 z-10 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-white px-4 py-2">{{ meetingName }}</h1>
      </div>
      <div 
        ref="videoContainer"
        class="w-full bg-gray-800 h-screen"
      ></div>
    </div>
  </div>
</template>

<style scoped>
:deep(iframe) {
  border-radius: 0;
}
</style>
