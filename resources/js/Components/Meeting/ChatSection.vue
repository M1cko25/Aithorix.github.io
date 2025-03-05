<template>
  <div class="flex flex-col h-full">
    <div class="flex-1 p-4 space-y-4 overflow-y-auto">
      <div v-for="msg in messages" 
           :key="msg.id"
           :class="['flex', msg.isFromUser ? 'justify-end' : 'justify-start']">
        <div :class="[
          'max-w-[80%] p-3 rounded-lg',
          msg.isFromUser ? 'bg-blue-500' : 'bg-gray-700'
        ]">
          <div v-if="!msg.isFromUser" class="text-sm text-gray-300 mb-1">
            {{ msg.sender }}
          </div>
          <div class="text-white">{{ msg.text }}</div>
        </div>
      </div>
    </div>
    
    <div class="p-4 border-t border-gray-700">
      <div class="flex gap-2">
        <input v-model="newMessage"
               @keyup.enter="sendMessage"
               placeholder="Type message..."
               class="flex-1 px-3 py-2 bg-gray-700 rounded-md text-white" />
        <button @click="sendMessage" class="p-2 bg-blue rounded-md text-white">
          <Send class="h-4 w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Send } from 'lucide-vue-next'

defineProps(['messages'])
const emit = defineEmits(['send'])
const newMessage = ref('')

const sendMessage = () => {
  if (newMessage.value.trim()) {
    emit('send', newMessage.value)
    newMessage.value = ''
  }
}
</script>