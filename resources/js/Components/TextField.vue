<script setup>
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
</script>
<template>
    <div class="flex flex-col gap-2">
        <label :for="name">{{ labeltxt }}</label>
        <div :class="`txt-primary ${style}`">
            <img :src="icon" class="h-fit w-fit" alt="icon">
            <input :type="type" class="bg-transparent h-full w-full outline-none p-3" :name="name" :placeholder="placeholder">
        </div>
    </div>
</template>
