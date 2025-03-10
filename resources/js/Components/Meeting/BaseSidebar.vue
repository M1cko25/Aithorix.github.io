<template>
  <div class="fixed right-0 top-0 bottom-0 w-80 bg-gray-800 shadow-lg h-full flex flex-col">
    <div class="p-4 border-b border-gray-700">
      <h2 class="text-xl text-white">{{ title }}</h2>
    </div>
    <div class="flex-1 overflow-y-auto">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps(['title'])
</script>