<script setup>
import { ref, computed, onMounted, reactive, watch, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { 
    Users, Share2, Star, Video, ChevronDown, ChevronLeft, ChevronRight, 
    Plus, Calendar as CalendarIcon, Clock, MapPin, User, Search, Filter, 
    Check, X, Edit, Trash2, PlusIcon, ClockIcon, UsersIcon, AlertCircle, Eye 
} from 'lucide-vue-next'
import EventSidebar from '@/Components/EventSidebar.vue'
import Header from '@/Components/Header.vue'
import Button from '@/Components/Button.vue'

const page = ref({
    projectDetails: {
        name: 'Sample Project'
    }
})

const view = ref('calendar');
const tabs = [
    { label: 'Calendar', value: 'calendar' },
    { label: 'Event List', value: 'list' },
    { label: 'Timeline', value: 'timeline' },
    { label: 'Tasks', value: 'tasks' }
];

const events = ref([
    {
        id: "1",
        title: "Wedding Planning Kickoff",
        description: "Initial planning meeting for Smith-Johnson Wedding",
        date: "2025-03-15",
        startTime: "10:00",
        endTime: "11:30",
        location: "Main Office",
        assignedTo: ["Project Manager", "Event Planner"],
        category: "Planning",
        tasks: [
            {
                id: "1-1",
                title: "Create Event Timeline",
                status: "In Progress",
                date: "2025-03-16",
                startTime: "09:00",
                endTime: "12:00",
                assignedTo: ["Event Planner"]
            },
            {
                id: "1-2",
                title: "Budget Planning",
                status: "To Do",
                date: "2025-03-17",
                startTime: "14:00",
                endTime: "16:00",
                assignedTo: ["Project Manager", "Coordinator"]
            }
        ]
    },
    {
        id: "2",
        title: "Venue Tour with Clients",
        description: "Visit potential wedding venues with the couple",
        date: "2025-03-20",
        startTime: "13:00",
        endTime: "17:00",
        location: "Grand Hall",
        assignedTo: ["Event Planner", "Client"],
        category: "Client Meeting",
        tasks: [
            {
                id: "2-1",
                title: "Prepare Venue Comparison Sheet",
                status: "Done",
                date: "2025-03-19",
                startTime: "10:00",
                endTime: "12:00",
                assignedTo: ["Event Planner"]
            },
            {
                id: "2-2",
                title: "Collect Venue Requirements",
                status: "To Do",
                date: "2025-03-20",
                startTime: "13:00",
                endTime: "14:00",
                assignedTo: ["Project Manager"]
            }
        ]
    },
    {
        id: "3",
        title: "Decoration Setup",
        description: "Wedding venue decoration setup",
        date: "2025-04-01",
        startTime: "08:00",
        endTime: "16:00",
        location: "Grand Hall",
        assignedTo: ["Setup Crew", "Decorator"],
        category: "Setup",
        tasks: [
            {
                id: "3-1",
                title: "Floral Arrangements",
                status: "To Do",
                date: "2025-04-01",
                startTime: "08:00",
                endTime: "11:00",
                assignedTo: ["Decorator"]
            },
            {
                id: "3-2",
                title: "Lighting Setup",
                status: "To Do",
                date: "2025-04-01",
                startTime: "09:00",
                endTime: "12:00",
                assignedTo: ["Technical Staff"]
            },
            {
                id: "3-3",
                title: "Table Settings",
                status: "To Do",
                date: "2025-04-01",
                startTime: "13:00",
                endTime: "15:00",
                assignedTo: ["Setup Crew"]
            }
        ]
    },
    {
        id: "4",
        title: "Smith-Johnson Wedding",
        description: "Wedding ceremony and reception",
        date: "2025-04-02",
        startTime: "14:00",
        endTime: "23:00",
        location: "Grand Hall",
        assignedTo: ["Event Manager", "Staff"],
        category: "Event Day",
        tasks: [
            {
                id: "4-1",
                title: "Ceremony Coordination",
                status: "To Do",
                date: "2025-04-02",
                startTime: "14:00",
                endTime: "15:30",
                assignedTo: ["Event Manager"]
            },
            {
                id: "4-2",
                title: "Reception Management",
                status: "To Do",
                date: "2025-04-02",
                startTime: "16:00",
                endTime: "23:00",
                assignedTo: ["Staff", "Security"]
            },
            {
                id: "4-3",
                title: "Vendor Coordination",
                status: "To Do",
                date: "2025-04-02",
                startTime: "12:00",
                endTime: "23:00",
                assignedTo: ["Event Manager", "Technical Team"]
            }
        ]
    },
    {
        id: "5",
        title: "Post-Wedding Review",
        description: "Event evaluation and feedback session",
        date: "2025-04-05",
        startTime: "10:00",
        endTime: "12:00",
        location: "Main Office",
        assignedTo: ["Project Manager", "Event Planner", "Coordinator"],
        category: "Post-Event",
        tasks: [
            {
                id: "5-1",
                title: "Collect Feedback",
                status: "To Do",
                date: "2025-04-03",
                startTime: "14:00",
                endTime: "16:00",
                assignedTo: ["Event Planner"]
            },
            {
                id: "5-2",
                title: "Financial Review",
                status: "To Do",
                date: "2025-04-04",
                startTime: "09:00",
                endTime: "11:00",
                assignedTo: ["Project Manager", "Coordinator"]
            },
            {
                id: "5-3",
                title: "Prepare Final Report",
                status: "To Do",
                date: "2025-04-05",
                startTime: "09:00",
                endTime: "10:00",
                assignedTo: ["Project Manager"]
            }
        ]
    },
    {
        id: "6",
        title: "Team Debrief Meeting",
        description: "Review event execution and discuss improvements",
        date: "2025-04-06",
        startTime: "14:00",
        endTime: "16:00",
        location: "Conference Room",
        assignedTo: ["Project Manager", "Event Planner", "Coordinator", "Staff"],
        category: "Team Meeting",
        tasks: [
            {
                id: "6-1",
                title: "Prepare Presentation",
                status: "To Do",
                date: "2025-04-06",
                startTime: "10:00",
                endTime: "12:00",
                assignedTo: ["Project Manager"]
            },
            {
                id: "6-2",
                title: "Document Lessons Learned",
                status: "To Do",
                date: "2025-04-06",
                startTime: "16:00",
                endTime: "17:00",
                assignedTo: ["Coordinator"]
            }
        ]
    }
]);

const isAddEventOpen = ref(false);
const isEditMode = ref(false);
const selectedEventId = ref(null);
const newEvent = reactive({
    title: "",
    description: "",
    date: "",
    startTime: "",
    endTime: "",
    location: "",
    assignedTo: [],
    category: "Planning",
    tasks: [],
});

const teamMembers = ["Sarah", "Michael", "Jessica", "David", "Emma"];
const categories = [
    "Planning",
    "Client Meeting",
    "Setup",
    "Event Day",
    "Post-Event",
    "Team Meeting"
];
const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

const currentDate = ref(new Date());
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

const showDatePicker = ref(false);
const selectedYear = ref(currentDate.value.getFullYear());
const selectedMonth = ref(currentDate.value.getMonth());

const showMonthPicker = ref(false);
const showYearPicker = ref(false);

const years = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({length: 20}, (_, i) => currentYear - 5 + i);
});

