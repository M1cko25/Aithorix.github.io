<script setup>
import LandingNav from '../Components/LandingNav.vue';
import DropDown from '../Components/DropDown.vue';
import { ref } from 'vue';

let options = [ 'Option 1', 'Option 2', 'Option 3']
</script>
<template>
    <LandingNav />
    <DropDown :options="options" :style1="false"/>
</template>