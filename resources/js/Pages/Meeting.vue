<script setup>
import { ref } from "vue";

const roomUrl = ref("https://aithorix.daily.co/aithorix_test_room");
</script>
<template>
  <div class="w-full h-full">
    <iframe
    :src="roomUrl"
    allow="camera; microphone; fullscreen; display-capture"
    width="100%"
    height="100%"
  ></iframe>
  </div>
</template>