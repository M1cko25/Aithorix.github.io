<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Users, Share2, Star, Video, UserCircle, Search, Plus, Mail, Phone, Upload, FileText, X, ChevronDown, Pencil } from 'lucide-vue-next'
import EventSidebar from '@/Components/EventSidebar.vue'
import Header from '@/Components/Header.vue'
import Button from '@/Components/Button.vue'
import Lira from '@/Components/Lira.vue'

const isLiraOpen = ref(false);
const page = ref({
    projectDetails: {
        name: 'Sample Project'
    }
})

const showModal = ref(false)
const showDropdown = ref(null)
const selectedFile = ref(null)
const showEditModal = ref(false)
const editingVendor = ref(null)

const newVendor = ref({
    name: '',
    company: '',
    email: '',
    phone: '',
    amount: '',
    status: 'Pending',
    documents: []
})

const vendors = ref([
    {
        id: 1,
        name: 'John Smith',
        company: 'Event Services Co.',
        status: 'Pending',
        email: 'john@eventservices.com',
        phone: '(555) 123-4567',
        amount: '$2,500',
        documents: [
            { name: 'Contract.pdf', icon: FileText },
            { name: 'Invoice.pdf', icon: FileText }
        ]
    },
    {
        id: 2,
        name: 'Sarah Johnson',
        company: 'Decor & Design LLC',
        status: 'Paid',
        email: 'sarah@decordesign.com',
        phone: '(555) 987-6543',
        amount: '$1,800',
        documents: [
            { name: 'Agreement.pdf', icon: FileText },
            { name: 'Quote.pdf', icon: FileText }
        ]
    },
    {
        id: 3,
        name: 'Mike Wilson',
        company: 'Catering Express',
        status: 'Overdue',
        email: 'mike@cateringexpress.com',
        phone: '(555) 456-7890',
        amount: '$3,200',
        documents: [
            { name: 'Menu.pdf', icon: FileText },
            { name: 'Terms.pdf', icon: FileText }
        ]
    }
])

const getStatusClass = (status) => {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'bg-[#FFF9E7] text-[#B59E45]'
        case 'paid':
            return 'bg-[#EBFFF3] text-success'
        case 'overdue':
            return 'bg-[#FFF2F2] text-error'
        default:
            return 'bg-neutral text-dark'
    }
}

const handleAddVendor = () => {
    if (!newVendor.value.name || !newVendor.value.company || !newVendor.value.email) {
        return
    }

    const vendor = {
        id: vendors.value.length + 1,
        ...newVendor.value,
        amount: newVendor.value.amount.startsWith('$') ? newVendor.value.amount : `$${newVendor.value.amount}`,
        documents: []
    }

    vendors.value.push(vendor)
    showModal.value = false
    resetNewVendor()
}

const resetNewVendor = () => {
    newVendor.value = {
        name: '',
        company: '',
        email: '',
        phone: '',
        amount: '',
        status: 'Pending',
        documents: []
    }
}

const updateVendorStatus = (vendorId, newStatus) => {
    const vendor = vendors.value.find(v => v.id === vendorId)
    if (vendor) {
        vendor.status = newStatus
    }
    showDropdown.value = null
}

const handleFileUpload = async (vendorId, event) => {
    const file = event.target.files[0]
    if (!file) return

    const vendor = vendors.value.find(v => v.id === vendorId)
    if (vendor) {
        vendor.documents.push({
            name: file.name,
            icon: FileText
        })
    }
    event.target.value = '' // Reset file input
}

const removeDocument = (vendorId, documentName) => {
    const vendor = vendors.value.find(v => v.id === vendorId)
    if (vendor) {
        vendor.documents = vendor.documents.filter(doc => doc.name !== documentName)
    }
}

// Click outside to close dropdown
const closeDropdowns = (e) => {
    if (!e.target.closest('.status-dropdown')) {
        showDropdown.value = null
    }
}

const handleEditVendor = (vendor) => {
    editingVendor.value = { ...vendor }
    showEditModal.value = true
}

const saveVendorEdit = () => {
    if (!editingVendor.value) return
    
    const index = vendors.value.findIndex(v => v.id === editingVendor.value.id)
    if (index !== -1) {
        vendors.value[index] = { ...editingVendor.value }
    }
    showEditModal.value = false
    editingVendor.value = null
}

onMounted(() => {
    document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdowns)
})
</script>

