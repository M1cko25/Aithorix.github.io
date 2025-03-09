<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { 
  Mic, 
  MicOff, 
  Video, 
  VideoOff, 
  PhoneOff, 
  MonitorUp, 
  ListTodo, 
  MessageSquare, 
  Users,
  User,
  Send,
  Trash,
  Plus,
  MoreVertical,
  Pin,
  Maximize2,
  Minimize2
} from 'lucide-vue-next'
import AgoraRTC from 'agora-rtc-sdk-ng';

// ===== CONTROL BAR STATE =====
const isSidebarOpen = ref(false)
const isChatOpen = ref(false)
const isParticipantsOpen = ref(false)
const isMuted = ref(false)
const isVideoOff = ref(false)
const isScreenSharing = ref(false)
const isPinned = ref(false)
const isFullscreen = ref(false)
const agoraClient = ref(null);
const localTracks = ref(null);
const remoteUsers = ref(new Map());
const appId = "7b621d5bd0454d72920fddfecd72a7eb"
const token = "007eJxTYHDNftUy+8A5+6YOoymni74GG0po3yw80lEh5FHXI2lWf1WBwTzJzMgwxTQpxcDE1CTF3MjSyCAtJSUtNRnITjRPTToefza9IZCRodiHj5mRAQJBfF4Gx8ySjPyizArdkNTiEgYGAIoaIuc="

const client = AgoraRTC.createClient({
  mode: 'rtc',
  codec: 'vp8'
});

// Add these new refs for managing streams
const localAudioTrack = ref(null);
const localVideoTrack = ref(null);

// Initialize Agora client
const initializeAgora = async () => {
  try {
    // Create and join channel
    await client.join(appId, 'Aithorix-Test', token, null);
    console.log('Successfully joined channel');

    // Create local tracks
    const [audioTrack, videoTrack] = await AgoraRTC.createMicrophoneAndCameraTracks(
      {
        encoderConfig: {
          width: { min: 640, ideal: 1920, max: 1920 },
          height: { min: 480, ideal: 1080, max: 1080 }
        }
      }
    );
    
    localAudioTrack.value = audioTrack;
    localVideoTrack.value = videoTrack;
    
    // Publish tracks
    await client.publish([audioTrack, videoTrack]);
    console.log('Successfully published tracks');

    // Play local video
    videoTrack.play('local-player');
    
    // Update UI state
    isVideoOff.value = false;
    isMuted.value = false;
  } catch (error) {
    console.error('Error in initializeAgora:', error);
    if (error.code === 'PERMISSION_DENIED') {
      alert('Please allow camera and microphone permissions to join the meeting.');
    }
  }
};

// Handle remote user events
client.on('user-published', async (user, mediaType) => {
  try {
    await client.subscribe(user, mediaType);
    console.log('Successfully subscribed to', mediaType, 'from user', user.uid);

    if (mediaType === 'video') {
      remoteUsers.value.set(user.uid, user);
      nextTick(() => {
        user.videoTrack.play(`player-${user.uid}`);
      });
    }
    if (mediaType === 'audio') {
      user.audioTrack.play();
    }
  } catch (error) {
    console.error('Error handling user-published event:', error);
  }
});

client.on('user-unpublished', (user, mediaType) => {
  if (mediaType === 'video') {
    remoteUsers.value.delete(user.uid);
  }
});

client.on('user-left', (user) => {
  remoteUsers.value.delete(user.uid);
});

// Update video controls
const toggleVideo = async () => {
  if (!localVideoTrack.value) return;
  
  try {
    await localVideoTrack.value.setEnabled(!isVideoOff.value);
    isVideoOff.value = !isVideoOff.value;
  } catch (error) {
    console.error('Error toggling video:', error);
  }
};

const toggleMute = async () => {
  if (!localAudioTrack.value) return;
  
  try {
    await localAudioTrack.value.setEnabled(!isMuted.value);
    isMuted.value = !isMuted.value;
  } catch (error) {
    console.error('Error toggling audio:', error);
  }
};

