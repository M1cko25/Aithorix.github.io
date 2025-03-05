<script setup>
import { ref, nextTick } from 'vue';
import { X, Send } from 'lucide-vue-next';

const isOpen = ref(true);
const input = ref('');
const messages = ref([]);
const isLoading = ref(false);
const messagesContainer = ref(null);
const chatWidth = ref(380); // Default width
const isResizing = ref(false);
const startX = ref(0);
const startWidth = ref(0);

const scrollToBottom = async () => {
  await nextTick();
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

const sendMessage = async () => {
  if (!input.value.trim() || isLoading.value) return;

  const userMessage = input.value;
  input.value = '';
  
  // Add user message
  messages.value.push({ role: 'user', content: userMessage });
  await scrollToBottom();
  
  // Simulate AI response
  isLoading.value = true;
  setTimeout(() => {
    messages.value.push({
      role: 'assistant',
      content: "I'm here to help! What would you like to know?"
    });
    isLoading.value = false;
    scrollToBottom();
  }, 1000);
};

const startResize = (e) => {
  isResizing.value = true;
  startX.value = e.clientX;
  startWidth.value = chatWidth.value;
  document.addEventListener('mousemove', handleResize);
  document.addEventListener('mouseup', stopResize);
};

const handleResize = (e) => {
  if (!isResizing.value) return;
  
  const diff = startX.value - e.clientX;
  const newWidth = Math.max(300, Math.min(800, startWidth.value + diff));
  chatWidth.value = newWidth;
};

const stopResize = () => {
  isResizing.value = false;
  document.removeEventListener('mousemove', handleResize);
  document.removeEventListener('mouseup', stopResize);
};
</script>

<template>
  <div>
    <!-- Minimized chat button -->
    <button
      v-if="!isOpen"
      @click="isOpen = true"
      class="fixed bottom-6 right-6 rounded-full w-14 h-14 shadow-lg bg-white flex items-center justify-center hover:shadow-xl transition-shadow"
    >
      <div class="chat-logo-small">
        <span>L</span>
      </div>
    </button>

    <!-- Chat interface -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-4"
    >
      <div
        v-if="isOpen"
        class="fixed bottom-6 right-6 bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100"
        :style="{ width: `${chatWidth}px` }"
      >
        <!-- Resize handle -->
        <div
          class="absolute left-0 top-0 bottom-0 w-1 cursor-ew-resize hover:bg-blue-500/20 transition-colors"
          @mousedown="startResize"
        ></div>

        <!-- Header -->
        <div class="relative h-48 bg-gradient-to-b from-gray-50 to-white px-6 pt-4 pb-16">
          <button
            @click="isOpen = false"
            class="absolute right-4 top-4 p-2 rounded-full hover:bg-black/5 transition-colors"
          >
            <X class="w-5 h-5 text-gray-500" />
          </button>
          
          <div class="flex flex-col items-center pt-6">
            <div class="chat-logo">
              <span>L</span>
            </div>
            <h2 class="mt-4 text-xl font-semibold text-gray-900">Hi, I'm LIRA</h2>
            <p class="mt-1 text-gray-600">How can I help you?</p>
          </div>
        </div>

        <!-- Messages -->
        <div class="h-[300px] overflow-y-auto px-6 py-4" ref="messagesContainer">
          <div v-if="messages.length === 0" class="flex items-center justify-center h-full">
            <p class="text-gray-400 text-sm">Start a conversation...</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="(message, index) in messages"
              :key="index"
              class="flex"
              :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
            >
              <div
                class="max-w-[80%] rounded-2xl px-4 py-2"
                :class="message.role === 'user' ? 'bg-blue text-white' : 'bg-gray-100 text-gray-900'"
              >
                {{ message.content }}
              </div>
            </div>
          </div>
        </div>

        <!-- Input area -->
         
        <div class="border-t bg-white px-6 py-4">
          <form @submit.prevent="sendMessage" class="relative">
            <input
              v-model="input"
              type="text"
              placeholder="Write something..."
              class="w-full pl-4 pr-12 py-3 bg-gray-50 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20 transition-all"
              :disabled="isLoading"
            />
            <button
              type="submit"
              :disabled="!input.trim() || isLoading"
              class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-lg text-blue-600 hover:bg-blue-50 disabled:opacity-50 disabled:hover:bg-transparent transition-colors"
            >
              <Send class="w-5 h-5" :class="{ 'animate-pulse': isLoading }" />
            </button>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.chat-logo {
  width: 64px;
  height: 64px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-logo::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: #fff;
  border: 2px solid #000;
  border-radius: 16px;
  transform: rotate(45deg);
}

.chat-logo::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  width: 16px;
  height: 16px;
  background-color: #fff;
  border-right: 2px solid #000;
  border-bottom: 2px solid #000;
  transform: translateX(-50%) rotate(45deg);
}

.chat-logo span {
  position: relative;
  z-index: 1;
  font-size: 28px;
  font-weight: bold;
}

.chat-logo-small {
  width: 32px;
  height: 32px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-logo-small::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: #fff;
  border: 1.5px solid #000;
  border-radius: 8px;
  transform: rotate(45deg);
}

.chat-logo-small::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 50%;
  width: 8px;
  height: 8px;
  background-color: #fff;
  border-right: 1.5px solid #000;
  border-bottom: 1.5px solid #000;
  transform: translateX(-50%) rotate(45deg);
}

.chat-logo-small span {
  position: relative;
  z-index: 1;
  font-size: 14px;
  font-weight: bold;
}
</style>