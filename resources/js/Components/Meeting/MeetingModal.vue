<!-- src/Components/MeetingModal.vue -->
<script setup>
import { ref, watch } from 'vue';
import { Video, Calendar, X, Copy } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';
import StateDisplay from '@/Components/StateDisplay.vue';

const props = defineProps({
  modelValue: Boolean
});

const emit = defineEmits(['update:modelValue']);

const showModal = ref(false);
const codeModal = ref(false);
const meetingCode = ref('');
const meetingName = ref('');
const isCreatingMeeting = ref(false);
const currentModal = ref({
  type: '',
  title: ''
});

const form = useForm({
  name: '',
  code: ''
});

const joinCode = ref('');

// Meeting Options
const meetingOptions = [
  {
    title: 'Start an instant meeting',
    description: 'Start a meeting right now',
    icon: Video,
    action: 'instant'
  },
  {
    title: 'Schedule a meeting',
    description: 'Schedule a meeting for a specific time',
    icon: Calendar,
    action: 'schedule'
  }
];

const handleMeetingOption = (action) => {
  showModal.value = true;
  currentModal.value = {
    type: action,
    title: action === 'instant' ? 'Start Instant Meeting' : 'Schedule Meeting'
  };
};

const closeModal = () => {
  showModal.value = false;
  currentModal.value = { type: '', title: '' };
  emit('update:modelValue', false);
};

const handleInstantMeeting = async () => {
  try {
    if (!form.name) {
      alert('Please enter a meeting name');
      return;
    }
    let response;
    if (!isCreatingMeeting.value) {
      isCreatingMeeting.value = true;
      response = await axios.post('/daily/create-room', {
        name: form.name.split(' ').join('_').trim(),
      });
    }

    if (response.data.error) {
      alert(response.data.error);
      return;
    }

    meetingCode.value = response.data.code;
    meetingName.value = response.data.name;
    codeModal.value = true;
    showModal.value = false;
    isCreatingMeeting.value = false;
    sessionStorage.setItem(`meeting_code_${response.data.name}`, response.data.code);
  } catch (error) {
    console.error('Error creating meeting:', error);
    alert(error.response?.data?.error || 'Failed to create meeting. Please try again.');
  }
};

const joinMeeting = () => {
  if (!joinCode.value) {
    alert('Please enter a meeting code');
    return;
  }
  
  const codePattern = /^[A-Z0-9]{4}-[A-Z0-9]{4}$/;
  if (!codePattern.test(joinCode.value)) {
    alert('Invalid code format. Please use format: XXXX-XXXX');
    return;
  }

  form.code = joinCode.value;
  form.post(route('meeting-room-post'), {
    preserveScroll: true,
    onSuccess: () => {
      joinCode.value = '';
      emit('update:modelValue', false);
    },
    onError: (errors) => {
      alert(errors.error || 'Failed to join meeting');
    }
  });
};

const goToMeeting = () => {
  if (!meetingName.value || !meetingCode.value) {
    alert('Invalid meeting information');
    return;
  }

  try {
    const fullRoomName = `${meetingName.value}-${meetingCode.value}`;
    window.location.href = route('meeting-room', { name: fullRoomName });
  } catch (error) {
    console.error('Error navigating to meeting:', error);
    alert('Failed to join meeting. Please try again.');
  }
};

const codeCopied = ref(false);
const stateDisplayMessage = ref('');
const stateDisplayState = ref('success');

const copyMeetingCode = () => {
  navigator.clipboard.writeText(meetingCode.value)
    .then(() => {
      const copyButton = document.querySelector('.copy-button');
      copyButton.classList.add('text-green-600');
      setTimeout(() => {
        copyButton.classList.remove('text-green-600');
      }, 1000);
      stateDisplayMessage.value = 'Meeting code copied to clipboard';
      codeCopied.value = true;
    }).catch(err => {
      stateDisplayState.value = 'error';
      stateDisplayMessage.value = 'Failed to copy code';
    });
};

</script>

