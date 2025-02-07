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
        default: 'px-6 py-2'
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
    }, 
    cta: {
        type: Boolean,
        default: false
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
    <button v-else-if="cta" @click="click" :type="type" :class="`btn-primary overflow-hidden group ${style}`">
        <img :src="pic" class="transform transition-transform duration-300 ease-in-out group-hover:translate-x-32 h-6 w-6">
        <p :type="type" class="transform transition-transform duration-300 group-hover:-translate-x-8">{{ text }}</p>
    </button>
    <button v-else  @click="click" :type="type" :class="`btn-primary ${style}`">
        <img v-if="pic" :src="pic" class="w-6 h-6">
        <slot/>
        <p>{{ text }}</p>
    </button>
</template>
<style scoped>

</style>