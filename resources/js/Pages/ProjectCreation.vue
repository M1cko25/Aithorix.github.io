<script setup>
import Logo from '../../../public/assets/logo.png';
import Icons from '../Icons.js';
import TextField from '../Components/TextField.vue';
import Button from '../Components/Button.vue';
import DropDown from '../Components/DropDown.vue';
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ChevronRight, Delete, MoveRight } from 'lucide-vue-next';
import Modal from '../Components/Modal.vue';
import { watch } from 'vue';
import axios from 'axios';

const searchResults = ref([]);
const isSearching = ref(false);
let projectName = ref('');
let memberToRemoveId = ref(null);

const props = usePage().props;
let projectKey = ref('');
let generatedKey = computed( () => {
  if (!projectName.value) return '';
  
  const words = projectName.value.split(' ');
  
  if (words.length === 1) {
    return words[0].slice(0, 4).toUpperCase();
  }
  
  if (words.length === 2) {
    return (words[0].length <= 4 && words[0].length >= 2) 
      ? words[0].toUpperCase()
      : words[0].slice(0, 2).toUpperCase() + words[1].slice(0, 2).toUpperCase();
  }
  
  return words.map(word => word[0]).join('').slice(0, 4).toUpperCase();
});

watch(projectName, (newValue) => {
  if (newValue) {
    projectKey.value = generatedKey.value;
  }
});

let member = ref('');

const members = ref([
  {
    id: props.auth.user.id,
    name: props.auth.user.name,
    email: props.auth.user.email,
    role: 'Scrum Master',
    avatar: props.auth.user.avatar,
    isOwner: true
  },
])

const roles = [
  'Product Owner',
  'Frontend Developer',
  'Backend Developer',
  'UI/UX Designer',
  'Quality Assurance',
  'Custom'
]

const form = useForm({
    name: projectName.value,
    key: projectKey.value,
    owner_id: props.auth.user.id,
    email : props.auth.user.email,
    google_email: props.auth.user.google_email,
    slack_email: props.auth.user.slack_email,
    template: props.selectedTemplate.selectedTemplate,
    members: members
})

watch(members, (newValue) => {
  form.members = newValue;
});

watch(projectName, (newValue) => {
    form.name = newValue;
});
watch(projectKey, (newValue) => {
    form.key = newValue;
});

watch(member, async (newValue) => {
  if (newValue.length >= 2) {
    isSearching.value = true;
    try {
      const response = await axios.get('/search-users', {
        params: { search: newValue }
      });
      
      searchResults.value = response.data.filter(user => 
        !members.value.some(member => member.id === user.id)
      );
    } catch (error) {
      console.error('Search failed:', error);
    }
    isSearching.value = false;
  } else {
    searchResults.value = [];
  }
});

// Add function to add member
const addMember = (user) => {
  members.value.push({
    id: user.id,
    name: user.name,
    email: user.email || user.google_email || user.slack_email,
    role: roles[0], // Default role
    avatar: user.avatar,
    isOwner: false
  });
  form.members = members.value;
  searchResults.value = searchResults.value.filter(r => r.id !== user.id);
  member.value = ''; // Clear search
};

const isRemoveModal = ref(false);
const RemoveOpen = (memberId) => {
  memberToRemoveId.value = memberId;
  isRemoveModal.value = true;
}

// Remove member function
const removeMember = () => {
  if (memberToRemoveId.value !== null) {
    members.value = members.value.filter(member => member.id !== memberToRemoveId.value);
    form.members = members.value;
    memberToRemoveId.value = null;
    isRemoveModal.value = false;
  }
};

// add custom role function
const customRole = ref('');

const addCustomRole = (memberId) => {
  if (customRole.value.trim()) {
    roles.splice(roles.length - 1, 0,customRole.value.trim());
    // Update the member's role
    updateMemberRole(memberId, customRole.value.trim());
    customRole.value = ''; // Reset the input
  }
};

