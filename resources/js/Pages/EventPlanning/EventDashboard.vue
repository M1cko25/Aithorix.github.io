<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { 
    Users, Share2, Star, Video, Calendar, DollarSign, 
    Store, ClipboardList, ChevronRight 
} from 'lucide-vue-next'
import EventSidebar from '@/Components/EventSidebar.vue'
import Header from '@/Components/Header.vue'
import Button from '@/Components/Button.vue'
import VueApexCharts from 'vue3-apexcharts'

// Initialize the required data
const events = ref([
    {
        id: "1",
        title: "Venue Tour",
        description: "Initial venue walkthrough with client",
        date: "2025-03-15",
        startTime: "10:00",
        endTime: "11:30",
        location: "Grand Hall",
        assignedTo: ["Sarah", "Michael"],
        category: "Planning",
    },
    // Add more sample events as needed
])

const vendors = ref([
    {
        id: 1,
        name: "John Smith",
        company: "Event Services Co.",
        status: "Pending",
        amount: "$2,500",
    },
    // Add more sample vendors as needed
])

const totalBudget = ref(10000)
const totalSpent = ref(5000)

const page = ref({
    projectDetails: {
        name: 'Sample Project'
    }
})

// Format date function
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Update stats with proper references
const stats = ref([
    {
        icon: Calendar,
        label: 'Upcoming Events',
        value: computed(() => events.value.filter(e => new Date(e.date) > new Date()).length),
        period: 'this month'
    },
    {
        icon: DollarSign,
        label: 'Total Budget',
        value: computed(() => `$${totalBudget.value.toLocaleString()}`),
        period: 'allocated'
    },
    {
        icon: Store,
        label: 'Active Vendors',
        value: computed(() => vendors.value.filter(v => v.status === 'Pending').length),
        period: 'pending approval'
    },
    {
        icon: ClipboardList,
        label: 'Total Events',
        value: computed(() => events.value.length),
        period: 'created'
    }
])

// Update activities computed
const activities = computed(() => {
    const allActivities = [
        ...events.value.map(event => ({
            id: event.id,
            type: 'event',
            title: event.title,
            date: formatDate(event.date),
            category: event.category
        })),
        ...vendors.value.map(vendor => ({
            id: vendor.id,
            type: 'vendor',
            name: vendor.name,
            status: vendor.status,
            amount: vendor.amount,
            date: formatDate(new Date()) // Add current date for vendors
        }))
    ]
    return allActivities.sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 5)
})

// Update chart data
const eventCategoryChart = ref({
    series: computed(() => {
        const categoryCounts = events.value.reduce((acc, event) => {
            acc[event.category] = (acc[event.category] || 0) + 1
            return acc
        }, {})
        return Object.values(categoryCounts)
    }),
    chartOptions: {
        labels: ['Planning', 'Client Meeting', 'Setup', 'Event Day', 'Post-Event', 'Team Meeting'],
        colors: ['#0463CA', '#09B1EC', '#65C2F5', '#FF3232', '#36454A', '#16A249'],
        chart: {
            type: 'donut'
        }
    }
})

const budgetChart = ref({
    series: computed(() => {
        return [
            (totalSpent.value / totalBudget.value) * 100,
            ((totalBudget.value - totalSpent.value) / totalBudget.value) * 100
        ]
    }),
    chartOptions: {
        labels: ['Spent', 'Remaining'],
        colors: ['#0463CA', '#E0E0E0'],
        chart: {
            type: 'donut'
        }
    }
})
</script>

<template>
    <EventSidebar />
    <Header />
    <Head title=" | Dashboard" />
    
    <div class="min-h-screen overflow-y-auto">
        <div class="ml-64 pt-16">
            <!-- Header -->
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold">
                        {{ page.projectDetails.name }}
                        <span class="text-xl font-normal"> > Dashboard</span>
                    </h1>
                    <button class="p-2 text-gray-600 hover:text-gray-800">
                        <Users class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="flex items-center gap-4">
                    <button><Share2 /></button>
                    <button><Star /></button>
                    <Button text="Create Meeting" variant="primary" :style="`flex px-4 py-2 items-center gap-2`">
                        <Video />
                    </Button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="stat in stats" 
                    :key="stat.label" 
                    class="bg-light rounded-xl p-6 shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-dark-gray mb-2">{{ stat.label }}</p>
                            <p class="text-3xl font-bold text-dark">{{ typeof stat.value === 'function' ? stat.value() : stat.value }}</p>
                            <p class="text-sm text-dark-gray mt-2">{{ stat.period }}</p>
                        </div>
                        <component :is="stat.icon" class="w-6 h-6 text-button" />
                    </div>
                </div>
            </div>

            <!-- Charts and Activities Grid -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activities -->
                <div class="bg-light rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Recent Activities</h2>
                    <div class="space-y-4">
                        <div v-for="activity in activities" :key="activity.id" 
                            class="flex items-start gap-4 p-4 bg-white rounded-lg"
                        >
                            <div class="flex-1">
                                <p class="text-sm">
                                    <span v-if="activity.type === 'event'" class="text-button">
                                        New event created: {{ activity.title }}
                                    </span>
                                    <span v-else class="text-success">
                                        Vendor updated: {{ activity.name }} - {{ activity.status }}
                                    </span>
                                </p>
                                <p class="text-xs text-dark-gray mt-1">{{ activity.date }}</p>
                            </div>
                            <ChevronRight class="w-4 h-4 text-dark-gray" />
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 gap-6">
                    <!-- Event Categories Chart -->
                    <div class="bg-light rounded-xl p-6 shadow-sm">
                        <h2 class="text-lg font-semibold mb-4">Event Categories</h2>
                        <VueApexCharts
                            type="donut"
                            :options="eventCategoryChart.chartOptions"
                            :series="eventCategoryChart.series"
                            height="250"
                        />
                    </div>

                    <!-- Budget Overview Chart -->
                    <div class="bg-light rounded-xl p-6 shadow-sm">
                        <h2 class="text-lg font-semibold mb-4">Budget Overview</h2>
                        <VueApexCharts
                            type="donut"
                            :options="budgetChart.chartOptions"
                            :series="budgetChart.series"
                            height="250"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-light {
    background-color: #F5F5F5;
}

.text-button {
    color: #0463CA;
}

.text-success {
    color: #16A249;
}

.text-dark {
    color: #36454A;
}

.text-dark-gray {
    color: #737373;
}

.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>
