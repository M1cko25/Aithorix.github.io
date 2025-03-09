<template>
  <div class="p-4">
    <div class="space-y-2">
      <div v-for="p in participants" 
           :key="p.id"
           class="flex items-center justify-between p-2 bg-gray-700 rounded-lg">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
            {{ p.name[0] }}
          </div>
          <div>
            <div class="text-white">
              {{ p.name }}
              <span v-if="p.isMainUser">(You)</span>
              <span v-if="p.isHost" class="ml-2 text-xs bg-blue-600 px-2 py-0.5 rounded">
                Host
              </span>
            </div>
          </div>
        </div>
        <div class="flex gap-2">
          <component :is="p.isMuted ? 'MicOff' : 'Mic'"
                    class="h-4 w-4"
                    :class="p.isMuted ? 'text-red-500' : 'text-white'" />
          <component :is="p.isVideoOff ? 'VideoOff' : 'Video'"
                    class="h-4 w-4"
                    :class="p.isVideoOff ? 'text-red-500' : 'text-white'" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Mic, MicOff, Video, VideoOff } from 'lucide-vue-next'
defineProps(['participants'])
</script>