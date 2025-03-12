<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Clock, ChevronDown, ChevronLeft, ChevronRight, Video, Calendar, Clock3, CalendarPlus, X, Users, Copy } from 'lucide-vue-next';
import { format, isToday, isThisWeek, parseISO, isAfter, isSameDay, addDays } from 'date-fns';
import axios from 'axios';
import { usePage, useForm } from '@inertiajs/vue3';
import Icons from '../../Icons'
import Modal from '@/js/Components/Modal.vue';
import StateDisplay from '@/js/Components/StateDisplay.vue';


const page = usePage().props;
  // Sample meeting data with ISO dates
const meetings = ref([
  { 
    id: 1,
    title: 'Team Meeting', 
    time: '10:00 AM', 
    duration: '30 min',
    date: '2024-12-15',
    type: 'instant'
  },
  { 
    id: 2,
    title: 'Team Planning', 
    time: '2:00 PM', 
    duration: '60 min',
    date: '2024-12-16',
    type: 'scheduled'
  },
  { 
    id: 3,
    title: 'Project Review', 
    time: '11:30 AM', 
    duration: '45 min',
    date: '2024-12-19',
    type: 'ready'
  },
  { 
    id: 4,
    title: 'Client Meeting', 
    time: '3:00 PM', 
    duration: '60 min',
    date: '2024-12-20',
    type: 'scheduled'
  },
  { 
    id: 5,
    title: 'Sprint Planning', 
    time: '9:00 AM', 
    duration: '90 min',
    date: '2024-12-23',
    type: 'ready'
  }
]);

// Add these new refs for example meetings
const laterMeetings = ref([
  {
    id: 'later-1',
    title: 'Quick Team Sync',
    description: 'Quick sync with the development team',
    duration: '30 min',
    type: 'ready'
  },
  {
    id: 'later-2',
    title: 'Project Review',
    description: 'Monthly project status review',
    duration: '60 min',
    type: 'ready'
  }
]);

const scheduledMeetings = ref([
  {
    id: 'scheduled-1',
    title: 'Sprint Planning',
    date: '2024-03-20',
    time: '10:00',
    duration: '90 min',
    type: 'scheduled'
  },
  {
    id: 'scheduled-2',
    title: 'Client Demo',
    date: '2024-03-21',
    time: '14:00',
    duration: '60 min',
    type: 'scheduled'
  }
]);

// Add these new example meetings to the script section
const upcomingMeetings = ref([
  {
    id: 'upcoming-1',
    title: 'Daily Standup',
    time: '9:00 AM',
    duration: '15 min',
    date: format(new Date(), 'yyyy-MM-dd'), // Today
    type: 'ready',
    participants: 8
  },
  {
    id: 'upcoming-2',
    title: 'Product Demo',
    time: '2:30 PM',
    duration: '45 min',
    date: format(new Date(), 'yyyy-MM-dd'), // Today
    type: 'scheduled',
    participants: 12
  },
  {
    id: 'upcoming-3',
    title: 'Design Review',
    time: '11:00 AM',
    duration: '60 min',
    date: format(addDays(new Date(), 1), 'yyyy-MM-dd'), // Tomorrow
    type: 'scheduled',
    participants: 6
  }
]);

// Statistics helper functions
const getTodayMeetings = () => {
  return meetings.value.filter(meeting => isToday(parseISO(meeting.date))).length;
};

const getWeekMeetings = () => {
  return meetings.value.filter(meeting => isThisWeek(parseISO(meeting.date))).length;
};

const getReadyMeetings = () => {
  return meetings.value.filter(meeting => meeting.type === 'ready').length;
};

// Calendar helper functions
const hasMeeting = (day) => {
  const checkDate = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), day);
  return meetings.value.some(meeting => isSameDay(parseISO(meeting.date), checkDate));
};

const getMeetingCount = (day) => {
  return meetings.value.filter(meeting => {
    const meetingDay = parseISO(meeting.date).getDate();
    return meetingDay === day;
  }).length;
};

// Format helpers
const formatDate = (dateString) => {
  const date = parseISO(dateString);
  return format(date, 'MMM d, yyyy');
};

// New Meeting Menu State
const showNewMeetingMenu = ref(false);
const newMeetingButton = ref(null);

// Modal State
const showModal = ref(false);
const codeModal = ref(false);
const meetingCode = ref('');
const meetingName = ref('');
const isCreatingMeeting = ref(false);
const currentModal = ref({
  type: '',
  title: ''
});