const handleDateChange = () => {
    currentDate.value = new Date(selectedYear.value, selectedMonth.value);
    showDatePicker.value = false;
};

const sortedEvents = computed(() => {
    return [...events.value].sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
});

const timelineEvents = computed(() => {
    return [...events.value].sort((a, b) => {
        const dateA = new Date(a.date).getTime();
        const dateB = new Date(b.date).getTime();
        return dateA - dateB || a.startTime.localeCompare(b.startTime);
    });
});

const eventsByDate = computed(() => {
    return events.value.reduce((acc, event) => {
        if (!acc[event.date]) {
            acc[event.date] = [];
        }
        acc[event.date].push(event);
        return acc;
    }, {});
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDayOfMonth = new Date(year, month, 1).getDay();
    
    const days = [];
    
    for (let i = 0; i < firstDayOfMonth; i++) {
        days.push(null);
    }
    
    for (let i = 1; i <= daysInMonth; i++) {
        const date = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
            day: i,
            date,
            events: eventsByDate.value[date] || [],
            hasEvents: (eventsByDate.value[date]?.length || 0) > 0
        });
    }
    
    return days;
});

const previousMonth = () => {
    const newDate = new Date(currentDate.value);
    newDate.setMonth(newDate.getMonth() - 1);
    currentDate.value = newDate;
};

const nextMonth = () => {
    const newDate = new Date(currentDate.value);
    newDate.setMonth(newDate.getMonth() + 1);
    currentDate.value = newDate;
};

const hasAttemptedSubmit = ref(false);

const taskStatuses = ["To Do", "In Progress", "Done"];

const allTasks = computed(() => {
    const tasks = [];
    events.value.forEach(event => {
        if (event.tasks && event.tasks.length > 0) {
            event.tasks.forEach(task => {
                tasks.push({
                    ...task,
                    eventId: event.id,
                    eventTitle: event.title,
                    eventCategory: event.category,
                    eventDate: event.date
                });
            });
        }
    });
    return tasks;
});

const tasksByStatus = computed(() => {
    return allTasks.value.reduce((acc, task) => {
        if (!acc[task.status]) {
            acc[task.status] = [];
        }
        acc[task.status].push(task);
        return acc;
    }, {});
});

const groupedTasks = computed(() => {
    const groups = {};
    events.value.forEach(event => {
        if (event.tasks && event.tasks.length > 0) {
            if (!groups[event.category]) {
                groups[event.category] = [];
            }
            event.tasks.forEach(task => {
                groups[event.category].push({
                    ...task,
                    eventId: event.id,
                    eventTitle: event.title,
                    eventDate: event.date || task.date
                });
            });
        }
    });
    return groups;
});

const addTask = () => {
    newEvent.tasks.push({
        id: Date.now(),
        title: "",
        status: "To Do",
        assignedTo: [],
        date: "",
        startTime: "",
        endTime: "",
        isEditing: true
    });
};

const removeTask = (taskId) => {
    newEvent.tasks = newEvent.tasks.filter(task => task.id !== taskId);
};

const updateTaskStatus = (taskId, status) => {
    const task = newEvent.tasks.find(t => t.id === taskId);
    if (task) {
        task.status = status;
    }
};

const validateForm = computed(() => {
    const taskValidation = newEvent.tasks.length > 0 ? newEvent.tasks.every(task => {
        return task.title?.trim() && (!task.date || (task.date && task.startTime && task.endTime));
    }) : true;

    return {
        title: !newEvent.title?.trim(),
        date: !newEvent.date,
        startTime: !newEvent.startTime,
        endTime: !newEvent.endTime,
        timeValid: newEvent.startTime && newEvent.endTime && 
            newEvent.startTime < newEvent.endTime,
        dateValid: newEvent.date && new Date(newEvent.date) >= new Date().setHours(0,0,0,0),
        tasks: !taskValidation,
        messages: {
            title: 'Title is required',
            date: 'Date is required',
            startTime: 'Start time is required',
            endTime: 'End time is required',
            timeValid: 'End time must be after start time',
            dateValid: 'Date cannot be in the past',
            tasks: 'All tasks must have a title, and if date is set, both start and end time are required'
        }
    }
});