<template>
  <Modal :modelValue="modelValue" @update:modelValue="$emit('update:modelValue', $event)" title="Meeting">
    <div class="w-full p-4">
      <StateDisplay v-if="codeCopied" :state="stateDisplayState" :message="stateDisplayMessage"/>
      
      <!-- Join Meeting Section -->
      <div class="flex gap-3 mb-6">
        <input 
          v-model="joinCode"
          type="text" 
          placeholder="Enter meeting code (e.g., ABCD-1234)" 
          class="flex-1 border rounded-lg px-3 py-2 text-sm outline-none"
          pattern="[A-Z0-9]{4}-[A-Z0-9]{4}"
        />
        <button 
          @click="joinMeeting" 
          class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          Join
        </button>
      </div>

      <div class="space-y-2">
        <button 
          v-for="option in meetingOptions" 
          :key="option.action"
          @click="handleMeetingOption(option.action)"
          class="w-full px-4 py-3 text-left hover:bg-gray-50 flex items-start gap-3 transition-colors rounded-lg border"
        >
          <component :is="option.icon" class="w-5 h-5 text-violet-600 mt-0.5" />
          <div>
            <div class="text-sm font-medium text-gray-800">{{ option.title }}</div>
            <div class="text-xs text-gray-500">{{ option.description }}</div>
          </div>
        </button>
      </div>
    </div>
  </Modal>

  <!-- Meeting Creation Modal -->
  <Modal v-model:modelValue="showModal" :title="currentModal.title">
    <div class="p-6">
      <template v-if="currentModal.type === 'instant'">
        <p class="text-sm text-gray-600 mb-4">  
          Start a meeting right now. You'll receive a unique code to share with participants.
        </p>
        <form @submit.prevent="handleInstantMeeting" class="space-y-4">
          <div>
            <label class="text-sm font-medium block mb-1">Meeting name</label>
            <input 
              type="text" 
              class="w-full border rounded-md px-3 py-2 text-sm"
              placeholder="My Instant Meeting"
              v-model="form.name"
              required
            />
          </div>
          <div class="flex justify-end gap-2">
            <button 
              type="button"
              class="px-4 py-2 text-sm border rounded-md hover:bg-gray-100"
              @click="closeModal"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 text-sm bg-violet-600 text-white rounded-md hover:bg-violet-700"
            >
              Start Meeting
            </button>
          </div>
        </form>
      </template>

      <template v-if="currentModal.type === 'schedule'">
        <p class="text-sm text-gray-600 mb-4">
          Schedule a meeting for a specific date and time.
        </p>
        <form class="space-y-4">
          <div>
            <label class="text-sm font-medium block mb-1">Meeting name</label>
            <input 
              type="text" 
              class="w-full border rounded-md px-3 py-2 text-sm"
              placeholder="Scheduled Meeting"
            />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium block mb-1">Date</label>
              <input 
                type="date" 
                class="w-full border rounded-md px-3 py-2 text-sm"
              />
            </div>
            <div>
              <label class="text-sm font-medium block mb-1">Time</label>
              <input 
                type="time" 
                class="w-full border rounded-md px-3 py-2 text-sm"
              />
            </div>
          </div>
          <div class="flex justify-end gap-2">
            <button 
              type="button"
              class="px-4 py-2 text-sm border rounded-md hover:bg-gray-100"
              @click="closeModal"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="px-4 py-2 text-sm bg-violet-600 text-white rounded-md hover:bg-violet-700"
            >
              Schedule Meeting
            </button>
          </div>
        </form>
      </template>
    </div>
  </Modal>

  <!-- Meeting Code Modal -->
  <Modal v-model:modelValue="codeModal" title="Meeting Created">
    <div class="w-full text-center p-4">
      <p class="mb-2">Your meeting code is:</p>
      <div class="bg-gray-50 p-4 rounded-lg mb-4 flex flex-row justify-center gap-4">
        <p class="text-2xl font-bold text-violet-600">{{ meetingCode }}</p>
        <button @click="copyMeetingCode" class="copy-button">
          <Copy class="w-5 h-5 text-violet-600" />
        </button>
      </div>
      <p class="text-sm text-gray-600 mb-6">Share this code with your team members to join the meeting.</p>
      <div class="flex justify-end gap-4">
        <button 
          @click="codeModal = false" 
          class="px-4 py-2 text-sm border rounded-md hover:bg-gray-100"
        >
          Close
        </button>
        <button 
          @click="goToMeeting" 
          class="px-4 py-2 text-sm bg-violet-600 text-white rounded-md hover:bg-violet-700"
          :class="`${isCreatingMeeting ? 'opacity-50' : ''}`"
          :disabled="isCreatingMeeting"
        >
          {{  isCreatingMeeting ? 'Joining...' : 'Join Meeting' }}
        </button>
      </div>
    </div>
  </Modal>
</template>
