<template>
  <div class="col-span-3 grid grid-rows-3 gap-2">
    <div class="row-span-3 relative rounded-lg overflow-hidden bg-gray-800">
      <template v-if="!isVideoOff">
        <div class="absolute inset-0">
          <div class="w-full h-full bg-gradient-to-b from-black/30 to-black/60"></div>
          <video
            :ref="el => { if (el) localVideo = el }"
            class="w-full h-full object-cover mirror"
            autoplay
            muted
            playsinline
          ></video>
        </div>
        <div class="absolute bottom-2 left-2 flex items-center gap-2">
          <span class="text-sm text-white font-medium">You (Host)</span>
        </div>
      </template>

      <template v-else>
        <div class="absolute inset-0 flex items-center justify-center">
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
      </template>

      <ControlsOverlay 
        :isPinned="isPinned"
        :isFullscreen="isFullscreen"
        :isMuted="isMuted"
        :isVideoOff="isVideoOff"
        @toggle-pin="$emit('toggle-pin')"
        @toggle-fullscreen="$emit('toggle-fullscreen')"
      />
    </div>
  </div>
</template>

<script setup>
import ControlsOverlay from './ControlsOverlay.vue'

defineProps({
  isVideoOff: Boolean,
  isMuted: Boolean,
  isPinned: Boolean,
  isFullscreen: Boolean,
  localVideo: Object
})

defineEmits(['toggle-pin', 'toggle-fullscreen'])
</script> 