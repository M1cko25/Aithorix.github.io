<script setup>
defineProps({
    text: String,
    btn2: {
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
    }, 
    click: {
        type: Function,
        default: () => {}
    }
})
</script>
<template>
    <button @click="click"  v-if="social" :type="type" :class="`btn-cancel ${style} flex row justify-center items-center gap-2`">
        <img :src="pic">
        {{ text }}
    </button>
    <button @click="click" v-else-if="btn2 && !social" :type="type" :class="`btn-cancel ${style}`">{{ text }}</button>
    <button @click="click" v-else :class="`btn-primary ${style}`" :type="type">{{ text }}</button>
</template>