const endMeeting = async () => {
  try {
    // Clean up tracks
    if (localAudioTrack.value) {
      localAudioTrack.value.close();
    }
    if (localVideoTrack.value) {
      localVideoTrack.value.close();
    }
    
    // Leave the channel
    await client.leave();
    remoteUsers.value.clear();
    
    // Redirect to home
    window.location.href = '/';
  } catch (error) {
    console.error('Error ending meeting:', error);
  }
};

// Computed property to determine if any sidebar is visible
const isSidebarVisible = computed(() => 
  isSidebarOpen.value || isChatOpen.value || isParticipantsOpen.value
)

// Control bar methods
const toggleScreenShare = () => {
  isScreenSharing.value = !isScreenSharing.value
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
  if (isSidebarOpen.value) {
    isChatOpen.value = false
    isParticipantsOpen.value = false
  }
}

const toggleChat = () => {
  isChatOpen.value = !isChatOpen.value
  if (isChatOpen.value) {
    isSidebarOpen.value = false
    isParticipantsOpen.value = false
  }
}

const toggleParticipants = () => {
  isParticipantsOpen.value = !isParticipantsOpen.value
  if (isParticipantsOpen.value) {
    isSidebarOpen.value = false
    isChatOpen.value = false
  }
}

const togglePin = () => {
  isPinned.value = !isPinned.value
}

const toggleFullscreen = async () => {
  const mainPresenter = document.querySelector('.col-span-3')
  
  if (!isFullscreen.value) {
    try {
      if (mainPresenter.requestFullscreen) {
        await mainPresenter.requestFullscreen()
      } else if (mainPresenter.webkitRequestFullscreen) {
        await mainPresenter.webkitRequestFullscreen()
      } else if (mainPresenter.msRequestFullscreen) {
        await mainPresenter.msRequestFullscreen()
      }
      isFullscreen.value = true
    } catch (err) {
      console.error('Error attempting to enable fullscreen:', err)
    }
  } else {
    try {
      if (document.exitFullscreen) {
        await document.exitFullscreen()
      } else if (document.webkitExitFullscreen) {
        await document.webkitExitFullscreen()
      } else if (document.msExitFullscreen) {
        await document.msExitFullscreen()
      }
      isFullscreen.value = false
    } catch (err) {
      console.error('Error attempting to exit fullscreen:', err)
    }
  }
}

// ===== VIDEO GRID STATE =====
// Compute the grid layout based on the number of participants
const gridClass = computed(() => {
  const count = displayedParticipants.value.length + 1 // +1 for main user
  if (count <= 4) return 'grid-cols-2'
  if (count <= 6) return 'grid-cols-3'
  return 'grid-cols-4'
})

// ===== TASK SIDEBAR STATE =====
const tasks = ref([
  { id: 1, text: "Discuss project timeline", completed: false },
  { id: 2, text: "Review design mockups", completed: true },
  { id: 3, text: "Assign tasks to team members", completed: false }
])
const newTaskText = ref('')

// Task methods
const addTask = () => {
  if (newTaskText.value.trim() === '') return
  
  tasks.value.push({
    id: Date.now(),
    text: newTaskText.value.trim(),
    completed: false
  })
  
  newTaskText.value = ''
}

const deleteTask = (taskId) => {
  tasks.value = tasks.value.filter(task => task.id !== taskId)
}

// ===== CHAT SIDEBAR STATE =====
const messages = ref([
  {
    id: 1,
    sender: "John Doe",
    text: "Hi everyone! Shall we start discussing the project timeline?",
    timestamp: new Date(Date.now() - 1000 * 60 * 5),
    isFromUser: false
  },
  {
    id: 2,
    sender: "Jane Smith",
    text: "Yes, I've prepared some notes we can go through.",
    timestamp: new Date(Date.now() - 1000 * 60 * 4),
    isFromUser: false
  },
  {
    id: 3,
    sender: "You",
    text: "Great! I also have some questions about the design mockups.",
    timestamp: new Date(Date.now() - 1000 * 60 * 3),
    isFromUser: true
  }
])
const newMessage = ref('')
const messagesContainer = ref(null)

