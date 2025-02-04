<script setup>
import LandingNav from '../Components/LandingNav.vue';

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});

</script>
<template>
    <LandingNav />
</template>