<script setup>
import { ref, watch } from 'vue'
import draggable from "vuedraggable";
import { ChevronRight } from 'lucide-vue-next'
import { createNewEpic } from '../ScrumServices/epicApi';
import { selectEpic } from '../ScrumComposables/UseEpics';
import Overlay from '../../../Components/Overlay.vue';
import {usePage} from '@inertiajs/vue3';
import axios from 'axios';
import Button from '../../../Components/Button.vue';

const page = usePage().props;
const props = defineProps({
    epic: {
        type: Array,
        required: true
    },
    epicSelected: {
        type: Object,
        required: true
    },
    projEpics: {
        type: Array,
        required: true
    },
    taskCounts: {
        type: Object,
        required: true
    },
    overlayEpicPosition: {
        type: Object,
        default: () => ({ x: 0, y: 0 })
    }
})

const epics = ref(props.epic)
const epicSelected = ref(props.epicSelected)
const emits = defineEmits(['updateEpicOrder', 'updateEpicSelected']);
const isCreatingEpic = ref(false);
const newEpic = ref('')
const epicProcessing = ref(false);
const isEpicOverlayOpen = ref(false)
const drag = ref(false)

// Watch for epic prop changes
watch(() => props.epic, (newVal) => {
  epics.value = newVal
}, { deep: true })

const EpicOverlayButtons = ref([
  {
    text: 'Edit Epic',
    function: () => {
      isEpicOverlayOpen.value = false
    }
  },
  {
    text: 'Delete Epic',
    function: () => {
      isEpicOverlayOpen.value = false
    }
  },
  {
    text: 'Open Epic',
    function: () => {
      isEpicOverlayOpen.value = false
    }
  }
])

const createEpic = () => {
  isCreatingEpic.value = !isCreatingEpic.value;
}

const epicKeyPress = (event) => {
    if (event.key === 'Enter') {
        createNewEpic(epics, epicProcessing, props.epicSelected, newEpic.value, props.projEpics, page.projectDetails);
    }
}

const handleEpicSelect = (epic) => {
  epics.value.forEach(e => e.isActive = e.epic_id === epic.epic_id)
  emits('updateEpicSelected', epic)
}

const handleDragEnd = () => {
  const updatedEpics = epics.value.map((epic, index) => ({
    epic_id: epic.epic_id,
    order: index + 1
  }));

  axios.post('/scrum/epics-reorder', {
    epics: updatedEpics,
    projectId: page.projectDetails.id
  })
  .then(() => {
    // Update local epic orders
    epics.value.forEach((epic, index) => {
      epic.order = index + 1;
    });
    emits('updateEpicOrder', epics.value);
  })
  .catch(error => {
    console.error('Error updating epic order:', error);
  });
}
</script>
<template>
    <Overlay 
    :isOpen="isEpicOverlayOpen" 
    :buttons="EpicOverlayButtons"
    :position="props.overlayEpicPosition"
    @close="isEpicOverlayOpen = false"
  />
    <div class="w-64 bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-xl font-semibold mb-4">Epic</h2>
        
        <div class="space-y-2">
          <draggable 
            v-model="epics" 
            class="space-y-2"
            item-key="epic_id"
            :group="{ name: 'epics' }"
            @start="drag=true"
            @end="handleDragEnd"
          >
          <template #item="{ element: epic }">
            <button @click="handleEpicSelect(epic)"
              class="w-full cursor-grab px-4 py-2 rounded-lg text-left flex items-center justify-between cursor-move"
              :class="epic.isActive ? 'bg-button text-light hover:bg-button-hover' : 'text-gray-700 hover:bg-gray-100'"
            >
              {{ epic.name }}
              <ChevronRight v-if="epic.isActive" class="w-5 h-5" />
            </button>
          </template>
          </draggable>
          <div v-if="isCreatingEpic" class="mt-4 relative">
            <div class="relative z-20">
              <input v-model="newEpic" @keypress="epicKeyPress" type="text" placeholder="Set new milestone" class="w-full px-4 py-2 outline-none" />
              <Button :disabled="epicProcessing" @click="() => {
                createNewEpic(epics, epicProcessing, epicSelected, newEpic, 
                props.projEpics, page.projectDetails);
                newEpic = '';
                isCreatingEpic = false;
                epicProcessing = false;
                }" class="btn-cancel mt-2 w-full">Create</Button>
            </div>
            <div class="fixed inset-0 z-10" @click="isCreatingEpic = false"></div>
          </div>
        </div>
        <button v-if="!isCreatingEpic" @click="createEpic" class="btn-cancel w-full mt-4 gap-4">
          <span class="text-xl">+</span> Create Epic
        </button>
      </div>
</template>