<template>
  <iframe
    :src="roomUrl"
    allow="camera; microphone; fullscreen; display-capture"
    width="100%"
    height="500px"
  ></iframe>
</template>

<script setup>
import { ref } from "vue";

const roomUrl = ref("https://aithorix.daily.co/hello-daily");
</script>
