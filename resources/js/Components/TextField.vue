<script setup>
import { Search } from 'lucide-vue-next';

let props = defineProps({
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
    },
    style: {
        type: String,
        default: ''
    },
    click: {
        type: Function,
        default: () => {}
    },
    hasButton: {
        type: Boolean,
        default: false
    }, Capitalized: {
        type: Boolean,
        default: false
    },
})

const emit = defineEmits(['update:modelValue']);

function onInput(event) {
  if (props.Capitalized) {
    emit('update:modelValue', event.target.value.toUpperCase());
  } else {
    emit('update:modelValue', event.target.value);
  }
}
</script>
<template>
    <div class="flex flex-col gap-2">
        <p v-if="labeltxt">{{ labeltxt }}</p>
        <div :class="`txt-primary flex flex-row ${style}`">
            <img v-if="icon && !hasButton" :src="icon" class="h-fit w-fit" alt="icon">
            <div v-if="type == 'search'" type="submit"><Search class="h-fit w-fit" /></div>
            <input :type="type" :class="`bg-transparent h-full w-full outline-none p-3`" @input="onInput" 
            :value="modelValue" autocomplete="email" @keyup.enter="$emit('onEnter')"
             :name="name" :placeholder="placeholder">
            <button v-if="hasButton" @click="click">
                <img :src="icon" class="h-8 w-8" alt="icon">
            </button>
        </div>
    </div>
</template>
<style scoped>
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px transparent inset !important;
    -webkit-text-fill-color: inherit !important;
    transition: background-color 5000s ease-in-out 0s;
}
</style>