// Chat methods
const sendMessage = () => {
  if (newMessage.value.trim() === '') return
  
  messages.value.push({
    id: Date.now(),
    sender: "You",
    text: newMessage.value.trim(),
    timestamp: new Date(),
    isFromUser: true
  })
  
  newMessage.value = ''
}

const formatTime = (date) => {
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

// Auto-scroll to bottom when new messages arrive
watch(messages, async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}, { deep: true })

// ===== PARTICIPANTS SIDEBAR STATE =====
const allParticipants = ref([
  { id: 1, name: "You", isMainUser: true, isMuted: false, isVideoOff: false, isHost: true },
  { id: 2, name: "John Doe", isMainUser: false, isMuted: true, isVideoOff: false },
  { id: 3, name: "Jane Smith", isMainUser: false, isMuted: false, isVideoOff: true },
  { id: 4, name: "Alex Johnson", isMainUser: false, isMuted: false, isVideoOff: false },
  { id: 5, name: "Sarah Williams", isMainUser: false, isMuted: true, isVideoOff: true },
  { id: 6, name: "Michael Brown", isMainUser: false, isMuted: false, isVideoOff: false },
  { id: 7, name: "Emily Davis", isMainUser: false, isMuted: true, isVideoOff: false },
  { id: 8, name: "David Wilson", isMainUser: false, isMuted: false, isVideoOff: true },
  { id: 9, name: "Lisa Taylor", isMainUser: false, isMuted: false, isVideoOff: false },
  { id: 10, name: "Robert Martinez", isMainUser: false, isMuted: true, isVideoOff: false },
  { id: 11, name: "Jennifer Anderson", isMainUser: false, isMuted: false, isVideoOff: true },
  { id: 12, name: "Thomas Jackson", isMainUser: false, isMuted: true, isVideoOff: true }
])
const activeDropdown = ref(null)

// Participants computed properties
const hostParticipants = computed(() => 
  allParticipants.value.filter(p => p.isHost)
)

const regularParticipants = computed(() => 
  allParticipants.value.filter(p => !p.isHost)
)

// Participants methods
const toggleDropdown = (participantId) => {
  if (activeDropdown.value === participantId) {
    activeDropdown.value = null
  } else {
    activeDropdown.value = participantId
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (activeDropdown.value !== null) {
    activeDropdown.value = null
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('fullscreenchange', handleFullscreenChange)
  document.addEventListener('webkitfullscreenchange', handleFullscreenChange)
  document.addEventListener('mozfullscreenchange', handleFullscreenChange)
  document.addEventListener('MSFullscreenChange', handleFullscreenChange)
  initializeAgora() // Start video conference when component mounts
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('fullscreenchange', handleFullscreenChange)
  document.removeEventListener('webkitfullscreenchange', handleFullscreenChange)
  document.removeEventListener('mozfullscreenchange', handleFullscreenChange)
  document.removeEventListener('MSFullscreenChange', handleFullscreenChange)
  if (localAudioTrack.value) {
    localAudioTrack.value.close();
  }
  if (localVideoTrack.value) {
    localVideoTrack.value.close();
  }
  client.removeAllListeners();
  // Leave the channel
  client.leave().catch(console.error);
  localStorage.removeItem('cameraPermissionShown') // Clean up permission flag
})

const handleFullscreenChange = () => {
  isFullscreen.value = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement)
}

// Update the computed properties
const maxVisibleParticipants = computed(() => {
  if (window.innerWidth >= 1280) return 7 // Main + 6 others
  if (window.innerWidth >= 1024) return 5 // Main + 4 others
  if (window.innerWidth >= 768) return 4  // Main + 3 others
  return 3 // Main + 2 others
})

const displayedParticipants = computed(() => {
  return regularParticipants.value.slice(0, 2); // Show only first 2 participants
})

const remainingParticipants = computed(() => {
  return Math.max(0, regularParticipants.value.length - 2); // Calculate remaining based on regular participants
})