const handleAddEvent = () => {
    hasAttemptedSubmit.value = true;

    // Validate main event
    if (!newEvent.title?.trim() || !newEvent.date || !newEvent.startTime || !newEvent.endTime) {
        return;
    }

    if (!validateForm.value.timeValid || !validateForm.value.dateValid) {
        return;
    }

    // Validate tasks if any exist
    if (newEvent.tasks.length > 0) {
        const invalidTasks = newEvent.tasks.filter(task => {
            return !task.title?.trim() || (task.date && (!task.startTime || !task.endTime));
        });

        if (invalidTasks.length > 0) {
            return;
        }
    }

    // Process tasks to ensure they have proper defaults
    const processedTasks = newEvent.tasks.map(task => ({
        ...task,
        id: task.id || Date.now().toString(),
        date: task.date || newEvent.date,
        startTime: task.startTime || newEvent.startTime,
        endTime: task.endTime || newEvent.endTime,
        status: task.status || 'To Do',
        assignedTo: task.assignedTo || []
    }));

    if (isEditMode.value && selectedEventId.value) {
        // Update existing event
        const index = events.value.findIndex(e => e.id === selectedEventId.value);
        if (index !== -1) {
            events.value[index] = {
                ...events.value[index],
                ...newEvent,
                tasks: processedTasks
            };
        }
    } else {
        // Create new event
        const event = {
            id: Date.now().toString(),
            ...newEvent,
            tasks: processedTasks
        };
        events.value.push(event);
    }
    
    closeModal();
};

const closeModal = () => {
    isAddEventOpen.value = false;
    isEditMode.value = false;
    selectedEventId.value = null;
    hasAttemptedSubmit.value = false;
    Object.assign(newEvent, {
        title: "",
        description: "",
        date: "",
        startTime: "",
        endTime: "",
        location: "",
        assignedTo: [],
        category: "Planning",
        tasks: [],
    });
};

function handleDeleteEvent(id) {
    events.value = events.value.filter(event => event.id !== id);
}

function handleAssigneeChange(member) {
    if (newEvent.assignedTo.includes(member)) {
        newEvent.assignedTo = newEvent.assignedTo.filter(name => name !== member);
    } else {
        newEvent.assignedTo.push(member);
    }
}

