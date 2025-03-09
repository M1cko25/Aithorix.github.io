<!-- src/Components/MeetingModal.vue -->
<script setup>
import { defineEmits, ref } from 'vue';
import { X, Calendar, Clock } from 'lucide-vue-next';

defineProps({
  show: Boolean
});

const emit = defineEmits(['close']);
const activeTab = ref('scheduled');

const scheduledMeetings = ref([
  { title: 'Team Sync', date: '2/23/2024', time: '10:00 AM', code: 'abc-123-xyz' },
  { title: 'Sprint Planning', date: '2/24/2024', time: '2:00 PM', code: 'def-456-uvw' }
]);

const closeModal = () => {
  emit('close');
};
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Meeting Details</h2>
        <button @click="closeModal"><X class="w-5 h-5 text-gray-600" /></button>
      </div>

      <div class="mt-4">
        <button @click="activeTab = 'scheduled'" :class="{'font-bold': activeTab === 'scheduled'}">Scheduled</button>
        <button @click="activeTab = 'new'" :class="{'font-bold': activeTab === 'new'}" class="ml-4">New Meeting</button>
      </div>

      <div v-if="activeTab === 'scheduled'" class="mt-4">
        <ul>
          <li v-for="meeting in scheduledMeetings" :key="meeting.code" class="border p-2 rounded-lg mt-2">
            <strong>{{ meeting.title }}</strong>
            <p><Calendar class="inline w-4 h-4" /> {{ meeting.date }}</p>
            <p><Clock class="inline w-4 h-4" /> {{ meeting.time }}</p>
          </li>
        </ul>
      </div>

      <div v-else class="mt-4">
        <p class="text-gray-600">Enter meeting details...</p>
      </div>
    </div>
  </div>
</template>