// Add these computed properties
const firstRowParticipants = computed(() => {
  return allParticipants.value.filter(p => !p.isMainUser).slice(0, 1)
})

const middleRowParticipants = computed(() => {
  return allParticipants.value.filter(p => !p.isMainUser).slice(1, 2)
})

const bottomRowParticipants = computed(() => {
  const nextParticipants = regularParticipants.value.slice(2);
  return nextParticipants.slice(0, 2); // Show next 2 participants in the stack
})

// Add this computed property for formatting remaining names
const formatRemainingNames = computed(() => {
  const remainingPeople = regularParticipants.value.slice(2);
  if (remainingPeople.length === 0) return '';
  if (remainingPeople.length === 1) return remainingPeople[0].name;
  if (remainingPeople.length === 2) return `${remainingPeople[0].name} and ${remainingPeople[1].name}`;
  
  return `${remainingPeople[0].name}, ${remainingPeople[1].name} and ${remainingPeople.length - 2} others`;
})

// Add these refs
const videoStream = ref(null)
const videoElement = ref(null)

// Add these methods
const startCamera = async () => {
  try {
    // Check if permission was already denied
    const permission = await navigator.permissions.query({ name: 'camera' });
    if (permission.state === 'denied') {
      isVideoOff.value = true;
      return; // Don't show alert if permission was already denied
    }

    const stream = await navigator.mediaDevices.getUserMedia({ 
      video: {
        width: { ideal: 1920 },
        height: { ideal: 1080 },
        facingMode: 'user'
      },
      audio: true
    });
    videoStream.value = stream;
    if (videoElement.value) {
      videoElement.value.srcObject = stream;
      await videoElement.value.play();
    }
    isVideoOff.value = false;
  } catch (err) {
    console.error('Error accessing camera:', err);
    isVideoOff.value = true;
    // Only show alert once
    if (!localStorage.getItem('cameraPermissionShown')) {
      alert('Unable to access camera. Please check your camera permissions.');
      localStorage.setItem('cameraPermissionShown', 'true');
    }
  }
}

const stopCamera = () => {
  if (videoStream.value) {
    videoStream.value.getTracks().forEach(track => track.stop())
    videoStream.value = null
    if (videoElement.value) {
      videoElement.value.srcObject = null
    }
  }
  isVideoOff.value = true
}

