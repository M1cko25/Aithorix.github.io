<script setup>
import { ref, watch } from 'vue';

defineProps({
    type: {
        type: String,
        default: 'text'
    },
    style: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    }, placeholder: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    labeltxt: {
        type: String,
        default: ''
    },
    modelValue: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue']);

function onInput(event) {
  emit('update:modelValue', event.target.value);
}
</script>
<template>
    <div class="flex flex-col gap-2">
        <p>{{ labeltxt }}</p>
        <div :class="`txt-primary ${style}`">
            <img :src="icon" class="h-fit w-fit" alt="icon">
            <input :type="type" class="bg-transparent h-full w-full outline-none p-3" @input="onInput" :value="modelValue" autocomplete="email" :name="name" :placeholder="placeholder">
        </div>
    </div>
</template>
