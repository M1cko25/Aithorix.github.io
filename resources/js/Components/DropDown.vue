<script setup>

defineProps({
    options: {
        type: Array,
        default: []
    },
    style1: {
        type: Boolean,
        default: false
    },
    oneValue: {
        type: Boolean,
        default: false
    },
    value: {
        type: String,
        default: ''
    },
    modelValue: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['select', 'update:modelValue'])
const selectOption = (event) => {
    emit('select', event.target.value)
    emit('update:modelValue', event.target.value)
}
</script>
<template>
    <div>
        <select v-if="style1" @change="selectOption($event)" :value="modelValue" class="rounded-md bg-transparent hover:bg-light-gray md:p-2 outline-none" >
            <option v-for="option in options" :key="option" :value="option" >
                {{ option }}
            </option>
        </select>
        <select v-else-if="oneValue" class="rounded-md bg-transparent hover:bg-light-gray p-2 outline-none" disabled>
            <option>
                {{ value }}
            </option>
        </select>
        <select v-else class="bg-transparent border-b-2 border-primary p-2 text-dark rounded shadow-lg">
            <option v-for="(option, index) in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>
    </div>
</template>