<template>
    <EventSidebar />
    <Header />
    <Head title=" | Vendor" />
    <div class="min-h-screen overflow-y-auto">
        <div class="ml-64 pt-16">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold">{{ page.projectDetails.name }}<span class="text-xl font-normal"> > Vendor</span></h1>
                    <div class="flex items-center -space-x-2"></div>
                    <button class="p-2 text-gray-600 hover:text-gray-800">
                        <Users class="w-5 h-5" />
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <button><Share2/></button>
                    <button><Star/></button>
                    <Link :href="route('meeting-home')">
            <Video />
          </Link>
                </div>
            </div>

            <!-- Vendor Management Header -->
            <div class="p-6 justify-between">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" placeholder="Search vendors..."
                            class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 w-64 focus:outline-none focus:border-button">
                        <Search class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" />
                    </div>
                    <Button @click="showModal = true" text="Add Vendor" variant="primary"
                        :style="`flex px-4 py-2 items-center gap-2`">
                        <Plus class="w-5 h-5" />
                        <span>Add Vendor</span>
                    </Button>
                </div>
            </div>

            <!-- Vendor Cards Grid -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="vendor in vendors" :key="vendor.id"
                    class="bg-gray-200 bg-light rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <!-- Vendor Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-light-blue flex items-center justify-center">
                                <span class="text-button font-medium text-lg">{{ vendor.name.charAt(0) }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div>
                                    <h3 class="font-semibold text-dark">{{ vendor.name }}</h3>
                                    <p class="text-sm text-dark-gray">{{ vendor.company }}</p>
                                </div>
                                <div class="relative status-dropdown">
                                    <button @click="showDropdown = showDropdown === vendor.id ? null : vendor.id"
                                        :class="['flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium transition-colors', 
                                            getStatusClass(vendor.status)]">
                                        {{ vendor.status }}
                                        <ChevronDown class="w-4 h-4" />
                                    </button>
                                    <div v-if="showDropdown === vendor.id"
                                        class="absolute left-0 mt-2 w-36 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                        <div class="py-1">
                                            <button @click="updateVendorStatus(vendor.id, 'Pending')"
                                                class="block w-full px-4 py-2 text-sm text-left hover:bg-light">
                                                Pending
                                            </button>
                                            <button @click="updateVendorStatus(vendor.id, 'Paid')"
                                                class="block w-full px-4 py-2 text-sm text-left hover:bg-light">
                                                Paid
                                            </button>
                                            <button @click="updateVendorStatus(vendor.id, 'Overdue')"
                                                class="block w-full px-4 py-2 text-sm text-left hover:bg-light">
                                                Overdue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button @click="handleEditVendor(vendor)" 
                            class="p-1.5 hover:bg-light rounded-full transition-colors">
                            <Pencil class="w-4 h-4 text-dark-gray" />
                        </button>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center gap-2 text-dark-gray">
                            <Mail class="w-4 h-4" />
                            <span class="text-sm">{{ vendor.email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-dark-gray">
                            <Phone class="w-4 h-4" />
                            <span class="text-sm">{{ vendor.phone }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-dark-gray">
                            <span class="font-medium text-dark">{{ vendor.amount }}</span>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div>
                        <h4 class="text-sm font-medium text-dark mb-2">Documents</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <div v-for="doc in vendor.documents" :key="doc.name"
                                class="group flex items-center gap-2 px-3 py-1.5 bg-light rounded-lg text-sm text-dark-gray hover:bg-neutral transition-colors">
                                <component :is="doc.icon" class="w-4 h-4" />
                                <span>{{ doc.name }}</span>
                                <button @click="removeDocument(vendor.id, doc.name)"
                                    class="opacity-0 group-hover:opacity-100 ml-1 hover:text-error transition-opacity">
                                    <X class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="file" :id="'file-upload-' + vendor.id" class="hidden"
                                @change="(e) => handleFileUpload(vendor.id, e)" />
                            <label :for="'file-upload-' + vendor.id"
                                class="flex items-center gap-2 text-button hover:text-button-hover text-sm font-medium cursor-pointer">
                                <Upload class="w-4 h-4" />
                                Upload Document
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Vendor Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-dark">Add New Vendor</h2>
                <button @click="showModal = false" class="text-dark-gray hover:text-dark">
                    <X class="w-6 h-6" />
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Vendor Name</label>
                    <input v-model="newVendor.name" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter vendor name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Company</label>
                    <input v-model="newVendor.company" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter company name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Email</label>
                    <input v-model="newVendor.email" type="email"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter email address" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Phone</label>
                    <input v-model="newVendor.phone" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter phone number" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Amount</label>
                    <input v-model="newVendor.amount" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter amount" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Status</label>
                    <select v-model="newVendor.status"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button">
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="showModal = false"
                    class="px-4 py-2 text-dark-gray hover:text-dark border border-gray-300 rounded-md">
                    Cancel
                </button>
                <button @click="handleAddVendor"
                    class="px-4 py-2 bg-button text-white rounded-md hover:bg-button-hover">
                    Add Vendor
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Vendor Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-dark">Edit Vendor</h2>
                <button @click="showEditModal = false" class="text-dark-gray hover:text-dark">
                    <X class="w-6 h-6" />
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Vendor Name</label>
                    <input v-model="editingVendor.name" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter vendor name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Company</label>
                    <input v-model="editingVendor.company" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter company name" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Email</label>
                    <input v-model="editingVendor.email" type="email"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter email address" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Phone</label>
                    <input v-model="editingVendor.phone" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter phone number" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-gray mb-1">Amount</label>
                    <input v-model="editingVendor.amount" type="text"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-button"
                        placeholder="Enter amount" />
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button @click="showEditModal = false"
                    class="px-4 py-2 text-dark-gray hover:text-dark border border-gray-300 rounded-md">
                    Cancel
                </button>
                <button @click="saveVendorEdit"
                    class="px-4 py-2 bg-button text-white rounded-md hover:bg-button-hover">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.status-dropdown {
    position: relative;
    display: inline-block;
}
</style>
