<script setup>
import { ref } from 'vue';
import Header from '../../Components/Header.vue';
import Sidebar from '../../Components/Sidebar.vue';
import { Search, ChevronDown, Star } from 'lucide-vue-next';

const sortOrder = ref('Newest');
const searchQuery = ref('');

const meetings = ref([
  {
    id: 3,
    title: 'Daily Stand-Up 3',
    date: 'Dec. 5',
    time: '11:00am - 11:20am',
    timeAgo: '15 hours ago',
    participants: [
      '/path/to/avatar1.jpg',
      '/path/to/avatar2.jpg'
    ],
    isStarred: false
  },
  {
    id: 2,
    title: 'Daily Stand-Up 2',
    date: 'Dec. 4',
    time: '11:00am - 11:30am',
    timeAgo: '1 day ago',
    participants: [
      '/path/to/avatar1.jpg',
      '/path/to/avatar2.jpg'
    ],
    isStarred: false
  },
  {
    id: 1,
    title: 'Daily Stand-Up 1',
    date: 'Dec. 3',
    time: '11:00am - 11:15am',
    timeAgo: '2 days ago',
    participants: [
      '/path/to/avatar1.jpg',
      '/path/to/avatar2.jpg'
    ],
    isStarred: false
  }
]);

const selectedMeeting = ref({
  title: 'Meeting Summary for Daily Stand-Up 3',
  date: 'December 5, 2024',
  time: '10:00 AM - 11:30 AM',
  location: 'Online (via Zoom)',
  participants: [
    { name: 'John Smith', role: 'Project Manager' },
    { name: 'Sarah Lee', role: 'UI/UX Designer' },
    { name: 'Mike Tan', role: 'Back-end Developer' },
    { name: 'Emily Carter', role: 'AI Specialist' },
    { name: 'James Park', role: 'Front-end Developer' }
  ],
  summary: {
    done: [
      { person: 'John', task: 'Reviewed and approved updated Gantt chart timelines.' },
      { person: 'Sarah', task: 'Completed design iterations for the Kanban template.' },
      { person: 'Mike', task: 'Deployed email verification feature to staging for testing.' },
      { person: 'Emily', task: "Worked on AI's ability to auto-categorize meeting transcripts." },
      { person: 'James', task: 'Fixed UI bugs in the sign-up form.' }
    ],
    todo: [
      { person: 'John', task: 'Follow up on template descriptions feedback from the team.' },
      { person: 'Sarah', task: 'Start wireframes for the bug tracking feature.' },
      { person: 'Mike', task: 'Conduct integration testing for email verification with front-end.' },
      { person: 'Emily', task: "Refine LIRA's logic for prioritizing action items from meeting summaries." },
      { person: 'James', task: 'Style improvements for project dashboard UI.' }
    ],
    blockers: [
      { person: 'Sarah', task: 'Needs clarification on bug tracking template requirements. (John to provide details by noon.)' },
      { person: 'Mike', task: 'API rate limit encountered during testing. Will investigate further.' },
      { person: 'James', task: 'Requesting updated icons from the design team to finalize styling.' }
    ]
  }
});

const toggleStar = (meeting) => {
  meeting.isStarred = !meeting.isStarred;
};

const selectMeeting = (meeting) => {
  // In a real app, you would fetch the meeting details here
  // For now, we'll just use the hardcoded selectedMeeting
};
</script>

<template>
  <Header />
  <Sidebar />
  
  <div class="min-h-screen">
    <div class="ml-64 pt-16">
      <div class="p-6">
        <h1 class="text-2xl font-bold mb-8">Meeting Summaries</h1>
        
        <div class="flex gap-6">
          <!-- Left Panel - Meeting List -->
          <div class="w-[400px] bg-white rounded-lg p-4">
            <h2 class="text-xl mb-4">Meetings</h2>
            
            <!-- Search Bar -->
            <div class="relative mb-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search"
                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              <Search class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            </div>
            
            <!-- Sort Dropdown -->
            <div class="flex justify-end mb-4">
              <button class="flex items-center gap-2 text-sm text-gray-600">
                {{ sortOrder }}
                <ChevronDown class="w-4 h-4" />
              </button>
            </div>
            
            <!-- Meeting List -->
            <div class="space-y-3">
              <div
                v-for="meeting in meetings"
                :key="meeting.id"
                @click="selectMeeting(meeting)"
                class="p-4 border rounded-lg hover:bg-gray-50 cursor-pointer"
              >
                <div class="flex justify-between items-start">
                  <div class="flex gap-2 items-center">
                    <div class="flex -space-x-2">
                      <img
                        v-for="(participant, index) in meeting.participants"
                        :key="index"
                        :src="participant"
                        class="w-6 h-6 rounded-full border-2 border-white"
                      />
                    </div>
                    <h3>{{ meeting.title }}</h3>
                  </div>
                  <button @click.stop="toggleStar(meeting)">
                    <Star
                      class="w-4 h-4"
                      :class="meeting.isStarred ? 'fill-yellow-400 text-yellow-400' : 'text-gray-400'"
                    />
                  </button>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                  {{ meeting.time }}
                </div>
                <div class="flex justify-between text-sm text-gray-500 mt-1">
                  <span>{{ meeting.date }}</span>
                  <span>{{ meeting.timeAgo }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Panel - Meeting Details -->
          <div class="flex-1 bg-white rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">{{ selectedMeeting.title }}</h2>
            
            <!-- Meeting Info -->
            <div class="space-y-2 mb-6">
              <p><span class="font-medium">Date:</span> {{ selectedMeeting.date }}</p>
              <p><span class="font-medium">Time:</span> {{ selectedMeeting.time }}</p>
              <p><span class="font-medium">Location:</span> {{ selectedMeeting.location }}</p>
              
              <!-- Participants -->
              <div class="mt-4">
                <p class="font-medium mb-2">Participants:</p>
                <ul class="list-disc pl-5">
                  <li v-for="participant in selectedMeeting.participants" :key="participant.name">
                    {{ participant.name }} ({{ participant.role }})
                  </li>
                </ul>
              </div>
            </div>
            
            <!-- Summary -->
            <div class="space-y-6">
              <div>
                <h3 class="font-medium mb-2">1. What was done yesterday?</h3>
                <ul class="list-disc pl-5 space-y-1">
                  <li v-for="(item, index) in selectedMeeting.summary.done" :key="index">
                    <span class="font-medium">{{ item.person }}:</span> {{ item.task }}
                  </li>
                </ul>
              </div>
              
              <div>
                <h3 class="font-medium mb-2">2. What will be done today?</h3>
                <ul class="list-disc pl-5 space-y-1">
                  <li v-for="(item, index) in selectedMeeting.summary.todo" :key="index">
                    <span class="font-medium">{{ item.person }}:</span> {{ item.task }}
                  </li>
                </ul>
              </div>
              
              <div>
                <h3 class="font-medium mb-2">3. Any blockers or issues?</h3>
                <ul class="list-disc pl-5 space-y-1">
                  <li v-for="(item, index) in selectedMeeting.summary.blockers" :key="index">
                    <span class="font-medium">{{ item.person }}:</span> {{ item.task }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 