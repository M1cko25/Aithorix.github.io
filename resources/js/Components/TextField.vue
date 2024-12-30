<script setup>
import { ref, watch } from 'vue';

const input = ref('');
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
    }
})

// defineEmits({update:newInput})

// watch(() => input, (newValue) => {
//     emit('update:newinput', newValue)
// })
</script>
<template>
    <div class="flex flex-col gap-2">
        <p>{{ labeltxt }}</p>
        <div :class="`txt-primary ${style}`">
            <img :src="icon" class="h-fit w-fit" alt="icon">
            <input :type="type" class="bg-transparent h-full w-full outline-none p-3" v-model="input" autocomplete="email" :name="name" :placeholder="placeholder">
        </div>
    </div>
</template>