//update member role function
const updateMemberRole = (memberId, newRole) => {
    const memberIndex = members.value.findIndex(m => m.id === memberId);
    if (memberIndex !== -1) {
      members.value[memberIndex] = {
            ...members.value[memberIndex],
            role: newRole
      }
      form.members = [...members.value];
    }
};

let membersName = computed(() => {
  if (members.value.length > 2) {
    return `${members.value[0].name}, ${members.value[1].name} and ${members.value.length - 2} 
    more`;
  }
  return members.value.map(member => member.name).join(', ');
});

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
}

const template = props.selectedTemplate.selectedTemplate;

const createProject = () => {
  form.post('/create-project');
}


</script>
<template>
    <Head title="| Create Project"/>
    <div class="min-h-screen flex justify-center">
        <!-- left side -->
         <div class="h-screen flex flex-col max-h-screen w-full gap-6 p-6">
             <div>
                <Link :href="route('template')" preserve-scroll class="flex flex-row 
                items-center gap-4 top-4 left-4">
                    <img :src="Icons.leftIcon">
                    <p>Back to Template Selection</p>
                </Link>
             </div>
             <div class="h-full flex flex-col lg:w-3/4">
                <div class="flex flex-col gap-3">
                    <img :src="Logo" alt="Aithorix Logo" class="w-16 h-16">
                    <h1 class="text-3xl font-bold">Create Project</h1>
                    <p>Welcome to Aithorix, let's create your first project</p>
                </div>
                <div class="flex flex-col gap-2 p-4">
                    <div>
                      <TextField
                      label="Project Name" 
                      v-model="projectName" 
                      type="text" labeltxt="Name *" 
                      name="projectName" 
                      placeholder="Project Name"/>
                      <p v-if="form.errors.name" class="text-red-600">{{form.errors.name}}</p>
                    </div>

                    <div>
                      <TextField 
                      label="Key"
                      v-if="projectName.length >= 3"
                      v-model="projectKey"
                      type="text" labeltxt="Key *"
                      name="projectKey"
                      placeholder=""
                      :style="`w-1/2`"
                      Capitalized
                      />
                      <p v-if="form.errors.key && !form.errors.name" class="text-red-600">{{form.errors.key}}</p>
                    </div>
                </div>
                <!-- Members section --> 
                <div class="flex flex-col w-full gap-5 p-4">
                    <div class="relative flex w-full flex-col gap-2">
                      <h2 class="text-xl font-semibold text-gray-800 mb-4">Invite Collaborators</h2>
                    
                      <!-- Search member input -->
                        <TextField
                        label="search"
                        v-model="member"
                        type="search" labeltxt="Name or Email"
                        name="member"
                        placeholder="Jon, Jon@gmail.com"
                        :style="`w-full`"
                        />
                        <div v-if="searchResults.length > 0" class="absolute mt-1 bottom-0 translate-y-full w-full bg-light shadow-lg rounded-lg max-h-60 overflow-auto z-50">
                        <div v-for="user in searchResults" 
                            :key="user.id"
                            @click="addMember(user)"
                            class="p-2 hover:bg-gray-100 cursor-pointer flex items-center gap-2">
                          <div v-if="user.avatar" class="w-8 h-8 rounded-full">
                            <img :src="user.avatar" class="w-full h-full rounded-full" :alt="user.name">
                          </div>
                          <div v-else class="w-8 h-8 rounded-full bg-cyan flex items-center justify-center">
                            {{ user.name.slice(0, 2).toUpperCase() }}
                          </div>
                          <div>
                            <div class="font-medium">{{ user.name }}</div>
                            <div v-if="user.email" class="text-sm text-gray-600">{{ user.email }}</div>
                            <div v-else-if="user.google_email" class="text-sm text-gray-600">{{ user.google_email }}</div>
                            <div v-else class="text-sm text-gray-600">{{ user.slack_email }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <button @click="openModal" class="flex flex-row gap-2 items-center 
                    justify-between hover:bg-neutral p-2 rounded-lg w-full">
                      <div class="flex flex-row gap-2 items-center">
                        <div class="flex flex-row -space-x-2">
                          <div v-for="(mber, index) in members.slice(0, 3)" :key="mber.id">
                            <img v-if="mber.avatar" :src="mber.avatar" alt="" class="w-10 
                            h-10 rounded-full">
                            <div v-else :class="`w-10 h-10 rounded-full bg-gray-300 flex
                            items-center justify-center
                            ${(index < 2) ? 'bg-cyan border' : 'bg-light text-2xl'}`">
                              {{ (index < 2) ? mber.name.slice(0, 2).toUpperCase() : "+" }}
                            </div>
                          </div>
                        </div>
                        <p class="text-sm prevent-select">{{ membersName }}</p>
                      </div>
                      <ChevronRight/>
                    </button>
                </div>
                <div class="flex flex-row items-center gap-2 p-4 my-6 bg-light rounded-lg">
                  <div>
                    <p class="text-lg">Selected Template: {{ template }}</p>
                  </div>
                </div>
                <div class="flex flex-row justify-between">
                  <Link :href="route('home')">Skip</Link>
                  <Button :style="`btn-primary overflow-hidden flex gap-2 group`" @click="createProject">
                    <MoveRight class="transform transition-transform duration-300 ease-in-out group-hover:translate-x-32 h-6 w-6"/>
                    <p :type="type" class="transform transition-transform duration-300 group-hover:-translate-x-8">Create Project</p>
                  </Button>
                </div>
            </div>
         </div>
         <!-- right side -->
          <div class="h-screen border lg:w-3/4 p-6">
            <div class="bg-dark-gray w-full h-full rounded-lg">
                
            </div>
          </div>
          <Modal v-model:modelValue="isModalOpen" title="Members" :subtitle="`(${members.length} 
          collaborators in this project)`"
          subtitleStyle="text-sm">
            <div v-for="(member, index) in members" :key="member.id" class="flex flex-col
             items-center w-full justify-between gap-2 p-2">
              <div class="flex flex-row gap-2 items-center justify-between w-full">
                <div class="flex flex-row gap-2 items-center">
                  <img v-if="member.avatar" :src="member.avatar" alt="" class="md:w-10 md:h-10 w-8 h-8
                  rounded-full">
                  <div v-else :class="`w-10 h-10 rounded-full bg-gray-300 flex items-center 
                  justify-center
                  bg-cyan'}`">
                    <p>{{ member.name.slice(0, 2).toUpperCase() }}</p>
                  </div>
                  <div class="flex flex-col">
                    <p class="prevent-select md:text-lg text-sm">{{ member.name }}</p>
                    <p class="md:text-sm text-xs">{{ member.email }}</p>
                  </div>
                  <div v-if="index === 0" class="px-2 py-1 rounded-lg bg-blue mx-5 text-white md:block hidden">
                    <p class="text-xs">Owner</p>
                  </div>
                </div>
                <DropDown v-if="index === 0 && template == 'Scrum'" oneValue :value="member.role"/>
                <div v-else class="flex flex-row md:gap-2 items-center">
                  <DropDown v-if="member.role !== 'Custom'" :options="roles" style1 v-model="member.role" @select="(value) => updateMemberRole(member.id, value)"/>
                  <TextField v-if="member.role === 'Custom'" v-model="customRole" 
                  hasButton :icon="Icons.plusIcon" :style="`w-3/4 h-10`" 
                  @click="addCustomRole(member.id)" @onEnter="addCustomRole(member.id)"  />
                  <button @click="RemoveOpen(member.id)">
                    <Delete class="text-red-600"/>
                  </button>
                </div>
              </div>
             </div>
          </Modal>
          <Modal v-model:modelValue="isRemoveModal">
            <div class="flex flex-col justify-center items-center gap-4">
              <p class="text-lg">are you sure you want to remove this member?</p>
              <div class="flex flex-row gap-2 justify-between w-full">
                <Button :style="`btn-cancel`" :click="()=> { isRemoveModal = false}">No</Button>
                <Button :style="`btn-primary w-full`" :click="removeMember">Yes</Button>
              </div>
            </div>
          </Modal>
    </div>
</template>
<style scoped>
* {
    box-sizing: border-box;
}
</style>