// Meeting Options
const meetingOptions = [
  {
    title: 'Start an instant meeting',
    description: 'Start a meeting right now',
    icon: Video,
    action: 'instant'
  },
  {
    title: 'Create meeting for later',
    description: 'Create a meeting you can start anytime',
    icon: Calendar,
    action: 'create'
  },
  {
    title: 'Schedule a meeting',
    description: 'Schedule a meeting for a specific time',
    icon: CalendarPlus,
    action: 'schedule'
  }
];

// Handle clicking outside of menu
const handleClickOutside = (event) => {
  if (newMeetingButton.value && !newMeetingButton.value.contains(event.target)) {
    showNewMeetingMenu.value = false;
  }
};

const handleModalSubmit = (type) => {
  console.log(`Handling ${type} meeting submission`);
  closeModal();
};

// Toggle new meeting menu
const toggleNewMeetingMenu = () => {
  showNewMeetingMenu.value = !showNewMeetingMenu.value;
};

// Handle meeting option selection
const handleMeetingOption = (action) => {
  showNewMeetingMenu.value = false;
  showModal.value = true;
  
  switch(action) {
    case 'instant':
      currentModal.value = {
        type: 'instant',
        title: 'Start Instant Meeting'
      };
      break;
    case 'create':
      currentModal.value = {
        type: 'create',
        title: 'Create Meeting'
      };
      break;
    case 'schedule':
      currentModal.value = {
        type: 'schedule',
        title: 'Schedule Meeting'
      };
      break;
  }
};

// Close modal
const closeModal = () => {
  showModal.value = false;
  currentModal.value = { type: '', title: '' };
};

// Calendar functionality
const currentMonth = ref(new Date())
const daysInMonth = computed(() => {
  const year = currentMonth.value.getFullYear()
  const month = currentMonth.value.getMonth()
  return new Date(year, month + 1, 0).getDate()
})

const firstDayOfMonth = computed(() => {
  const year = currentMonth.value.getFullYear()
  const month = currentMonth.value.getMonth()
  return new Date(year, month, 1).getDay()
})

const previousMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1)
}

const nextMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1)
}

const monthYearDisplay = computed(() => {
  return format(currentMonth.value, 'MMMM yyyy')
})

// Add these refs and computed properties
const selectedDate = ref(new Date());

const filteredMeetings = computed(() => {
  if (!selectedDate.value) return upcomingMeetings.value;
  
  return upcomingMeetings.value.filter(meeting => {
    const meetingDate = parseISO(meeting.date);
    return isSameDay(meetingDate, selectedDate.value);
  });
});

// Update calendar helper functions
const isSelectedDay = (day) => {
  return isSameDay(
    new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), day),
    selectedDate.value
  );
};

const handleDateSelect = (day) => {
  selectedDate.value = new Date(
    currentMonth.value.getFullYear(),
    currentMonth.value.getMonth(),
    day
  );
};

const viewAllMeetings = () => {
  selectedDate.value = new Date(); // Reset to today
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const form = useForm({
  name: '',
  code: ''
})

// Add this ref for the join input
const joinCode = ref('');

// Update the joinMeeting function
const joinMeeting = () => {
  if (!joinCode.value) {
    alert('Please enter a meeting code');
    return;
  }
  
  // Validate code format
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
    },
    onError: (errors) => {
      alert(errors.error || 'Failed to join meeting');
    }
  });
};

// Update the handleInstantMeeting function
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
        name: form.name.split(' ').join('_'),
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

