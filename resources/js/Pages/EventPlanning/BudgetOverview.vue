<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Users, Share2, Star, Video, PlusCircle, Trash2, Search, Pencil, Save, X } from 'lucide-vue-next'
import EventSidebar from '@/Components/EventSidebar.vue'
import Header from '@/Components/Header.vue'
import Button from '@/Components/Button.vue'
import Chart from 'chart.js/auto'

const page = ref({
    projectDetails: {
        name: 'Sample Project'
    }
})

const tabs = [
  { label: 'Overview', value: 'overview' },
  { label: 'Expenses', value: 'expenses' },
  { label: 'Categories', value: 'categories' }
]
const activeTab = ref('overview')
const totalBudget = ref(10000)
const expenses = ref([])

const newExpense = reactive({
  name: '',
  amount: 0,
  category: '',
  date: new Date().toISOString().split('T')[0]
})

const categories = ref([
  { id: '1', name: 'Venue', budget: 4000, color: '#4f46e5' },
  { id: '2', name: 'Catering', budget: 3000, color: '#0ea5e9' },
  { id: '3', name: 'Decoration', budget: 1000, color: '#10b981' },
  { id: '4', name: 'Entertainment', budget: 1500, color: '#f59e0b' },
  { id: '5', name: 'Miscellaneous', budget: 500, color: '#6b7280' }
])
const newCategory = reactive({
  name: '',
  budget: 0,
  color: '#4f46e5'
})

const editingId = ref(null)
const editForm = reactive({
  id: '',
  name: '',
  budget: 0,
  color: ''
})

const searchTerm = ref('')
const categoryFilter = ref('all')

const budgetChartRef = ref(null)
let budgetChart = null

const totalSpent = computed(() => {
  return expenses.value.reduce((sum, expense) => sum + expense.amount, 0)
})

const remainingBudget = computed(() => {
  return totalBudget.value - totalSpent.value
})

const percentageSpent = computed(() => {
  return (totalSpent.value / totalBudget.value) * 100
})

const totalAllocated = computed(() => {
  return categories.value.reduce((sum, category) => sum + category.budget, 0)
})

const unallocated = computed(() => {
  return totalBudget.value - totalAllocated.value
})

const percentageAllocated = computed(() => {
  return (totalAllocated.value / totalBudget.value) * 100
})

const spendingByCategory = computed(() => {
  return categories.value.map(category => {
    const spent = expenses.value
      .filter(expense => expense.category === category.id)
      .reduce((sum, expense) => sum + expense.amount, 0)
    
    return {
      ...category,
      spent,
      remaining: category.budget - spent,
      percentSpent: (spent / category.budget) * 100
    }
  })
})

const filteredExpenses = computed(() => {
  let filtered = [...expenses.value]
  
  if (searchTerm.value) {
    filtered = filtered.filter(expense => 
      expense.name.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }

  if (categoryFilter.value !== 'all') {
    filtered = filtered.filter(expense => expense.category === categoryFilter.value)
  }

  return filtered.sort((a, b) => 
    new Date(b.date).getTime() - new Date(a.date).getTime()
  )
})

function handleAddExpense() {
  if (!newExpense.name || newExpense.amount <= 0 || !newExpense.category) {
    return
  }
  
  const expense = {
    ...newExpense,
    id: crypto.randomUUID(),
    amount: Number(newExpense.amount)
  }
  
  expenses.value.push(expense)
  
  newExpense.name = ''
  newExpense.amount = 0
  newExpense.category = ''
  newExpense.date = new Date().toISOString().split('T')[0]
}

function handleDeleteExpense(id) {
  expenses.value = expenses.value.filter(expense => expense.id !== id)
}

function handleAddCategory() {
  if (!newCategory.name || newCategory.budget <= 0) {
    return
  }
  
  const category = {
    ...newCategory,
    id: crypto.randomUUID()
  }
  
  categories.value.push(category)
  
  newCategory.name = ''
  newCategory.budget = 0
  newCategory.color = '#4f46e5'
}

function handleStartEdit(category) {
  editingId.value = category.id
  Object.assign(editForm, category)
}

function handleCancelEdit() {
  editingId.value = null
}

function handleSaveEdit() {
  if (editForm && editingId.value) {
    const index = categories.value.findIndex(c => c.id === editingId.value)
    if (index !== -1) {
      categories.value[index] = { ...editForm }
    }
    editingId.value = null
  }
}

function handleDeleteCategory(id) {
  categories.value = categories.value.filter(category => category.id !== id)
}

function getCategoryName(categoryId) {
  const category = categories.value.find(cat => cat.id === categoryId)
  return category ? category.name : 'Unknown'
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
}

function initChart() {
  if (budgetChart) {
    budgetChart.destroy()
  }
  
  const ctx = budgetChartRef.value.getContext('2d')
  budgetChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: categories.value.map(cat => cat.name),
      datasets: [
        {
          data: categories.value.map(cat => cat.budget),
          backgroundColor: categories.value.map(cat => cat.color),
          borderColor: 'white',
          borderWidth: 2
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            padding: 20,
            font: {
              size: 12
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || ''
              const value = context.raw
              const total = context.dataset.data.reduce((a, b) => a + b, 0)
              const percentage = Math.round((value / total) * 100)
              return `${label}: $${value.toLocaleString()} (${percentage}%)`
            }
          }
        }
      }
    }
  })
}

