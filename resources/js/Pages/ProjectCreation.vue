<script setup>
import Logo from '../../../public/assets/logo.png';
import Icons from '../Icons.js';
import TextField from '../Components/TextField.vue';
import Button from '../Components/Button.vue';
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import Modal from '../Components/Modal.vue';

let projectName = ref('');

const props = usePage().props;

let projectKey = computed(() => {
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

let member = ref('');

const members = ref([
  {
    id: 1,
    name: 'Mico Jake Lutiva',
    email: 'lutiva@gmail.com',
    role: 'Scrum Master',
    avatar: '',
    isOwner: true
  },
  {
    id: 2,
    name: 'Andrei Odango',
    email: 'andreiodango05@gmail.com',
    role: 'Product Owner',
    avatar: '',
    isOwner: false
  },
  {
    id: 3,
    name: 'John Lorezo',
    email: 'johnny2@gmail.com',
    role: 'Frontend Developer',
    avatar: '',
    isOwner: false
  }
])

const roles = [
  'Scrum Master',
  'Product Owner',
  'Frontend Developer',
  'Backend Developer',
  'UI/UX Designer'
]

const updateRole = (memberId, newRole) => {
  const member = members.value.find(m => m.id === memberId)
  if (member) {
    member.role = newRole
  }
}

const form = useForm({
    name: projectName,
    key: projectKey,
})

let membersName = computed(() => {
  if (members.value.length > 2) {
    return `${members.value[0].name}, ${members.value[1].name} and ${members.value.length - 2} more`;
  }
  return members.value.map(member => member.name).join(', ');
});

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
}

const template = props.selectedTemplate.selectedTemplate;

</script>
<template>
    <Head title="| Create Project"/>
    <div class="min-h-screen flex justify-center">
        <!-- left side -->
         <div class="h-screen flex flex-col max-h-screen w-full gap-6 p-6">
             <div>
                <Link :href="route('template')" class="flex flex-row items-center gap-4 top-4 left-4">
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
                    <TextField
                    label="Project Name" 
                    v-model="projectName" 
                    type="text" labeltxt="Name *" 
                    name="projectName" 
                    placeholder="Project Name"/>

                    <TextField 
                    label="Key"
                    v-if="projectName.length >= 3"
                    v-model="projectKey"
                    type="text" labeltxt="Key *"
                    name="projectKey"
                    placeholder=""
                    :style="`w-1/2`"
                    />
                </div>
                <!-- Members section -->
                <div class="flex flex-col gap-5 p-4">
                    <div class="flex flex-col gap-2">
                      <h2 class="text-xl font-semibold text-gray-800 mb-4">Invite Collaborators</h2>
                    
                      <!-- Search member input -->
                      <TextField
                      label="search"
                      v-model="member"
                      type="search" labeltxt="Name or Email"
                      name="member"
                      placeholder="Jon, Jon@gmail.com"
                      />
                    </div>
                    <button @click="openModal" class="flex flex-row gap-2 items-center justify-between hover:bg-neutral p-2 rounded-lg w-full">
                      <div class="flex flex-row gap-2 items-center">
                        <div class="flex flex-row -space-x-2">
                          <div v-for="(mber, index) in members.slice(0, 3)" :key="mber.id">
                            <img v-if="mber.avatar" :src="mber.avatar" alt="" class="w-10 h-10 rounded-full">
                            <div v-else :class="`w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center
                            ${(index < 2) ? 'bg-cyan border' : 'bg-light text-2xl'}`">
                              {{ (index < 2) ? mber.name.slice(0, 2).toUpperCase() : "+" }}
                            </div>
                          </div>
                        </div>
                        <p class="text-sm">{{ membersName }}</p>
                      </div>
                      <ChevronRight/>
                    </button>
                </div>
                <div class="flex flex-row items-center gap-2 p-4 my-6 bg-light rounded-lg">
                  <div>
                    <p class="text-lg">Selected Template: {{ template }}</p>
                  </div>
                </div>
                <div class="w-full flex justify-end">
                    <Button :pic="Icons.startIcon" text="Create Project" :style="`px-4 py-2 flex hover:flex-row-reverse transition-all duration-500 ease-in-out gap-4`" :disabled="!projectName || !projectKey" />
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
            <div v-for="member in members" :key="member.id" class="flex flex-col items-center justify-between gap-2 p-2">
              <p>{{  member.name }}</p>
            </div>
          </Modal>
    </div>
</template>
<style scoped>
* {
    box-sizing: border-box;
}
</style>