function getCategoryClass(category) {
    switch (category) {
        case "Planning":
            return "bg-button text-light hover:bg-button-hover";
        case "Client Meeting":
            return "bg-blue text-light hover:bg-blue/90";
        case "Setup":
            return "bg-cyan text-dark hover:bg-cyan/90";
        case "Event Day":
            return "bg-light-blue text-dark hover:bg-light-blue/90";
        case "Post-Event":
            return "bg-dark text-light hover:bg-dark/90";
        case "Team Meeting":
            return "bg-button-hover text-light hover:bg-button-hover/90";
        default:
            return "bg-neutral text-dark hover:bg-neutral/90";
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

const handleEventEdit = (event) => {
    isEditMode.value = true;
    selectedEventId.value = event.id;
    Object.assign(newEvent, {
        ...event
    });
    isAddEventOpen.value = true;
};

const handleDateClick = (date) => {
    if (date) {
        Object.assign(newEvent, {
            date: date,
            title: "",
            description: "",
            startTime: "",
            endTime: "",
            location: "",
            assignedTo: [],
            category: "Planning",
        });
        isAddEventOpen.value = true;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const monthPickerRef = ref(null);
const yearPickerRef = ref(null);

const handleClickOutside = (event) => {
    if (monthPickerRef.value && !monthPickerRef.value.contains(event.target)) {
        showMonthPicker.value = false;
    }
    if (yearPickerRef.value && !yearPickerRef.value.contains(event.target)) {
        showYearPicker.value = false;
    }
};

// Add this validation function
const validateYear = (value) => {
    const year = parseInt(value);
    const currentYear = new Date().getFullYear();
    if (year < currentYear - 5 || year > currentYear + 15) {
        selectedYear.value = currentYear;
        handleDateChange();
    }
};

// Add watch for year changes
watch(selectedYear, (newValue) => {
    validateYear(newValue);
});

// Update the isFormValid computed property
const isFormValid = computed(() => {
    return newEvent.title?.trim() && newEvent.date && newEvent.startTime && newEvent.endTime;
});

// Add these new refs
const isViewEventOpen = ref(false);
const viewingEvent = ref(null);

// Add this new function
const handleViewEvent = (event) => {
    viewingEvent.value = event;
    isViewEventOpen.value = true;
};

// Add these new constants for category-specific participants
const categoryParticipants = {
    "Planning": {
        label: "Team Members",
        members: ["Project Manager", "Event Planner", "Coordinator"]
    },
    "Client Meeting": {
        label: "Participants",
        members: ["Client", "Project Manager", "Event Planner"]
    },
    "Setup": {
        label: "Staff",
        members: ["Setup Crew", "Decorator", "Technical Staff"]
    },
    "Event Day": {
        label: "Event Staff",
        members: ["Event Manager", "Staff", "Security", "Technical Team"]
    },
    "Post-Event": {
        label: "Team Members",
        members: ["Project Manager", "Event Planner", "Coordinator"]
    },
    "Team Meeting": {
        label: "Team Members",
        members: ["Project Manager", "Event Planner", "Coordinator", "Staff"]
    }
};

// Add new reactive refs for search and filters
const searchQuery = ref('');
const selectedCategoryFilter = ref('All');

// Update the filteredEvents computed property
const filteredEvents = computed(() => {
    let filtered = [...events.value];
    
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim();
        filtered = filtered.filter(event => 
            event.title.toLowerCase().includes(query) ||
            event.description?.toLowerCase().includes(query) ||
            event.location?.toLowerCase().includes(query) ||
            event.category.toLowerCase().includes(query) ||
            event.tasks?.some(task => 
                task.title.toLowerCase().includes(query) ||
                task.assignedTo?.some(member => member.toLowerCase().includes(query))
            )
        );
    }
    
    if (selectedCategoryFilter.value !== 'All') {
        filtered = filtered.filter(event => event.category === selectedCategoryFilter.value);
    }
    
    return filtered;
});

// Update the filteredGroupedTasks computed property
const filteredGroupedTasks = computed(() => {
    const filtered = {};
    
    Object.entries(groupedTasks.value).forEach(([category, tasks]) => {
        let filteredTasks = [...tasks];
        
        if (searchQuery.value.trim()) {
            const query = searchQuery.value.toLowerCase().trim();
            filteredTasks = filteredTasks.filter(task => 
                task.title.toLowerCase().includes(query) ||
                task.eventTitle?.toLowerCase().includes(query) ||
                task.assignedTo?.some(member => member.toLowerCase().includes(query))
            );
        }
        
        if (selectedCategoryFilter.value !== 'All') {
            if (category === selectedCategoryFilter.value) {
                filtered[category] = filteredTasks;
            }
        } else if (filteredTasks.length > 0) {
            filtered[category] = filteredTasks;
        }
    });
    
    return filtered;
});

// Add new ref for viewing task
const viewingTask = ref(null);
const isViewTaskOpen = ref(false);

// Add new function to handle task view
const handleViewTask = (task) => {
    viewingTask.value = task;
    isViewTaskOpen.value = true;
};

// Add function to handle task status update
const handleTaskStatusUpdate = (task, newStatus) => {
    const event = events.value.find(e => e.id === task.eventId);
    if (event) {
        const taskToUpdate = event.tasks.find(t => t.id === task.id);
        if (taskToUpdate) {
            taskToUpdate.status = newStatus;
        }
    }
};

// Add function to handle task deletion
const handleDeleteTask = (taskId, eventId) => {
    const event = events.value.find(e => e.id === eventId);
    if (event) {
        event.tasks = event.tasks.filter(task => task.id !== taskId);
    }
};
</script>

<template>
    <Header />
    <EventSidebar />
    <Head title=" | Calendar" />
    <div class="min-h-screen overflow-y-auto">
        <div class="ml-64 pt-16">
            <div class="p-6 flex itenter justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold">
                        {{ page.projectDetails.name }}
                        <span class="text-xl font-normal"> > Dashboard</span>
                    </h1>
                    <div class="flex items-center -space-x-2"></div>
                    <button class="p-2 text-gray-600 hover:text-gray-800">
                        <Users class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <button>
                        <Share2 />
                    </button>
                    <button>
                        <Star />
                    </button>
                    <Button text="Create Meeting" variant="primary" :style="`flex px-4 py-2 items-center gap-2`">
                        <Video />
                    </Button>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div class="bg-light inline-flex rounded-md shadow-sm" role="group">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.value"
                            @click="view = tab.value" 
                            :class="[
                                'px-4 py-2 text-sm font-medium',
                                view === tab.value 
                                    ? 'bg-button text-light' 
                                    : 'text-dark hover:bg-neutral'
                            ]"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                    <Button 
                        @click="isAddEventOpen = true"
                        text="Add Event"
                        variant="primary"
                        :style="`flex items-center gap-2`"
                    >
                        <Plus class="h-4 w-4" />
                    </Button>
                </div>

                <div v-if="view === 'calendar'" class="border border-light-gray rounded-lg bg-white">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <button @click="previousMonth" class="btn-cancel !p-2">
                                <ChevronLeft class="w-5 h-5" />
                            </button>
                            
                            <div class="flex items-center gap-6">
                                <!-- Month Dropdown -->
                                <div class="relative" ref="monthPickerRef">
                                    <button 
                                        @click="showMonthPicker = !showMonthPicker"
                                        class="flex items-center gap-2 px-3 py-2 border border-light-gray rounded-md hover:bg-neutral w-[160px]"
                                    >
                                        <span class="text-lg font-medium truncate flex-1 text-left">{{ monthNames[currentDate.getMonth()] }}</span>
                                        <ChevronDown class="w-4 h-4 flex-shrink-0" />
                                    </button>
                                    
                                    <div v-if="showMonthPicker" 
                                        class="absolute top-full left-0 mt-1 bg-white border border-light-gray rounded-md shadow-lg z-20"
                                    >
                                        <div class="grid grid-cols-3 gap-1 p-2 w-[240px]">
                                            <button
                                                v-for="(month, index) in monthNames"
                                                :key="index"
                                                @click="() => {
                                                    selectedMonth = index;
                                                    showMonthPicker = false;
                                                    handleDateChange();
                                                }"
                                                :class="[
                                                    'px-2 py-1 text-sm rounded hover:bg-neutral',
                                                    selectedMonth === index ? 'bg-button text-light' : 'text-dark'
                                                ]"
                                            >
                                                {{ month.slice(0, 3) }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Year Input -->
                                <div class="relative" ref="yearPickerRef">
                                    <button 
                                        @click="showYearPicker = !showYearPicker"
                                        class="flex items-center gap-2 px-3 py-2 border border-light-gray rounded-md hover:bg-neutral w-[120px]"
                                    >
                                        <span class="text-lg font-medium flex-1 text-left">{{ selectedYear }}</span>
                                        <ChevronDown class="w-4 h-4 flex-shrink-0" />
                                    </button>
                                    
                                    <div v-if="showYearPicker" 
                                        class="absolute top-full right-0 mt-1 bg-white border border-light-gray rounded-md shadow-lg z-50"
                                    >
                                        <div class="max-h-[200px] overflow-y-auto w-[120px]">
                                            <button
                                                v-for="year in years"
                                                :key="year"
                                                @click="() => {
                                                    selectedYear = year;
                                                    showYearPicker = false;
                                                    handleDateChange();
                                                }"
                                                :class="[
                                                    'w-full px-3 py-2 text-sm text-left hover:bg-neutral',
                                                    selectedYear === year ? 'bg-button text-light' : 'text-dark'
                                                ]"
                                            >
                                                {{ year }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button @click="nextMonth" class="btn-cancel !p-2">
                                <ChevronRight class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            <div v-for="day in weekdays" :key="day" 
                                class="text-center font-medium py-2 text-dark">
                                {{ day }}
                            </div>
                            <div
                                v-for="(day, index) in calendarDays"
                                :key="index"
                                :class="[
                                    'min-h-[120px] border border-light-gray rounded-md p-1',
                                    day ? 'bg-white hover:bg-light cursor-pointer' : 'bg-neutral',
                                    day?.hasEvents ? 'ring-1 ring-button ring-opacity-20' : ''
                                ]"
                                @click="day && handleDateClick(day.date)"
                            >
                                <template v-if="day">
                                    <div :class="[
                                        'text-sm font-medium p-1 rounded-full w-7 h-7 flex items-center justify-center',
                                        day.hasEvents ? 'bg-button/10 text-button' : 'text-dark'
                                    ]">
                                        {{ day.day }}
                                    </div>
                                    <div class="h-[90px] overflow-auto">
                                        <div
                                            v-for="event in day.events"
                                            :key="event.id"
                                            class="mb-1 px-2 py-1 text-xs truncate cursor-pointer group flex items-center justify-between rounded"
                                            :class="getCategoryClass(event.category)"
                                            @click.stop="handleEventEdit(event)"
                                        >
                                            <span>{{ event.startTime }} - {{ event.title }}</span>
                                            <Edit 
                                                class="w-3 h-3 text-current opacity-0 group-hover:opacity-100 transition-opacity" 
                                            />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="view === 'list'" class="border border-light-gray rounded-lg bg-white">
                    <div class="p-6">
                        <!-- Search and Filter Bar -->
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex-1 relative">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg"
                                    placeholder="Search events..."
                                />
                            </div>
                            <div class="flex items-center gap-2">
                                <Filter class="w-4 h-4 text-gray-400" />
                                <select 
                                    v-model="selectedCategoryFilter"
                                    class="border rounded-lg px-3 py-2"
                                >
                                    <option value="All">All Categories</option>
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div v-if="filteredEvents.length === 0" class="text-center py-8 text-dark-gray">
                                No events found. Try adjusting your search or filters.
                            </div>
                            <div 
                                v-for="event in filteredEvents" 
                                :key="event.id" 
                                class="border border-light-gray rounded-lg bg-white hover:bg-light transition-colors cursor-pointer"
                                @click="handleViewEvent(event)"
                            >
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold text-dark">{{ event.title }}</h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span :class="`inline-block px-2 py-1 text-xs rounded-full ${getCategoryClass(event.category)}`">
                                                    {{ event.category }}
                                                </span>
                                                <span class="text-sm text-dark-gray">
                                                    {{ formatDate(event.date) }} • {{ event.startTime }} - {{ event.endTime }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button 
                                                @click.stop="handleEventEdit(event)"
                                                class="btn-cancel !p-2 !border-0"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </button>
                                            <button 
                                                @click.stop="handleDeleteEvent(event.id)"
                                                class="btn-cancel !p-2 !border-0"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Event Details -->
                                    <div class="mt-4 grid grid-cols-1 gap-4">
                                        <div class="space-y-2">
                                            <p v-if="event.description" class="text-sm text-dark-gray">{{ event.description }}</p>
                                            <div v-if="event.location" class="flex items-center text-sm text-dark-gray">
                                                <MapPin class="mr-2 h-4 w-4" />
                                                {{ event.location }}
                                            </div>
                                            <div class="flex items-center text-sm text-dark-gray">
                                                <Users class="mr-2 h-4 w-4" />
                                                {{ event.assignedTo.join(", ") }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="view === 'timeline'" class="border border-light-gray rounded-lg bg-white">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-if="timelineEvents.length === 0" class="text-center py-8 text-dark-gray">
                                No events scheduled. Click "Add Event" to create one.
                            </div>
                            <div v-for="event in timelineEvents" :key="event.id" class="relative pl-8">
                                <!-- Timeline dot and line -->
                                <div class="absolute left-0 top-2 w-4 h-4 rounded-full bg-button"></div>
                                <div class="absolute left-2 top-6 bottom-0 w-[1px] bg-light-gray"></div>
                                
                                <!-- Event content -->
                                <div class="hover:bg-light transition-colors rounded-lg">
                                    <div 
                                        class="p-4 cursor-pointer"
                                        @click="handleViewEvent(event)"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-lg font-semibold text-dark">{{ event.title }}</h3>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span :class="`inline-block px-2 py-1 text-xs rounded-full ${getCategoryClass(event.category)}`">
                                                        {{ event.category }}
                                                    </span>
                                                    <span class="text-sm text-dark-gray">
                                                        {{ formatDate(event.date) }} • {{ event.startTime }} - {{ event.endTime }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-dark-gray mt-2">{{ event.description }}</p>
                                        <div class="mt-3 flex items-center gap-4 text-sm text-dark-gray">
                                            <div v-if="event.location" class="flex items-center">
                                                <MapPin class="w-4 h-4 mr-1" />
                                                {{ event.location }}
                                            </div>
                                            <div class="flex items-center">
                                                <Users class="w-4 h-4 mr-1" />
                                                {{ event.assignedTo.join(", ") }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tasks Section -->
                                    <template v-if="event.tasks && event.tasks.length > 0">
                                        <div class="mt-2 pl-4 space-y-2 pb-4">
                                            <div v-for="task in event.tasks" :key="task.id" 
                                                class="flex items-center justify-between p-2 bg-white rounded-lg border border-light-gray cursor-pointer hover:bg-gray-50"
                                                @click.stop="handleViewTask({...task, eventId: event.id, eventTitle: event.title})"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span 
                                                        class="w-2 h-2 rounded-full"
                                                        :class="{
                                                            'bg-gray-400': task.status === 'To Do',
                                                            'bg-orange-400': task.status === 'In Progress',
                                                            'bg-green-400': task.status === 'Done'
                                                        }"
                                                    ></span>
                                                    <span class="text-sm">{{ task.title }}</span>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <span class="text-xs text-dark-gray">
                                                        {{ task.startTime ? `${task.startTime} - ${task.endTime}` : '' }}
                                                    </span>
                                                    <div class="flex -space-x-2">
                                                        <div v-for="member in task.assignedTo" :key="member"
                                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium border-2 border-white"
                                                        >
                                                            {{ member.charAt(0) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="view === 'tasks'" class="border border-light-gray rounded-lg bg-white">
                    <div class="p-6">
                        <!-- Search and Filter Bar -->
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex-1 relative">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg"
                                    placeholder="Search tasks..."
                                />
                            </div>
                            <div class="flex items-center gap-2">
                                <Filter class="w-4 h-4 text-gray-400" />
                                <select 
                                    v-model="selectedCategoryFilter"
                                    class="border rounded-lg px-3 py-2"
                                >
                                    <option value="All">All Categories</option>
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div v-if="Object.keys(filteredGroupedTasks).length === 0" class="text-center py-8 text-dark-gray">
                                No tasks found. Try adjusting your search or filters.
                            </div>
                            <div v-for="(tasks, category) in filteredGroupedTasks" :key="category">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold">{{ category }}</h3>
                                    <span :class="`px-2 py-1 text-xs rounded-full ${getCategoryClass(category)}`">
                                        {{ tasks.length }} tasks
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    <div v-for="task in tasks" :key="task.id" 
                                        class="bg-white p-4 rounded-lg border border-gray-200 hover:bg-light transition-colors cursor-pointer"
                                        @click="handleViewTask(task)"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-dark">{{ task.title }}</h4>
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    <div v-for="member in task.assignedTo" :key="member"
                                                        class="flex items-center gap-1 px-2 py-1 bg-light rounded-full text-xs text-gray-600"
                                                    >
                                                        <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-medium">
                                                            {{ member.charAt(0) }}
                                                        </div>
                                                        {{ member }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <select 
                                                    v-model="task.status"
                                                    class="px-2 py-1 text-xs border rounded-lg cursor-pointer"
                                                    :class="{
                                                        'text-gray-600 bg-gray-50': task.status === 'To Do',
                                                        'text-orange-600 bg-orange-50': task.status === 'In Progress',
                                                        'text-green-600 bg-green-50': task.status === 'Done'
                                                    }"
                                                    @change="handleTaskStatusUpdate(task, $event.target.value)"
                                                >
                                                    <option v-for="status in taskStatuses" :key="status" :value="status">
                                                        {{ status }}
                                                    </option>
                                                </select>
                                                <button 
                                                    @click="handleDeleteTask(task.id, task.eventId)"
                                                    class="p-1 text-gray-400 hover:text-red-500 transition-colors"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="isAddEventOpen" 
                    class="fixed inset-0 z-50 overflow-hidden">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
                    
                    <!-- Modal Container -->
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-[90vw] max-h-[90vh] flex flex-col">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 py-4 border-b">
                                <div class="flex-1">
                                    <input 
                                        v-model="newEvent.title"
                                        type="text"
                                        class="text-xl font-semibold w-full bg-transparent border-0 focus:ring-0 focus:outline-none"
                                        :class="{ 'border-red-500': hasAttemptedSubmit && validateForm.title }"
                                        placeholder="Event title"
                                    />
                                    <div v-if="hasAttemptedSubmit && validateForm.title" class="mt-1 text-sm text-error flex items-center gap-1">
                                        <AlertCircle class="w-4 h-4" />
                                        {{ validateForm.messages.title }}
                                    </div>
                                </div>
                                <button @click="closeModal" class="p-1 hover:bg-gray-100 rounded">
                                    <X class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="flex flex-1 min-h-0">
                                <!-- Left Column -->
                                <div class="w-2/3 border-r p-6 overflow-y-auto">
                                    <div class="space-y-6">
                                        <!-- Date, Time, Category Row -->
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                                <input
                                                    type="date"
                                                    v-model="newEvent.date"
                                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    :class="{ 'border-error': hasAttemptedSubmit && validateForm.date }"
                                                />
                                                <span v-if="hasAttemptedSubmit && validateForm.date" class="text-xs text-error block mt-1">
                                                    {{ validateForm.messages.date }}
                                                </span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                                <div class="flex gap-2">
                                                    <input
                                                        type="time"
                                                        v-model="newEvent.startTime"
                                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                                        :class="{ 'border-error': hasAttemptedSubmit && validateForm.startTime }"
                                                    />
                                                    <input
                                                        type="time"
                                                        v-model="newEvent.endTime"
                                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                                        :class="{ 'border-error': hasAttemptedSubmit && validateForm.endTime }"
                                                    />
                                                </div>
                                                <span v-if="hasAttemptedSubmit && (validateForm.startTime || validateForm.endTime)" class="text-xs text-error block mt-1">
                                                    {{ validateForm.startTime ? validateForm.messages.startTime : validateForm.messages.endTime }}
                                                </span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                                <select 
                                                    v-model="newEvent.category"
                                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                                >
                                                    <option v-for="category in categories" :key="category" :value="category">
                                                        {{ category }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea 
                                                v-model="newEvent.description"
                                                rows="4"
                                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Add a description..."
                                            ></textarea>
                                        </div>

                                        <!-- Location -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                            <div class="flex items-center gap-2">
                                                <MapPin class="w-4 h-4 text-gray-400" />
                                                <input
                                                    v-model="newEvent.location"
                                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    placeholder="Add location"
                                                />
                                            </div>
                                        </div>

                                        <!-- Category-specific participants -->
                                        <div v-if="categoryParticipants[newEvent.category]">
                                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                                {{ categoryParticipants[newEvent.category].label }}
                                            </label>
                                            <div class="flex flex-wrap gap-2">
                                                <button
                                                    v-for="member in categoryParticipants[newEvent.category].members"
                                                    :key="member"
                                                    @click="handleAssigneeChange(member)"
                                                    class="flex items-center gap-2 px-3 py-2 rounded-lg border transition-colors"
                                                    :class="[
                                                        newEvent.assignedTo.includes(member)
                                                            ? 'bg-button text-light border-button'
                                                            : 'border-light-gray text-dark hover:bg-light'
                                                    ]"
                                                >
                                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium">
                                                        {{ member.charAt(0) }}
                                                    </div>
                                                    {{ member }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Event Info -->
                                        <div class="flex items-center gap-3 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                                            <CalendarIcon class="w-4 h-4" />
                                            <span>{{ newEvent.date || 'No date selected' }}</span>
                                            <span class="mx-1">•</span>
                                            <Clock class="w-4 h-4" />
                                            <span>{{ newEvent.startTime || '--:--' }} - {{ newEvent.endTime || '--:--' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="w-1/3 p-6 overflow-y-auto bg-gray-50">
                                    <!-- Tasks -->
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-700 mb-3">Tasks</h3>
                                        <div class="space-y-3">
                                            <div v-for="task in newEvent.tasks" :key="task.id" 
                                                class="bg-white p-3 rounded-lg border border-gray-200">
                                                <div class="flex items-start gap-2">
                                                    <div class="flex-1">
                                                        <input
                                                            v-model="task.title"
                                                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 mb-2"
                                                            :class="{ 'border-error': hasAttemptedSubmit && !task.title?.trim() }"
                                                            placeholder="Task title"
                                                        />
                                                        <div class="grid grid-cols-3 gap-2 mb-2">
                                                            <input
                                                                type="date"
                                                                v-model="task.date"
                                                                class="w-full px-3 py-2 border rounded-lg text-sm"
                                                                placeholder="Date"
                                                            />
                                                            <input
                                                                type="time"
                                                                v-model="task.startTime"
                                                                class="w-full px-3 py-2 border rounded-lg text-sm"
                                                                placeholder="Start"
                                                            />
                                                            <input
                                                                type="time"
                                                                v-model="task.endTime"
                                                                class="w-full px-3 py-2 border rounded-lg text-sm"
                                                                placeholder="End"
                                                            />
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <select 
                                                                v-model="task.status"
                                                                class="px-2 py-1 text-sm border rounded-lg"
                                                                :class="{
                                                                    'text-gray-600 bg-gray-50': task.status === 'To Do',
                                                                    'text-orange-600 bg-orange-50': task.status === 'In Progress',
                                                                    'text-green-600 bg-green-50': task.status === 'Done'
                                                                }"
                                                            >
                                                                <option v-for="status in taskStatuses" :key="status" :value="status">
                                                                    {{ status }}
                                                                </option>
                                                            </select>
                                                            <div class="flex-1">
                                                                <div class="flex flex-wrap gap-2">
                                                                    <button
                                                                        v-for="member in categoryParticipants[newEvent.category]?.members || []"
                                                                        :key="member"
                                                                        @click="() => {
                                                                            if (!task.assignedTo) task.assignedTo = [];
                                                                            if (task.assignedTo.includes(member)) {
                                                                                task.assignedTo = task.assignedTo.filter(name => name !== member);
                                                                            } else {
                                                                                task.assignedTo.push(member);
                                                                            }
                                                                        }"
                                                                        class="flex items-center gap-1 px-2 py-1 rounded-lg border text-xs transition-colors"
                                                                        :class="[
                                                                            task.assignedTo?.includes(member)
                                                                                ? 'bg-button text-light border-button'
                                                                                : 'border-light-gray text-dark hover:bg-light'
                                                                        ]"
                                                                    >
                                                                        <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-medium">
                                                                            {{ member.charAt(0) }}
                                                                        </div>
                                                                        {{ member }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <button 
                                                                @click="() => removeTask(task.id)"
                                                                class="p-1 text-gray-400 hover:text-gray-600"
                                                            >
                                                                <X class="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button 
                                                @click="addTask"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 flex items-center justify-center gap-2"
                                            >
                                                <Plus class="w-4 h-4" />
                                                Add Task
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-6 py-4 border-t bg-gray-50 flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    Press Esc to close
                                </div>
                                <div class="flex gap-3">
                                    <button @click="closeModal" class="btn-cancel">Cancel</button>
                                    <button 
                                        @click="handleAddEvent"
                                        class="btn-primary"
                                    >
                                        {{ isEditMode ? 'Save Changes' : 'Save Event' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add the view event modal -->
    <div v-if="isViewEventOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-dark bg-opacity-50" @click="isViewEventOpen = false"></div>
        <div class="relative w-full max-w-2xl rounded-lg border border-light-gray bg-white p-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-semibold text-dark">
                            {{ viewingEvent?.title }}
                        </h2>
                        <span :class="`inline-block px-2 py-1 text-xs rounded-full mt-2 ${getCategoryClass(viewingEvent?.category)}`">
                            {{ viewingEvent?.category }}
                        </span>
                    </div>
                    <button 
                        @click="isViewEventOpen = false"
                        class="text-dark-gray hover:text-dark"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Event Details -->
            <div class="space-y-4">
                <!-- Date and Time -->
                <div class="flex items-center text-sm text-dark-gray">
                    <CalendarIcon class="mr-2 h-4 w-4" />
                    {{ formatDate(viewingEvent?.date) }}
                </div>
                <div class="flex items-center text-sm text-dark-gray">
                    <Clock class="mr-2 h-4 w-4" />
                    {{ viewingEvent?.startTime }} - {{ viewingEvent?.endTime }}
                </div>

                <!-- Description -->
                <div v-if="viewingEvent?.description" class="text-sm">
                    <h3 class="font-medium text-dark mb-1">Description</h3>
                    <p class="text-dark-gray">{{ viewingEvent?.description }}</p>
                </div>

                <!-- Location -->
                <div v-if="viewingEvent?.location" class="flex items-center text-sm">
                    <MapPin class="mr-2 h-4 w-4 text-dark-gray" />
                    <span class="text-dark-gray">{{ viewingEvent?.location }}</span>
                </div>

                <!-- Assigned Team Members -->
                <div v-if="viewingEvent?.assignedTo?.length" class="text-sm">
                    <h3 class="font-medium text-dark mb-2">Assigned Team Members</h3>
                    <div class="flex flex-wrap gap-2">
                        <div 
                            v-for="member in viewingEvent.assignedTo" 
                            :key="member"
                            class="flex items-center gap-1 px-2 py-1 bg-light rounded-full text-xs"
                        >
                            <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-medium">
                                {{ member.charAt(0) }}
                            </div>
                            <span>{{ member }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div v-if="viewingEvent?.tasks?.length" class="text-sm mt-6">
                    <h3 class="font-medium text-dark mb-3">Tasks</h3>
                    <div class="space-y-3">
                        <div v-for="task in viewingEvent.tasks" :key="task.id" 
                            class="p-3 bg-light rounded-lg border border-light-gray">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span 
                                        class="w-2 h-2 rounded-full"
                                        :class="{
                                            'bg-gray-400': task.status === 'To Do',
                                            'bg-orange-400': task.status === 'In Progress',
                                            'bg-green-400': task.status === 'Done'
                                        }"
                                    ></span>
                                    <span class="font-medium">{{ task.title }}</span>
                                    <span 
                                        class="px-2 py-0.5 text-xs rounded-lg"
                                        :class="{
                                            'text-gray-600 bg-gray-50': task.status === 'To Do',
                                            'text-orange-600 bg-orange-50': task.status === 'In Progress',
                                            'text-green-600 bg-green-50': task.status === 'Done'
                                        }"
                                    >
                                        {{ task.status }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div v-if="task.date || task.startTime" class="text-xs text-dark-gray">
                                        {{ task.date ? formatDate(task.date) : '' }}
                                        {{ task.startTime ? `• ${task.startTime} - ${task.endTime}` : '' }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Task Assignees -->
                            <div v-if="task.assignedTo?.length" class="mt-2 flex flex-wrap gap-2">
                                <div 
                                    v-for="member in task.assignedTo" 
                                    :key="member"
                                    class="flex items-center gap-1 px-2 py-1 bg-white rounded-full text-xs border border-light-gray"
                                >
                                    <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-medium">
                                        {{ member.charAt(0) }}
                                    </div>
                                    <span>{{ member }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex justify-end">
                <button 
                    @click="isViewEventOpen = false"
                    class="btn-cancel !px-4 !py-2 text-sm"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Add Task View Modal -->
    <div v-if="isViewTaskOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-dark bg-opacity-50" @click="isViewTaskOpen = false"></div>
        <div class="relative w-full max-w-lg rounded-lg border border-light-gray bg-white p-4">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-dark">{{ viewingTask?.title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">From: {{ viewingTask?.eventTitle }}</p>
                </div>
                <button 
                    @click="isViewTaskOpen = false"
                    class="text-dark-gray hover:text-dark"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Task Details -->
            <div class="space-y-4">
                <!-- Status -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium">Status:</span>
                    <span 
                        class="px-2 py-1 text-xs rounded-lg"
                        :class="{
                            'text-gray-600 bg-gray-50': viewingTask?.status === 'To Do',
                            'text-orange-600 bg-orange-50': viewingTask?.status === 'In Progress',
                            'text-green-600 bg-green-50': viewingTask?.status === 'Done'
                        }"
                    >
                        {{ viewingTask?.status }}
                    </span>
                </div>

                <!-- Date and Time -->
                <div v-if="viewingTask?.date || viewingTask?.startTime" class="text-sm space-y-2">
                    <div v-if="viewingTask?.date" class="flex items-center text-dark-gray">
                        <CalendarIcon class="w-4 h-4 mr-2" />
                        {{ formatDate(viewingTask.date) }}
                    </div>
                    <div v-if="viewingTask?.startTime" class="flex items-center text-dark-gray">
                        <Clock class="w-4 h-4 mr-2" />
                        {{ viewingTask.startTime }} - {{ viewingTask.endTime }}
                    </div>
                </div>

                <!-- Assignees -->
                <div v-if="viewingTask?.assignedTo?.length" class="text-sm">
                    <h3 class="font-medium text-dark mb-2">Assigned To</h3>
                    <div class="flex flex-wrap gap-2">
                        <div 
                            v-for="member in viewingTask.assignedTo" 
                            :key="member"
                            class="flex items-center gap-1 px-2 py-1 bg-light rounded-full text-xs"
                        >
                            <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-medium">
                                {{ member.charAt(0) }}
                            </div>
                            <span>{{ member }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex justify-end">
                <button 
                    @click="isViewTaskOpen = false"
                    class="btn-cancel !px-4 !py-2 text-sm"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<style>
/* Base colors */
:root {
  --color-button: #0463CA;
  --color-light-gray: #e5e7eb;
  --color-dark: #1f2937;
}

/* Common styles */
.text-dark { color: var(--color-dark); }
.bg-light { background-color: #f9fafb; }
.border-light-gray { border-color: var(--color-light-gray); }

/* Input styles */
input[type="number"],
input[type="number"].border-none {
  -moz-appearance: textfield;
  appearance: textfield;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Scrollbar styles */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #e0e0e0 #f5f5f5;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f5f5f5;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e0e0e0;
  border-radius: 2px;
}

/* Remove unused styles and keep only essential ones */
</style>