function loadFromLocalStorage() {
  const savedBudget = localStorage.getItem('eventBudget')
  const savedExpenses = localStorage.getItem('eventExpenses')
  const savedCategories = localStorage.getItem('eventCategories')
  
  if (savedBudget) totalBudget.value = JSON.parse(savedBudget)
  if (savedExpenses) expenses.value = JSON.parse(savedExpenses)
  if (savedCategories) categories.value = JSON.parse(savedCategories)
}

function saveToLocalStorage() {
  localStorage.setItem('eventBudget', JSON.stringify(totalBudget.value))
  localStorage.setItem('eventExpenses', JSON.stringify(expenses.value))
  localStorage.setItem('eventCategories', JSON.stringify(categories.value))
}

onMounted(() => {
  loadFromLocalStorage()
  
  setTimeout(() => {
    if (budgetChartRef.value) {
      initChart()
    }
  }, 0)
})

watch([categories, activeTab], () => {
  if (activeTab.value === 'overview' && budgetChartRef.value) {
    setTimeout(() => {
      initChart()
    }, 0)
  }
  saveToLocalStorage()
}, { deep: true })

watch([totalBudget, expenses], () => {
  saveToLocalStorage()
}, { deep: true })

watch(activeTab, (newTab, oldTab) => {
  if (newTab === 'overview') {
    nextTick(() => {
      if (budgetChartRef.value) {
        initChart()
      }
    })
  }
})

const isValidHexColor = (color) => {
  return /^#[0-9A-Fa-f]{6}$/.test(color)
}

const validateHexColor = (event) => {
  let color = event.target.value
  
  // If input doesn't start with #, add it
  if (!color.startsWith('#')) {
    color = '#' + color
  }
  
  // Remove any non-hex characters
  color = '#' + color.replace(/[^0-9A-Fa-f]/g, '').slice(0, 6)
  
  // Update the color if it's different
  if (color !== newCategory.color) {
    newCategory.color = color
  }
}
</script>

