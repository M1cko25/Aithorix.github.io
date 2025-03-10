<script setup lang="ts">
import { ref } from 'vue'
import { CheckCircle } from 'lucide-vue-next'
import Header from '../../components/Header.vue'
import Sidebar from '../../components/Sidebar.vue'

const billingCycle = ref('monthly')

const handleUpgrade = () => {
    // Handle upgrade logic here
    console.log('Upgrading plan:', billingCycle.value)
}
</script>

<template>
    <Header />
    <Sidebar />
    <div class="pt-24 ml-64 min-h-screen bg-gray-0 p-6">
        <div class="mx-auto max-w-5xl rounded-xl bg-white p-8 shadow-lg">
            <h1 class="mb-6 text-2xl font-bold">Upgrade To Plus</h1>

            <!-- Plan Selection -->
            <div class="mb-8">
                <h2 class="mb-2 text-lg font-medium">Select a plan</h2>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer rounded-lg border p-4 transition-colors"
                        :class="{ 'border-blue-500 bg-blue-50': billingCycle === 'monthly', 'border-gray-200': billingCycle !== 'monthly' }">
                        <input type="radio" name="billing" value="monthly" v-model="billingCycle" class="mr-2" />
                        Monthly
                    </label>
                    <label class="flex-1 cursor-pointer rounded-lg border p-4 transition-colors"
                        :class="{ 'border-blue-500 bg-blue-50': billingCycle === 'annually', 'border-gray-200': billingCycle !== 'annually' }">
                        <input type="radio" name="billing" value="annually" v-model="billingCycle" class="mr-2" />
                        Annually
                    </label>
                </div>
            </div>

            <!-- Overview -->
            <div class="mb-8">
                <h2 class="mb-2 text-lg font-medium">Overview</h2>
                <p class="text-gray-600">
                    Designed for project managers, team leaders, freelancers, and small to medium-sized teams who
                    require flexible and powerful project management tools without a long-term commitment.
                </p>
            </div>

            <!-- Features -->
            <div class="mb-8">
                <h2 class="mb-4 text-lg font-medium">What you get?</h2>
                <ul class="space-y-4">
                    <li class="flex items-center">
                        <CheckCircle class="mr-2 h-5 w-5 text-blue-500" />
                        <span><span class="font-medium text-button">UNLIMITED</span> Video Call Meetings</span>
                    </li>
                    <li class="flex items-center">
                        <CheckCircle class="mr-2 h-5 w-5 text-blue-500" />
                        <span><span class="font-medium text-button">UNLIMITED</span> Access to AI LIRA</span>
                    </li>
                    <li class="flex items-center">
                        <CheckCircle class="mr-2 h-5 w-5 text-blue-500" />
                        <span>Gantt Chart Access</span>
                    </li>
                </ul>
            </div>

            <!-- Pricing and CTA -->
            <div class="flex items-center justify-between border-t pt-6">
                <div>
                    <span class="text-3xl font-bold">$8</span>
                    <span class="text-gray-600">(billed monthly)</span>
                </div>
                <button @click="handleUpgrade" class="btn-primary">
                    Upgrade To Plus
                </button>
            </div>
        </div>
    </div>
</template>
