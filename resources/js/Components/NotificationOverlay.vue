<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { CheckCheck, ChevronDown, ChevronUp, Clock, MessageSquare, Clipboard, X } from 'lucide-vue-next';
import axios from 'axios';


const props = defineProps({
  modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const notifications = ref([]);
const isLoading = ref(true);
const expandedSections = ref({
  today: true,
  yesterday: false,
});

// Group notifications by date (today, yesterday, older)
const groupedNotifications = computed(() => {
  const groups = {
    today: [],
    yesterday: [],
    older: []
  };

  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  notifications.value.forEach(notification => {
    const notifDate = new Date(notification.created_at);
    notifDate.setHours(0, 0, 0, 0);

    if (notifDate.getTime() === today.getTime()) {
      groups.today.push(notification);
    } else if (notifDate.getTime() === yesterday.getTime()) {
      groups.yesterday.push(notification);
    } else {
      groups.older.push(notification);
    }
  });

  return groups;
});

const fetchNotifications = async () => {
  try {
    isLoading.value = true;
    const response = await axios.get(route('notifications.index'));
    notifications.value = response.data;
    console.log(response.data);
    isLoading.value = false;
  } catch (error) {
    console.error('Error fetching notifications:', error);
    isLoading.value = false;
  }
};

const markAllAsRead = async () => {
  try {
    await axios.post(route('notifications.mark-all-read'));
    notifications.value = notifications.value.map(notif => ({
      ...notif,
      is_read: true
    }));
  } catch (error) {
    console.error('Error marking notifications as read:', error);
  }
};

const markAsRead = async (id) => {
  try {
    await axios.post(route('notifications.mark-read', { id }));
    notifications.value = notifications.value.map(notif =>
      notif.id === id ? { ...notif, is_read: true } : notif
    );
  } catch (error) {
    console.error('Error marking notification as read:', error);
  }
};

const deleteNotification = async (id) => {
  try {
    await axios.delete(route('notifications.destroy', { id }));
    notifications.value = notifications.value.filter(notif => notif.id !== id);
  } catch (error) {
    console.error('Error deleting notification:', error);
  }
};

const toggleSection = (section) => {
  expandedSections.value[section] = !expandedSections.value[section];
};

const getTimeAgo = (timestamp) => {
  const now = new Date();
  const date = new Date(timestamp);
  const seconds = Math.floor((now - date) / 1000);

  if (seconds < 60) {
    return 'Just now';
  }

  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) {
    return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`;
  }

  const hours = Math.floor(minutes / 60);
  if (hours < 24) {
    return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`;
  }

  const days = Math.floor(hours / 24);
  if (days === 1) {
    return 'Yesterday';
  }

  return `${days} days ago`;
};

const getNotificationIcon = (type) => {
  switch (type) {
    case 'task_assigned':
      return Clipboard;
    case 'task_due':
      return Clock;
    case 'task_comment':
      return MessageSquare;
    default:
      return Clipboard;
  }
};

onMounted(() => {
  fetchNotifications();
//   setInterval(fetchNotifications, 1000); // Fetch notifications every minute)
});
</script>

<template>
  <div v-if="modelValue" class="fixed right-20 top-14 w-full md:w-96 bg-white border shadow-lg rounded-lg z-50 max-h-[80vh] overflow-hidden flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b">
      <h2 class="text-lg font-medium">Notifications</h2>
      <button
        @click="markAllAsRead"
        class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1"
      >
        <CheckCheck class="w-4 h-4" />
        Mark all as read
      </button>
    </div>

    <!-- Content -->
    <div class="overflow-y-auto flex-1">
      <div v-if="isLoading" class="p-6 text-center text-gray-500">
        Loading notifications...
      </div>

      <div v-else-if="notifications.length === 0" class="p-6 text-center text-gray-500">
        No notifications yet
      </div>

      <div v-else>
        <!-- Today's notifications -->
        <div v-if="groupedNotifications.today.length > 0" class="border-b">
          <div
            @click="toggleSection('today')"
            class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
          >
            <h3 class="font-medium">Today</h3>
            <component :is="expandedSections.today ? ChevronUp : ChevronDown" class="w-4 h-4" />
          </div>

          <div v-if="expandedSections.today" class="space-y-1">
            <div
              v-for="notification in groupedNotifications.today"
              :key="notification.id"
              class="flex px-4 py-3 hover:bg-gray-50 relative"
              :class="{ 'bg-blue-50': !notification.is_read }"
              @click="!notification.is_read && markAsRead(notification.id)"
            >
              <div class="mr-3 mt-1">
                <component :is="getNotificationIcon(notification.type)" class="w-6 h-6 text-gray-600" />
              </div>

              <div class="flex-1">
                <h4 class="font-medium">{{ notification.title }}</h4>
                <p class="text-sm text-gray-600" v-html="notification.content"></p>
                <p class="text-xs text-gray-500 mt-1">{{ getTimeAgo(notification.created_at) }}</p>
              </div>

              <button
                class="absolute right-4 top-3 text-gray-400 hover:text-gray-600"
                @click.stop="deleteNotification(notification.id)"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <!-- Yesterday's notifications -->
        <div v-if="groupedNotifications.yesterday.length > 0" class="border-b">
          <div
            @click="toggleSection('yesterday')"
            class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
          >
            <h3 class="font-medium">Yesterday</h3>
            <component :is="expandedSections.yesterday ? ChevronUp : ChevronDown" class="w-4 h-4" />
          </div>

          <div v-if="expandedSections.yesterday" class="space-y-1">
            <div
              v-for="notification in groupedNotifications.yesterday"
              :key="notification.id"
              class="flex px-4 py-3 hover:bg-gray-50 relative"
              :class="{ 'bg-blue-50': !notification.is_read }"
              @click="!notification.is_read && markAsRead(notification.id)"
            >
              <div class="mr-3 mt-1">
                <component :is="getNotificationIcon(notification.type)" class="w-6 h-6 text-gray-600" />
              </div>

              <div class="flex-1">
                <h4 class="font-medium">{{ notification.title }}</h4>
                <p class="text-sm text-gray-600" v-html="notification.content"></p>
                <p class="text-xs text-gray-500 mt-1">{{ getTimeAgo(notification.created_at) }}</p>
              </div>

              <button
                class="absolute right-4 top-3 text-gray-400 hover:text-gray-600"
                @click.stop="deleteNotification(notification.id)"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="p-4 border-t">
      <a href="#" class="text-violet-600 text-sm hover:underline block text-center">
        View all
      </a>
    </div>
  </div>
</template>

<style scoped>
.notification-section {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease-out;
}

.notification-section.expanded {
  max-height: 1000px;
  transition: max-height 0.5s ease-in;
}
</style>