<template>
  <EventSidebar />
  <Header />
  <Head :title="`${page.projectDetails.name} | Budget`" />
  
  <div class="min-h-screen overflow-y-auto">
    <div class="ml-64 pt-16">
      <div class="p-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold">
            {{ page.projectDetails.name }}
            <span class="text-xl font-normal"> > Budget Overview</span>
          </h1>
          <div class="flex items-center -space-x-2"></div>
          <button class="p-2 text-gray-600 hover:text-gray-800">
            <Users class="w-5 h-5" />
          </button>
        </div>
      </div>
      
      <!-- Budget Tracker App -->
      <div class="container mx-auto py-8 px-4">
        <!-- Budget Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="pb-2">
              <h3 class="text-lg font-semibold">Total Budget</h3>
              <p class="text-sm text-gray-500">Set your overall event budget</p>
            </div>
            <div class="flex items-center space-x-2">
              <span class="text-2xl font-bold">${{ totalBudget.toLocaleString() }}</span>
              <input
                type="number"
                v-model.number="totalBudget"
                class="w-32 px-3 py-2 border rounded-md"
              />
            </div>
          </div>
          
          <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="pb-2">
              <h3 class="text-lg font-semibold">Spent</h3>
              <p class="text-sm text-gray-500">Total expenses so far</p>
            </div>
            <div class="text-2xl font-bold">${{ totalSpent.toLocaleString() }}</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div class="bg-primary h-2 rounded-full" 
                :class="{ 'bg-red-500': percentageSpent > 100 }"
                :style="{ width: `${Math.min(percentageSpent, 100)}%` }"></div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg border shadow-sm p-6">
            <div class="pb-2">
              <h3 class="text-lg font-semibold">Remaining</h3>
              <p class="text-sm text-gray-500">Budget left to spend</p>
            </div>
            <div :class="['text-2xl font-bold', remainingBudget < 0 ? 'text-red-500' : '']">
              ${{ remainingBudget.toLocaleString() }}
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div class="bg-primary h-2 rounded-full" 
                :class="{ 'bg-red-500': remainingBudget < 0 }"
                :style="{ width: `${Math.max(Math.min((100 - percentageSpent), 100), 0)}%` }"></div>
            </div>
          </div>
        </div>
        
        <!-- Tabs -->
        <div class="mb-6">
          <div class="border-b">
            <nav class="flex space-x-4" aria-label="Tabs">
              <button 
                v-for="tab in tabs" 
                :key="tab.value"
                @click="activeTab = tab.value"
                :class="[
                  'py-2 px-1 border-b-2 font-medium text-sm',
                  activeTab === tab.value 
                    ? 'border-primary text-primary' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                {{ tab.label }}
              </button>
            </nav>
          </div>
        </div>
        <div v-if="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-lg border shadow-sm">
            <div class="p-6">
              <h3 class="text-lg font-semibold">Budget Allocation</h3>
              <p class="text-sm text-gray-500">How your budget is distributed</p>
            </div>
            <div class="p-6 h-80">
              <canvas ref="budgetChartRef"></canvas>
            </div>
          </div>
          
          <div class="bg-white rounded-lg border shadow-sm">
            <div class="p-6">
              <h3 class="text-lg font-semibold">Category Spending</h3>
              <p class="text-sm text-gray-500">Budget vs. actual spending by category</p>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div v-for="category in spendingByCategory" :key="category.id">
                  <div class="flex justify-between mb-1">
                    <span class="font-medium">{{ category.name }}</span>
                    <span :class="{ 'text-red-500': category.spent > category.budget }">
                      ${{ category.spent.toLocaleString() }} / ${{ category.budget.toLocaleString() }}
                    </span>
                  </div>
                  <div class="w-full rounded-full h-2" :style="{ backgroundColor: `${category.color}40` }">
                    <div 
                      class="h-2 rounded-full" 
                      :style="{ 
                        width: `${Math.min(category.percentSpent, 100)}%`, 
                        backgroundColor: category.color 
                      }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Expenses Tab -->
        <div v-if="activeTab === 'expenses'" class="bg-white rounded-lg border shadow-sm">
          <div class="p-6">
            <h3 class="text-lg font-semibold">Expense Tracker</h3>
            <p class="text-sm text-gray-500">Add and manage your event expenses</p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Expense Name</label>
                <input
                  v-model="newExpense.name"
                  placeholder="e.g., Venue deposit"
                  class="w-full px-3 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                <input
                  v-model.number="newExpense.amount"
                  type="number"
                  placeholder="0.00"
                  class="w-full px-3 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select
                  v-model="newExpense.category"
                  class="w-full px-3 py-2 border rounded-md"
                >
                  <option value="" disabled>Select category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input
                  v-model="newExpense.date"
                  type="date"
                  class="w-full px-3 py-2 border rounded-md"
                />
              </div>
            </div>
            
            <button @click="handleAddExpense" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md mb-6">
              <PlusCircle class="mr-2 h-4 w-4" />
              Add Expense
            </button>
            
            <div class="flex flex-col sm:flex-row gap-4 mb-4">
              <div class="relative flex-1">
                <Search class="absolute left-2 top-2.5 h-4 w-4 text-gray-400" />
                <input
                  v-model="searchTerm"
                  placeholder="Search expenses..."
                  class="w-full pl-8 pr-3 py-2 border rounded-md"
                />
              </div>
              
              <select
                v-model="categoryFilter"
                class="w-full sm:w-[180px] px-3 py-2 border rounded-md"
              >
                <option value="all">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            
            <div v-if="filteredExpenses.length > 0" class="border rounded-md">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expense</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 w-[50px]"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="expense in filteredExpenses" :key="expense.id">
                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ expense.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ getCategoryName(expense.category) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(expense.date) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">${{ expense.amount.toLocaleString() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                      <button
                        @click="handleDeleteExpense(expense.id)"
                        class="text-gray-400 hover:text-red-500"
                        aria-label="Delete expense"
                      >
                        <Trash2 class="h-4 w-4" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              {{ expenses.length === 0 
                ? "No expenses added yet. Add your first expense above."
                : "No expenses match your search criteria." }}
            </div>
          </div>
        </div>
        
        <!-- Categories Tab -->
        <div v-if="activeTab === 'categories'" class="bg-white rounded-lg border shadow-sm">
          <div class="p-6">
            <h3 class="text-lg font-semibold">Budget Categories</h3>
            <p class="text-sm text-gray-500">Manage your budget categories and allocations</p>
          </div>
          <div class="p-6">
            <div class="mb-6">
              <div class="flex justify-between mb-2">
                <span class="font-medium">Budget Allocation</span>
                <span>
                  ${{ totalAllocated.toLocaleString() }} / ${{ totalBudget.toLocaleString() }}
                  <span v-if="unallocated < 0" class="text-red-500 ml-2">
                    (Over budget by ${{ Math.abs(unallocated).toLocaleString() }})
                  </span>
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="h-2 rounded-full" 
                  :class="{ 'bg-red-500': unallocated < 0, 'bg-primary': unallocated >= 0 }"
                  :style="{ width: `${percentageAllocated > 100 ? 100 : percentageAllocated}%` }"
                ></div>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                <input
                  v-model="newCategory.name"
                  placeholder="e.g., Venue"
                  class="w-full px-3 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Budget ($)</label>
                <input
                  v-model.number="newCategory.budget"
                  type="number"
                  placeholder="0.00"
                  class="w-full px-3 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <div class="flex gap-2">
                  <input
                    v-model="newCategory.color"
                    type="color"
                    class="w-12 p-1 h-10"
                    @input="validateHexColor"
                  />
                  <div class="relative flex-1">
                    <input
                      v-model="newCategory.color"
                      class="w-full px-3 py-2 border rounded-md"
                      placeholder="#000000"
                      @input="validateHexColor"
                      pattern="^#[0-9A-Fa-f]{6}$"
                    />
                    <div class="absolute right-2 top-2 w-6 h-6 rounded-full border"
                      :style="{ backgroundColor: isValidHexColor(newCategory.color) ? newCategory.color : '#000000' }">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <button @click="handleAddCategory" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md mb-6">
              <PlusCircle class="mr-2 h-4 w-4" />
              Add Category
            </button>
            
            <div v-if="categories.length > 0" class="border rounded-md">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                    <th class="px-6 py-3 w-[100px]"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="category in categories" :key="category.id">
                    <td v-if="editingId === category.id" class="px-6 py-4 whitespace-nowrap">
                      <input
                        v-model="editForm.color"
                        type="color"
                        class="w-12 p-1 h-8"
                      />
                    </td>
                    <td v-else class="px-6 py-4 whitespace-nowrap">
                      <div 
                        class="w-6 h-6 rounded-full" 
                        :style="{ backgroundColor: category.color }"
                      ></div>
                    </td>
                    
                    <td v-if="editingId === category.id" class="px-6 py-4 whitespace-nowrap">
                      <input
                        v-model="editForm.name"
                        class="w-full px-3 py-1 border rounded-md"
                      />
                    </td>
                    <td v-else class="px-6 py-4 whitespace-nowrap font-medium">{{ category.name }}</td>
                    
                    <td v-if="editingId === category.id" class="px-6 py-4 whitespace-nowrap text-right">
                      <input
                        v-model.number="editForm.budget"
                        type="number"
                        class="w-24 ml-auto text-right px-3 py-1 border rounded-md"
                      />
                    </td>
                    <td v-else class="px-6 py-4 whitespace-nowrap text-right">${{ category.budget.toLocaleString() }}</td>
                    
                    <td v-if="editingId === category.id" class="px-6 py-4 whitespace-nowrap">
                      <div class="flex justify-end gap-1">
                        <button
                          @click="handleSaveEdit"
                          class="text-gray-400 hover:text-green-500"
                          aria-label="Save changes"
                        >
                          <Save class="h-4 w-4" />
                        </button>
                        <button
                          @click="handleCancelEdit"
                          class="text-gray-400 hover:text-gray-700"
                          aria-label="Cancel editing"
                        >
                          <X class="h-4 w-4" />
                        </button>
                      </div>
                    </td>
                    <td v-else class="px-6 py-4 whitespace-nowrap">
                      <div class="flex justify-end gap-1">
                        <button
                          @click="() => handleStartEdit(category)"
                          class="text-gray-400 hover:text-gray-700"
                          aria-label="Edit category"
                        >
                          <Pencil class="h-4 w-4" />
                        </button>
                        <button
                          @click="() => handleDeleteCategory(category.id)"
                          class="text-gray-400 hover:text-red-500"
                          aria-label="Delete category"
                        >
                          <Trash2 class="h-4 w-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              No categories added yet. Add your first category above.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
:root {
  --color-primary: #3b82f6;
  --color-primary-foreground: white;
}

.bg-primary {
  background-color: var(--color-primary);
}

.text-primary {
  color: var(--color-primary);
}

.border-primary {
  border-color: var(--color-primary);
}
</style>