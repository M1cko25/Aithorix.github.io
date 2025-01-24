<script setup>
import { ref, watch } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    subtitle: {
        type: String,
        default: ''
    },
    subtitleStyle: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(["update:modelValue"]);

const show = ref(props.modelValue);

watch(
  () => props.modelValue,
  (newValue) => {
    show.value = newValue;
  }
);

const close = () => {
  show.value = false;
  emit("update:modelValue", false);
};
</script>
<template>
    <div v-if="show" @click.self="close" class="absolute w-screen h-screen bg-dark/50 z-10 flex flex-col justify-center items-center">
        <div class="bg-light p-6 rounded-lg shadow-md w-1/2 h-fit">
            <header v-if="props.title" class="flex flex-row justify-between border-b border-dark p-4">
                <div class="flex flex-col">
                    <h1 class="text-xl">{{ props.title }}</h1>
                    <p v-if="props.subtitle" :class="props.subtitleStyle">{{ props.subtitle }}</p>
                </div>
                <button @click="close">
                    <X/>
                </button>
            </header>
            <div class="flex flex-col items-start p-4">
                <slot/>
            </div>
        </div>
    </div>
</template>