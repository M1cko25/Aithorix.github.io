<template>
  <div class="col-span-1 grid grid-rows-3 gap-2">
    <Participant 
      v-if="firstRowParticipants[0]"
      :participant="firstRowParticipants[0]"
      :showVideo="true"
    />
    
    <Participant 
      v-if="middleRowParticipants[0]"
      :participant="middleRowParticipants[0]"
      :showVideo="false"
    />
    
    <OthersCount 
      :bottomRowParticipants="bottomRowParticipants"
      :remainingParticipants="remainingParticipants"
    />
  </div>
</template>

<script setup>
import Participant from './Participant.vue'
import OthersCount from './OthersCount.vue'

defineProps({
  firstRowParticipants: Array,
  middleRowParticipants: Array,
  bottomRowParticipants: Array,
  remainingParticipants: Number
})
</script> 