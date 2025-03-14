<template>
  <div class="flex items-center justify-center p-4 bg-gray-800 border-t border-gray-700">
    <div class="flex space-x-2">
      <button
        @click="$emit('toggle-mute')"
        :class="[
          'rounded-full p-3 focus:outline-none',
          controls.isMuted ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-700 hover:bg-gray-600'
        ]"
        :title="controls.isMuted ? 'Unmute' : 'Mute'"
      >
        <MicOff v-if="controls.isMuted" class="h-5 w-5 text-white" />
        <Mic v-else class="h-5 w-5 text-white" />
      </button>

      <button
        @click="$emit('toggle-video')"
        :class="[
          'rounded-full p-3 focus:outline-none',
          controls.isVideoOff ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-700 hover:bg-gray-600'
        ]"
        :title="controls.isVideoOff ? 'Turn on camera' : 'Turn off camera'"
      >
        <VideoOff v-if="controls.isVideoOff" class="h-5 w-5 text-white" />
        <Video v-else class="h-5 w-5 text-white" />
      </button>

      <button
        @click="$emit('toggle-screen-share')"
        :class="[
          'rounded-full p-3 focus:outline-none',
          controls.isScreenSharing ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-700 hover:bg-gray-600'
        ]"
        title="Share screen"
      >
        <MonitorUp class="h-5 w-5 text-white" />
      </button>

      <button
        @click="$emit('toggle-sidebar')"
        :class="[
          'rounded-full p-3 focus:outline-none',
          controls.isSidebarOpen ? 'bg-blue hover:bg-blue' : 'bg-gray-700 hover:bg-gray-600'
        ]"
        :title="controls.isSidebarOpen ? 'Hide tasks' : 'Show tasks'"
      >
        <ListTodo class="h-5 w-5 text-white" />
      </button>

      <button
        @click="$emit('toggle-chat')"
        :class="[
          'rounded-full p-3 focus:outline-none',
          controls.isChatOpen ? 'bg-blue hover:bg-blue' : 'bg-gray-700 hover:bg-gray-600'
        ]"
        :title="controls.isChatOpen ? 'Hide chat' : 'Show chat'"
      >
        <MessageSquare class="h-5 w-5 text-white" />
      </button>

      <button
        @click="$emit('toggle-participants')"
        :class="[
          'rounded-full p-3 focus:outline-none',
          controls.isParticipantsOpen ? 'bg-blue hover:bg-blue' : 'bg-gray-700 hover:bg-gray-600'
        ]"
        :title="controls.isParticipantsOpen ? 'Hide participants' : 'Show participants'"
      >
        <Users class="h-5 w-5 text-white" />
      </button>

      <button
        @click="$emit('end-meeting')"
        class="rounded-full p-3 bg-red-500 hover:bg-red-600 focus:outline-none"
        title="End meeting"
      >
        <PhoneOff class="h-5 w-5 text-white" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { 
  Mic, 
  MicOff, 
  Video, 
  VideoOff, 
  PhoneOff, 
  MonitorUp, 
  ListTodo, 
  MessageSquare, 
  Users 
} from 'lucide-vue-next'

defineProps({
  controls: {
    type: Object,
    required: true
  }
})

defineEmits([
  'toggle-mute',
  'toggle-video',
  'toggle-screen-share',
  'toggle-sidebar',
  'toggle-chat',
  'toggle-participants',
  'end-meeting'
])
</script> 