// Add this watch to handle video element updates
watch(videoElement, (el) => {
  if (el && videoStream.value) {
    el.srcObject = videoStream.value
    el.play().catch(err => console.error('Error playing video:', err))
  }
})
</script>
<template>
  <div class="h-screen bg-gray-900">
    <!-- Main Content Area -->
    <div class="flex flex-col h-full">
      <!-- Video Grid -->
      <div class="flex-1 relative" :class="{ 'mr-80': isSidebarVisible }">
        <div class="absolute inset-0 p-4">
          <div class="grid grid-cols-4 gap-4 h-full">
            <!-- Left side (Main presenter) -->
            <div class="col-span-3 relative rounded-lg overflow-hidden bg-gray-800">
              <!-- Local Video Container -->
              <div id="local-player" class="w-full h-full"></div>
              
              <!-- Fallback content when video is off -->
              <div v-if="isVideoOff" class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <div class="h-24 w-24 rounded-full bg-gray-700 flex items-center justify-center text-4xl font-medium text-white mb-3">
                    Y
                  </div>
                  <span class="text-xl text-white">You</span>
                  <div class="flex items-center justify-center gap-2 mt-1">
                    <span class="text-sm text-gray-400">(Host)</span>
                    <span class="px-2 py-0.5 bg-gray-700 text-white text-xs rounded-full">Host</span>
                  </div>
                </div>
              </div>
              
              <!-- Controls overlay -->
              <div class="absolute top-2 right-2 flex items-center gap-2">
                <button 
                  @click="togglePin"
                  class="p-1.5 rounded-lg bg-gray-900/80 hover:bg-gray-900 text-white transition-colors"
                  :class="{ 'text-blue-500': isPinned }"
                  title="Pin video"
                >
                  <Pin class="h-4 w-4" :class="{ 'text-blue-500': isPinned }" />
                </button>
                <button 
                  @click="toggleFullscreen"
                  class="p-1.5 rounded-lg bg-gray-900/80 hover:bg-gray-900 text-white transition-colors"
                  title="Full screen"
                >
                  <Maximize2 v-if="!isFullscreen" class="h-4 w-4" />
                  <Minimize2 v-else class="h-4 w-4" />
                </button>
              </div>

              <!-- Status indicators -->
              <div class="absolute bottom-2 left-2 flex items-center gap-2">
                <div v-if="isMuted" class="p-1.5 rounded-lg bg-red-500/80 text-white">
                  <MicOff class="h-4 w-4" />
                </div>
                <div v-if="isVideoOff" class="p-1.5 rounded-lg bg-red-500/80 text-white">
                  <VideoOff class="h-4 w-4" />
                </div>
              </div>
            </div>

            <!-- Right side (Remote participants) -->
            <div class="col-span-1 grid grid-rows-3 gap-4">
              <div v-for="user in Array.from(remoteUsers.values())" 
                   :key="user.uid"
                   class="relative rounded-lg overflow-hidden bg-gray-800">
                <!-- Remote Video Container -->
                <div :id="'player-' + user.uid" class="w-full h-full"></div>
                
                <!-- Overlay content -->
                <div class="absolute bottom-2 left-2 flex items-center gap-2">
                  <div v-if="user.hasAudio === false" class="p-1.5 rounded-lg bg-red-500/80 text-white">
                    <MicOff class="h-4 w-4" />
                  </div>
                  <div v-if="user.hasVideo === false" class="p-1.5 rounded-lg bg-red-500/80 text-white">
                    <VideoOff class="h-4 w-4" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Control Bar -->
      <div class="bg-gray-800 border-t border-gray-700 p-4">
        <div class="flex items-center justify-center gap-4">
          <button
            @click="toggleMute"
            :class="[
              'rounded-full p-3 focus:outline-none',
              isMuted ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-700 hover:bg-gray-600'
            ]"
            :title="isMuted ? 'Unmute' : 'Mute'"
          >
            <MicOff v-if="isMuted" class="h-5 w-5 text-white" />
            <Mic v-else class="h-5 w-5 text-white" />
          </button>

          <button
            @click="toggleVideo"
            :class="[
              'rounded-full p-3 focus:outline-none',
              isVideoOff ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-700 hover:bg-gray-600'
            ]"
            :title="isVideoOff ? 'Turn on camera' : 'Turn off camera'"
          >
            <VideoOff v-if="isVideoOff" class="h-5 w-5 text-white" />
            <Video v-else class="h-5 w-5 text-white" />
          </button>

          <button
            @click="toggleScreenShare"
            :class="[
              'rounded-full p-3 focus:outline-none',
              isScreenSharing ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-700 hover:bg-gray-600'
            ]"
            title="Share screen"
          >
            <MonitorUp class="h-5 w-5 text-white" />
          </button>

          <button
            @click="toggleChat"
            :class="[
              'rounded-full p-3 focus:outline-none',
              isChatOpen ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-700 hover:bg-gray-600'
            ]"
            :title="isChatOpen ? 'Hide chat' : 'Show chat'"
          >
            <MessageSquare class="h-5 w-5 text-white" />
          </button>

          <button
            @click="toggleParticipants"
            :class="[
              'rounded-full p-3 focus:outline-none',
              isParticipantsOpen ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-700 hover:bg-gray-600'
            ]"
            :title="isParticipantsOpen ? 'Hide participants' : 'Show participants'"
          >
            <Users class="h-5 w-5 text-white" />
          </button>

          <button
            @click="endMeeting"
            class="rounded-full p-3 bg-red-500 hover:bg-red-600 focus:outline-none"
            title="End meeting"
          >
            <PhoneOff class="h-5 w-5 text-white" />
          </button>
        </div>
      </div>
    </div>

    <!-- Task Sidebar -->
    <div
      class="fixed right-0 top-0 bottom-0 w-80 bg-white shadow-lg transition-transform duration-300 transform h-full flex flex-col"
      :class="isSidebarOpen ? 'translate-x-0' : 'translate-x-full'"
      style="z-index: 10;"
    >
      <div class="p-4 border-b">
        <h2 class="text-xl font-semibold">Meeting Tasks</h2>
      </div>
      
      <div class="flex-1 overflow-y-auto p-4">
        <p v-if="tasks.length === 0" class="text-gray-500 text-center py-4">
          No tasks yet. Add one below!
        </p>
        <ul v-else class="space-y-2">
          <li
            v-for="task in tasks"
            :key="task.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
          >
            <div class="flex items-center space-x-3">
              <input
                type="checkbox"
                :id="`task-${task.id}`"
                v-model="task.completed"
                class="rounded border-gray-300 text-blue focus:ring-blue"
              />
              <label
                :for="`task-${task.id}`"
                class="cursor-pointer"
                :class="{ 'line-through text-gray-500': task.completed }"
              >
                {{ task.text }}
              </label>
            </div>
            <button
              @click="deleteTask(task.id)"
              class="text-gray-500 hover:text-red-500"
            >
              <Trash class="h-4 w-4" />
            </button>
          </li>
        </ul>
      </div>
      
      <div class="p-4 border-t">
        <div class="flex space-x-2">
          <input
            v-model="newTaskText"
            type="text"
            placeholder="Add a new task..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue"
            @keydown.enter="addTask"
          />
          <button
            @click="addTask"
            class="p-2 bg-blue text-white rounded-md hover:bg-blue focus:outline-none focus:ring-2 focus:ring-blue"
          >
            <Plus class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Chat Sidebar -->
    <div
      class="fixed right-0 top-0 bottom-0 w-80 bg-white shadow-lg transition-transform duration-300 transform h-full flex flex-col"
      :class="isChatOpen ? 'translate-x-0' : 'translate-x-full'"
      style="z-index: 10;"
    >
      <div class="p-4 border-b">
        <h2 class="text-xl font-semibold">Meeting Chat</h2>
      </div>
      
      <div class="flex-1 overflow-y-auto p-4" ref="messagesContainer">
        <p v-if="messages.length === 0" class="text-gray-500 text-center py-4">
          No messages yet. Start the conversation!
        </p>
        <div v-else class="space-y-4">
          <div
            v-for="message in messages"
            :key="message.id"
            :class="[
              'flex',
              message.isFromUser ? 'justify-end' : 'justify-start'
            ]"
          >
            <div
              :class="[
                'max-w-[80%] rounded-lg p-3',
                message.isFromUser ? 'bg-blue text-white' : 'bg-gray-100'
              ]"
            >
              <div v-if="!message.isFromUser" class="flex items-center space-x-2 mb-1">
                <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center text-xs font-medium">
                  {{ message.sender.charAt(0) }}
                </div>
                <span class="font-medium text-sm">{{ message.sender }}</span>
              </div>
              <p>{{ message.text }}</p>
              <p
                :class="[
                  'text-xs mt-1',
                  message.isFromUser ? 'text-blue' : 'text-gray-500'
                ]"
              >
                {{ formatTime(message.timestamp) }}
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="p-4 border-t">
        <div class="flex space-x-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type a message..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue"
            @keydown.enter="sendMessage"
          />
          <button
            @click="sendMessage"
            class="p-2 bg-blue text-white rounded-md hover:bg-blue focus:outline-none focus:ring-2 focus:ring-blue"
          >
            <Send class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Participants Sidebar -->
    <div
      class="fixed right-0 top-0 bottom-0 w-80 bg-white shadow-lg transition-transform duration-300 transform h-full flex flex-col"
      :class="isParticipantsOpen ? 'translate-x-0' : 'translate-x-full'"
      style="z-index: 10;"
    >
      <div class="p-4 border-b">
        <h2 class="text-xl font-semibold">Participants ({{ allParticipants.length }})</h2>
      </div>
      
      <div class="flex-1 overflow-y-auto">
        <div class="p-2">
          <h3 class="text-sm font-medium text-gray-500 mb-2">Host</h3>
          <div
            v-for="participant in hostParticipants"
            :key="participant.id"
            class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-md"
          >
            <div class="flex items-center space-x-3">
              <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-lg font-medium">
                {{ participant.name.charAt(0) }}
              </div>
              <div>
                <p class="font-medium">
                  {{ participant.name }} {{ participant.isMainUser ? "(You)" : "" }}
                  <span v-if="participant.isHost" class="ml-2 text-xs bg-blue text-white px-2 py-0.5 rounded">
                    Host
                  </span>
                </p>
              </div>
            </div>
            
            <div class="flex items-center space-x-1">
              <MicOff v-if="participant.isMuted" class="h-4 w-4 text-gray-400" />
              <Mic v-else class="h-4 w-4 text-gray-600" />
              
              <VideoOff v-if="participant.isVideoOff" class="h-4 w-4 text-gray-400" />
              <Video v-else class="h-4 w-4 text-gray-600" />
            </div>
          </div>
        </div>
        
        <div class="p-2">
          <h3 class="text-sm font-medium text-gray-500 mb-2">In this meeting</h3>
          <div
            v-for="participant in regularParticipants"
            :key="participant.id"
            class="flex items-center justify-between p-2 hover:bg-gray-100 rounded-md"
          >
            <div class="flex items-center space-x-3">
              <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-lg font-medium">
                {{ participant.name.charAt(0) }}
              </div>
              <div>
                <p class="font-medium">
                  {{ participant.name }} {{ participant.isMainUser ? "(You)" : "" }}
                </p>
              </div>
            </div>
            
            <div class="flex items-center space-x-1">
              <MicOff v-if="participant.isMuted" class="h-4 w-4 text-gray-400" />
              <Mic v-else class="h-4 w-4 text-gray-600" />
              
              <VideoOff v-if="participant.isVideoOff" class="h-4 w-4 text-gray-400" />
              <Video v-else class="h-4 w-4 text-gray-600" />
              
              <div v-if="!participant.isMainUser" class="relative">
                <button 
                  @click.stop="toggleDropdown(participant.id)"
                  class="h-8 w-8 flex items-center justify-center text-gray-500 hover:text-gray-700 focus:outline-none"
                >
                  <MoreVertical class="h-4 w-4" />
                </button>
                
                <div 
                  v-if="activeDropdown === participant.id"
                  class="absolute right-0 mt-1 w-40 bg-white rounded-md shadow-lg z-10"
                >
                  <div class="py-1">
                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      Pin
                    </button>
                    <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      Mute
                    </button>
                    <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Base styles */
