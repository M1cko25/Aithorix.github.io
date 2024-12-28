<script setup>

defineProps({
    options: {
        type: Array,
        default: []
    },
    style1: {
        type: Boolean,
        default: true
    }
})
</script>
<template>
    <div>
        <select v-if="style1">
            <option v-for="option in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>
        <select v-else class="bg-transparent border-b-2 border-primary p-2 text-dark rounded shadow-lg">
            <option v-for="option in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>
    </div>

</template>