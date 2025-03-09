<template>
  <div class="grid h-full bg-gray-900 p-2 md:p-4">
    <div class="grid grid-cols-4 gap-2 h-full">
      <!-- Main Presenter -->
      <MainPresenter 
        :isVideoOff="isVideoOff"
        :isMuted="isMuted"
        :isPinned="isPinned"
        :isFullscreen="isFullscreen"
        :localVideo="localVideo"
        @toggle-pin="$emit('toggle-pin')"
        @toggle-fullscreen="$emit('toggle-fullscreen')"
      />

      <!-- Participants Grid -->
      <ParticipantsGrid 
        :firstRowParticipants="firstRowParticipants"
        :middleRowParticipants="middleRowParticipants"
        :bottomRowParticipants="bottomRowParticipants"
        :remainingParticipants="remainingParticipants"
      />
    </div>
  </div>
</template>

<script setup>
import MainPresenter from './MainPresenter.vue'
import ParticipantsGrid from './ParticipantsGrid.vue'

defineProps({
  isVideoOff: Boolean,
  isMuted: Boolean,
  isPinned: Boolean,
  isFullscreen: Boolean,
  localVideo: Object,
  firstRowParticipants: Array,
  middleRowParticipants: Array,
  bottomRowParticipants: Array,
  remainingParticipants: Number
})

defineEmits(['toggle-pin', 'toggle-fullscreen'])
</script> 