.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #1a1a1a;
  color: white;
}

/* Header styles */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1.5rem;
  background: #2a2a2a;
}

.meeting-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.meeting-info h1 {
  font-size: 1.25rem;
  margin: 0;
}

.meeting-code {
  background: #4CAF50;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.875rem;
}

/* Main content layout */
.main-content {
  display: grid;
  grid-template-columns: 1fr 320px;
  flex: 1;
  overflow: hidden;
  gap: 1rem;
}

/* Video grid */
.video-grid {
  display: flex;
  flex-direction: collumn;
  gap: 1rem;
  padding: 1rem;
  height: calc(100vh - 120px); /* Adjust for header and controls */
}

.main-video {
  flex: 1;
  position: relative;
  background: #2a2a2a;
  border-radius: 8px;
  overflow: hidden;
  min-height: 0; /* Important for Firefox */
}

.main-video video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.participants-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 0.75rem;
  max-height: 25vh;
  overflow-y: auto;
  padding-right: 0.5rem;
}

.participant {
  position: relative;
  background: #2a2a2a;
  border-radius: 6px;
  overflow: hidden;
  aspect-ratio: 16/9;
}

.participant video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Sidebar */
.sidebar {
  background: #2a2a2a;
  border-left: 1px solid #3a3a3a;
  display: flex;
  flex-direction: column;
  height: calc(100vh - 60px); /* Adjust for header */
}

