<script setup>
import { route } from '../../../vendor/tightenco/ziggy/src/js';

defineProps({
    disable: {
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
    pic: {
        type: String,
        default: ''
    },
})
</script>
<template>
    <a v-if="social" :href="href" :class="`btn-cancel ${style} py-2 w-full text-lg flex row justify-center items-center gap-2`">
        <img :src="pic">
        <slot/>
    </a>
    <button v-else @click="click" :class="`${style}`" :disabled="disable">
        <slot/>
    </button>
</template>
<style scoped>

</style>