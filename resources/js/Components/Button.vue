<script setup>
defineProps({
    text: String,
    style2: {
        type: Boolean,
        default: false
    },
    style: {
        type: String,
        default: ''
    },
    social: {
        type: Boolean,
        default: false
    },
    pic: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'button'
    }
})
</script>
<template>
    <button v-if="social" :type="type" :class="`btn-cancel ${style} flex row justify-center items-center gap-2`">
        <img :src="pic">
        {{ text }}
    </button>
    <button v-else-if="style2 && !social" :type="type" :class="`btn-cancel ${style}`">{{ text }}</button>
    <button v-else :class="`btn-primary ${style}`" :type="type">{{ text }}</button>
</template>