.tasks {
  padding: 1rem;
  border-bottom: 1px solid #3a3a3a;
}

.tasks h3, .chat h3 {
  margin: 0 0 0.75rem 0;
  font-size: 1rem;
}

.task-list {
  max-height: 25vh;
  overflow-y: auto;
  margin-bottom: 0.75rem;
}

.chat {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 1rem;
}

.messages {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 1rem;
  padding-right: 0.5rem;
}

/* Controls */
.controls {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(10px);
}

.controls button {
  width: 45px;
  height: 45px;
  border: none;
  border-radius: 50%;
  background: #3a3a3a;
  color: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

/* Responsive breakpoints */
@media (max-width: 1200px) {
  .main-content {
    grid-template-columns: 1fr 280px;
  }

  .participants-grid {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  }
}

@media (max-width: 1024px) {
  .main-content {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: fixed;
    right: -320px;
    top: 0;
    bottom: 0;
    width: 320px;
    z-index: 1000;
    transition: right 0.3s ease;
  }

  .sidebar.show {
    right: 0;
  }

  .video-grid {
    height: calc(100vh - 110px);
  }
}

@media (max-width: 768px) {
  .header {
    padding: 0.5rem 1rem;
  }

  .meeting-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .participants-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    max-height: 30vh;
  }

  .sidebar {
    width: 100%;
    right: -100%;
  }

  .controls {
    padding: 0.75rem;
    gap: 0.75rem;
  }

  .controls button {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 480px) {
  .participants-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  }

  .video-grid {
    padding: 0.5rem;
    gap: 0.5rem;
  }

  .task-list, .messages {
    max-height: 35vh;
  }
}

