<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import noData from '@/assets/noDataIllustration.svg'
const props = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue']);
</script>
<template>
    <Modal v-model:modelValue="props.modelValue" >
        <div class="flex flex-col items-center justify-center gap-4">
            <img :src="noData" class="w-28 h-28" alt="no data">
            <p class="text-xl">Please add a backlog to sprint</p>
            <button class="btn-primary" @click="emit('update:modelValue', false)">Okay</button>
        </div>
    </Modal>
</template>