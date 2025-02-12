<script setup>
import { Check, X,  } from 'lucide-vue-next';
defineProps({
    state: {
        type: String,
        default: 'neutral'
    },
    message: {
        type: String,
        default: 'Success'
    },
    icon: {
        type: String,
        default: ''
    }
})
</script>
<template>
    <div v-motion="pop" :class="`absolute popup top-0 right-0 flex 
    flex-row gap-6 m-4 md:p-6 text-white rounded-lg shadow ${state == 'success' ? 'bg-success'
    : (state == 'error') ? 'bg-error' : 'bg-neutral'}`">
      <Check v-if="state == 'success' && !icon"/>
      <X v-else-if="state == 'error' && !icon"/>
      <img v-if="icon" :src="icon">
      <h1>{{ message }}</h1>
    </div>
</template>
<style scoped>
.popup {
  animation: timer 5s forwards;
}

@keyframes timer {
  0%, 80% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}
</style>