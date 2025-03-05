<template>
  <button class="rounded-full p-3 text-white transition-colors"
          :class="[
            color === 'red' ? 'bg-red-500 hover:bg-red-600' :
            color === 'blue' ? 'bg-blue-500 hover:bg-blue-600' :
            color === 'green' ? 'bg-green-500 hover:bg-green-600' :
            active ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-700 hover:bg-gray-600'
          ]">
    <slot />
  </button>
</template>

<script setup>
defineProps({
  active: Boolean,
  color: String
})
</script>