const goToMeeting = () => {
  if (!meetingName.value || !meetingCode.value) {
    alert('Invalid meeting information');
    return;
  }

  try {
    const codeWithoutHyphen = meetingCode.value.replace('-', '');
    const fullRoomName = `${meetingName.value}_${codeWithoutHyphen}`;

    window.location.href = route('meeting-room', { name: fullRoomName });
  } catch (error) {
    console.error('Error navigating to meeting:', error);
    alert('Failed to join meeting. Please try again.');
  }
};
const codeCopied = ref(false);
const stateDisplayMessage = ref('')
const stateDisplayState = ref('success');
const copyMeetingCode = () => {
  navigator.clipboard.writeText(meetingCode.value)
    .then(() => {
      // Optional: Add a visual feedback that code was copied
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
  <Head title="Meeting Home" />

  <StateDisplay v-if="codeCopied" :state="stateDisplayState" :message="stateDisplayMessage"/>
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
  <div class="min-h-screen bg-gray-50 p-4">
    <div class="container max-w-5xl mx-auto">
      <div>
        <Link :href="route('home')" preserve-scroll class="flex flex-row 
        items-center gap-4 top-4 left-4">
            <img :src="Icons.leftIcon">
            <p>Back to Home</p>
        </Link>
      </div>
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-3 text-gray-800">Meet, connect, and collaborate in real-time</h1>
        <p class="text-base text-gray-600 max-w-2xl mx-auto">Professional video conferencing that brings teams together, wherever they are</p>
      </div>
      
      <!-- Action Buttons -->
      <div class="flex gap-3 mb-8 relative">
        <div class="relative">
          <button 
            class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 shadow-sm transition-colors"
            @click="toggleNewMeetingMenu"
            ref="newMeetingButton"
          >
            <Video class="w-5 h-5" />
            New meeting
            <ChevronDown class="w-5 h-5" :class="{ 'rotate-180': showNewMeetingMenu }" />
          </button>

          <!-- New Meeting Options Menu -->
          <div v-if="showNewMeetingMenu" 
            class="absolute top-full left-0 mt-2 w-80 bg-white rounded-lg shadow-xl border py-2 z-10">
            <button 
              v-for="(option, index) in meetingOptions" 
              :key="index"
              @click="handleMeetingOption(option.action)"
              class="w-full px-4 py-3 text-left hover:bg-gray-50 flex items-start gap-3 transition-colors"
            >
              <component :is="option.icon" class="w-5 h-5 text-violet-600 mt-0.5" />
              <div>
                <div class="text-sm font-medium text-gray-800">{{ option.title }}</div>
                <div class="text-xs text-gray-500">{{ option.description }}</div>
              </div>
            </button>
          </div>
        </div>

        <div class="border rounded-lg flex-1 p-2 flex items-center bg-white shadow-sm">
          <input 
            v-model="joinCode"
            type="text" 
            placeholder="Enter meeting code (e.g., ABCD-1234)" 
            class="w-full outline-none text-sm"
            pattern="[A-Z0-9]{4}-[A-Z0-9]{4}"
          />
        </div>
        <button 
          @click="joinMeeting" 
          class="bg-white hover:bg-gray-50 text-gray-700 rounded-lg px-6 py-3 text-sm font-medium shadow-sm transition-colors"
        >
          Join
        </button>
      </div>

      <!-- Statistics Section -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl border shadow-sm">
          <h3 class="text-sm text-gray-500 mb-1">Total Meetings</h3>
          <p class="text-2xl font-semibold text-gray-800">{{ meetings.length }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border shadow-sm">
          <h3 class="text-sm text-gray-500 mb-1">Scheduled Today</h3>
          <p class="text-2xl font-semibold text-gray-800">{{ getTodayMeetings() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border shadow-sm">
          <h3 class="text-sm text-gray-500 mb-1">This Week</h3>
          <p class="text-2xl font-semibold text-gray-800">{{ getWeekMeetings() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border shadow-sm">
          <h3 class="text-sm text-gray-500 mb-1">Ready to Start</h3>
          <p class="text-2xl font-semibold text-gray-800">{{ getReadyMeetings() }}</p>
        </div>
      </div>
      
      <!-- Meeting Panels -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upcoming Meetings Panel -->
        <div class="lg:col-span-2 border rounded-xl overflow-hidden bg-white shadow-sm">
          <div class="flex items-center justify-between bg-gray-50 p-4 border-b">
            <h2 class="text-base font-semibold text-gray-800">
              Upcoming meetings
            </h2>
            <div class="flex gap-2">
              <button 
                @click="viewAllMeetings"
                class="text-sm text-violet-600 hover:text-violet-700 font-medium transition-colors"
              >
                View all
              </button>
            </div>
          </div>
          
          <div class="divide-y">
            <template v-if="filteredMeetings.length > 0">
              <div v-for="meeting in filteredMeetings" 
                   :key="meeting.id" 
                   class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                  <div class="flex items-center gap-2">
                    <div class="text-sm font-medium text-gray-900">{{ meeting.title }}</div>
                    <div class="text-xs px-2 py-0.5 rounded-full"
                         :class="{
                           'bg-violet-100 text-violet-700': meeting.type === 'ready',
                           'bg-emerald-100 text-emerald-700': meeting.type === 'scheduled'
                         }">
                      {{ meeting.type === 'ready' ? 'Ready to start' : 'Scheduled' }}
                    </div>
                  </div>
                  <button class="text-sm text-violet-600 hover:text-violet-700 font-medium transition-colors">
                    {{ meeting.type === 'ready' ? 'Start' : 'Join' }}
                  </button>
                </div>
                <div class="flex items-center gap-3 text-gray-500">
                  <div class="flex items-center gap-1">
                    <Clock class="w-4 h-4" />
                    <span class="text-xs">{{ meeting.time }}</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <Clock3 class="w-4 h-4" />
                    <span class="text-xs">{{ meeting.duration }}</span>
                  </div>
                  <div v-if="meeting.type === 'scheduled'" class="flex items-center gap-1">
                    <Calendar class="w-4 h-4" />
                    <span class="text-xs">{{ formatDate(meeting.date) }}</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <Users class="w-4 h-4" />
                    <span class="text-xs">{{ meeting.participants }} participants</span>
                  </div>
                </div>
              </div>
            </template>
            <div v-else class="p-4 text-center text-gray-500">
              No meetings scheduled for this date
            </div>
          </div>
        </div>
        
        <!-- Calendar Panel -->
        <div class="border rounded-xl overflow-hidden bg-white shadow-sm">
          <div class="flex items-center justify-between bg-gray-50 p-4 border-b">
            <h2 class="text-base font-semibold text-gray-800">{{ monthYearDisplay }}</h2>
            <div class="flex items-center gap-2">
              <button @click="previousMonth" class="p-1 hover:bg-gray-200 rounded-full transition-colors">
                <ChevronLeft class="w-5 h-5 text-gray-600" />
              </button>
              <button @click="nextMonth" class="p-1 hover:bg-gray-200 rounded-full transition-colors">
                <ChevronRight class="w-5 h-5 text-gray-600" />
              </button>
            </div>
          </div>
          
          <div class="p-4">
            <!-- Calendar Days Header -->
            <div class="grid grid-cols-7 gap-1 mb-2">
              <div v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="day" 
                class="text-xs font-medium text-gray-500 text-center">
                {{ day }}
              </div>
            </div>
            
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1">
              <div v-for="i in firstDayOfMonth" :key="`empty-${i}`" 
                class="aspect-square">
              </div>
              
              <div v-for="day in daysInMonth" 
                   :key="day" 
                   @click="handleDateSelect(day)"
                   class="aspect-square flex flex-col items-center justify-center text-sm relative cursor-pointer transition-colors"
                   :class="{ 
                     'bg-violet-100 text-violet-900 hover:bg-violet-200': hasMeeting(day),
                     'bg-violet-500 text-white': isSelectedDay(day),
                     'hover:bg-gray-50': !hasMeeting(day) && !isSelectedDay(day),
                     'font-medium': hasMeeting(day) || isSelectedDay(day)
                   }"
              >
                {{ day }}
                <div v-if="hasMeeting(day)" 
                     class="absolute bottom-1 left-1/2 transform -translate-x-1/2 flex gap-0.5">
                  <div class="w-1 h-1 rounded-full"
                       :class="isSelectedDay(day) ? 'bg-white' : 'bg-violet-500'">
                  </div>
                  <div v-if="getMeetingCount(day) > 1"
                       class="w-1 h-1 rounded-full"
                       :class="isSelectedDay(day) ? 'bg-white opacity-75' : 'bg-violet-500 opacity-75'">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal -->
      <div v-if="showModal" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-20"
        @click="closeModal"
      >
        <!-- Modal Content -->
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" @click.stop>
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-lg font-semibold">{{ currentModal.title }}</h3>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Instant Meeting Modal Content -->
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
                <div>
                  <label class="text-sm font-medium block mb-1">Duration</label>
                  <select class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="15">15 minutes</option>
                    <option value="30">30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">1 hour</option>
                  </select>
                </div>
                <div class="flex justify-end gap-2">
                  <button 
                    type="button"
                    class="btn-cancel"
                    @click="closeModal"
                  >
                    Cancel
                  </button>
                  <button type="submit" class="btn-primary">
                    Start Meeting
                  </button>
                </div>
              </form>
            </template>

            <!-- Create Meeting Modal Content -->
            <template v-if="currentModal.type === 'create'">
              <p class="text-sm text-gray-600 mb-4">
                Create a meeting that you can start at any time.
              </p>
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium block mb-1">Meeting name</label>
                  <input 
                    type="text" 
                    class="w-full border rounded-md px-3 py-2 text-sm"
                    placeholder="Team Meeting"
                    v-model="meetingName"
                  />
                </div>
                <div>
                  <label class="text-sm font-medium block mb-1">Description (optional)</label>
                  <textarea 
                    class="w-full border rounded-md px-3 py-2 text-sm h-24 resize-none"
                    placeholder="Add meeting description..."
                  ></textarea>
                </div>
                <div>
                  <label class="text-sm font-medium block mb-1">Duration</label>
                  <select class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="15">15 minutes</option>
                    <option value="30">30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">1 hour</option>
                    <option value="90">1.5 hours</option>
                    <option value="120">2 hours</option>
                  </select>
                </div>

                <!-- Example meetings -->
                <div class="border rounded-lg mt-4">
                  <div class="p-3 bg-gray-50 border-b">
                    <h4 class="text-sm font-medium text-gray-700">Meeting for later</h4>
                  </div>
                  <div class="divide-y">
                    <div v-for="meeting in laterMeetings" 
                         :key="meeting.id"
                         class="p-3 hover:bg-gray-50">
                      <div class="flex justify-between items-start">
                        <div>
                          <h5 class="text-sm font-medium text-gray-900">{{ meeting.title }}</h5>
                          <p class="text-xs text-gray-500 mt-1">{{ meeting.description }}</p>
                        </div>
                        <Link :href="route('meeting-conference')" class="px-4 py-2 text-sm bg-violet-500 text-white rounded-md hover:bg-violet-600">
  Start
</Link>
                      </div>
                      <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                          <Clock3 class="w-3 h-3" />
                          {{ meeting.duration }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end gap-2">
                  <button 
                    class="px-4 py-2 text-sm border rounded-md hover:bg-gray-100"
                    @click="closeModal"
                  >
                    Cancel
                  </button>
                  <button class="px-4 py-2 text-sm bg-violet-500 text-white rounded-md hover:bg-violet-600">
                    Create meeting
                  </button>
                </div>
              </div>
            </template>

            <!-- Schedule Meeting Modal Content -->
            <template v-if="currentModal.type === 'schedule'">
              <p class="text-sm text-gray-600 mb-4">
                Schedule a meeting for a specific date and time.
              </p>
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium block mb-1">Meeting name</label>
                  <input 
                    type="text" 
                    class="w-full border rounded-md px-3 py-2 text-sm"
                    placeholder="Scheduled Team Meeting"
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
                <div>
                  <label class="text-sm font-medium block mb-1">Duration</label>
                  <select class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="30">30 minutes</option>
                    <option value="60">1 hour</option>
                    <option value="90">1.5 hours</option>
                    <option value="120">2 hours</option>
                  </select>
                </div>

                <!-- Example scheduled meetings -->
                <div class="border rounded-lg mt-4">
                  <div class="p-3 bg-gray-50 border-b">
                    <h4 class="text-sm font-medium text-gray-700">Upcoming scheduled meetings</h4>
                  </div>
                  <div class="divide-y">
                    <div v-for="meeting in scheduledMeetings" 
                         :key="meeting.id"
                         class="p-3 hover:bg-gray-50">
                      <div class="flex justify-between items-start">
                        <div>
                          <h5 class="text-sm font-medium text-gray-900">{{ meeting.title }}</h5>
                          <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-gray-500 flex items-center gap-1">
                              <Calendar class="w-3 h-3" />
                              {{ formatDate(meeting.date) }}
                            </span>
                            <span class="text-xs text-gray-500 flex items-center gap-1">
                              <Clock class="w-3 h-3" />
                              {{ meeting.time }}
                            </span>
                            <span class="text-xs text-gray-500 flex items-center gap-1">
                              <Clock3 class="w-3 h-3" />
                              {{ meeting.duration }}
                            </span>
                          </div>
                        </div>
                        <div class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                          Scheduled
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end gap-2">
                  <button 
                    class="px-4 py-2 text-sm border rounded-md hover:bg-gray-100"
                    @click="closeModal"
                  >
                    Cancel
                  </button>
                  <button class="px-4 py-2 text-sm bg-violet-500 text-white rounded-md hover:bg-violet-600">
                    Schedule meeting
                  </button>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.aspect-square {
  aspect-ratio: 1 / 1;
}
</style>

