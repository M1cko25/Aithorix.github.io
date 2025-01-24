<script setup>
import { route } from '../../../vendor/tightenco/ziggy/src/js';

defineProps({
    text: String,
    closeBtn: {
        type: Boolean,
        default: false
    },
    disableBtn: {
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
    },
    href: {
        type: String,
        default: ''
    }
})
</script>
<template>
    <a v-if="social" :href="href" :class="`btn-cancel ${style} flex row justify-center items-center gap-2`">
        <img :src="pic">
        {{ text }}
    </a>
    <button @click="click" v-else-if="closeBtn && !social" :type="type" :class="`btn-cancel ${style}`">{{ text }}</button>
    <button @click="click" v-else-if="disableBtn && !social" :class="`btn-disable ${style}`" disabled>{{ text }}</button>
    <div v-else :class="`btn-primary ${style}`">
        <img :src="pic" class="w-6 h-6">
        <button @click="click" :type="type">{{ text }}</button>
    </div>
</template>