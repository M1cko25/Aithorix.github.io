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
    <div v-if="show" class="w-screen h-screen bg-dark/50 absolute z-8"></div>
    <Transition name="modal-pop">
        <div v-if="show" @click.self="close" class="absolute w-screen h-screen z-10 flex flex-col justify-center items-center">
            <div class="bg-light p-6 rounded-lg shadow-md lg:w-1/2 w-full h-fit">
                <header v-if="props.title" class="flex flex-row justify-between border-b border-dark p-4">
                    <div class="flex flex-col">
                        <h1 class="text-xl">{{ props.title }}</h1>
                        <p v-if="props.subtitle" :class="props.subtitleStyle">{{ props.subtitle }}</p>
                    </div>
                    <button @click="close">
                        <X/>
                    </button>
                </header>
                <div class="flex flex-col justify-center items-center items-start p-4">
                    <slot/>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.modal-pop-enter-active,
.modal-pop-leave-active {
    transition: all 0.3s ease;
}

.modal-pop-enter-from,
.modal-pop-leave-to {
    transform: scale(0.8);
    opacity: 0;
}

.modal-pop-enter-to,
.modal-pop-leave-from {
    transform: scale(1);
    opacity: 1;
}
</style>