/* Height-based media queries */
@media (max-height: 600px) {
  .participants-grid {
    max-height: 20vh;
  }

  .task-list, .messages {
    max-height: 30vh;
  }

  .controls button {
    width: 35px;
    height: 35px;
  }
}

/* Scrollbar styling */
.participants-grid::-webkit-scrollbar,
.task-list::-webkit-scrollbar,
.messages::-webkit-scrollbar {
  width: 4px;
}

.participants-grid::-webkit-scrollbar-track,
.task-list::-webkit-scrollbar-track,
.messages::-webkit-scrollbar-track {
  background: transparent;
}

.participants-grid::-webkit-scrollbar-thumb,
.task-list::-webkit-scrollbar-thumb,
.messages::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

/* Input and message styles */
.add-task input,
.chat-input input {
  padding: 0.5rem;
  border: 1px solid #3a3a3a;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.05);
  color: white;
  font-size: 0.875rem;
}

.message p {
  margin: 0;
  padding: 0.5rem 0.75rem;
  border-radius: 12px;
  max-width: 85%;
  word-break: break-word;
}

/* Add these styles */
.grid {
  grid-auto-rows: 1fr;
}

/* Main participant highlight styles */
.border-blue {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Responsive styles */
@media (min-width: 768px) {
  .col-span-2.row-span-2 {
    grid-column: span 2;
    grid-row: span 2;
  }
}

@media (max-width: 767px) {
  .col-span-2.row-span-2 {
    grid-column: span 1;
    grid-row: span 1;
  }
  
  .h-24.w-24 {
    height: 3rem;
    width: 3rem;
  }
  
  .text-4xl {
    font-size: 1.5rem;
  }
  
  .text-xl {
    font-size: 1rem;
  }
}

/* Participant cell styles */
.bg-gray-800 {
  background-color: #1f2937;
}

.bg-gray-700 {
  background-color: #374151;
}

/* Gradient overlay for names */
.from-black\/70 {
  --tw-gradient-from: rgba(0, 0, 0, 0.7);
  --tw-gradient-to: transparent;
}

/* Main presenter styles */
.aspect-video {
  aspect-ratio: 16/9;
}

/* Fullscreen styles */
:fullscreen .h-24 {
  height: 8rem;
  width: 8rem;
}

:fullscreen .text-4xl {
  font-size: 3.75rem;
}

:fullscreen .text-xl {
  font-size: 1.875rem;
}
</style>
