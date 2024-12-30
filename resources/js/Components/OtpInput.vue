<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    required: true,
    validator: (value) => value.length === 4
  }
})

const emit = defineEmits(['update:modelValue'])

const inputs = ref([])
const inputRefs = ref([])

// Initialize input refs
for (let i = 0; i < 4; i++) {
  inputRefs.value[i] = ref(null)
}

watch(() => props.modelValue, (newValue) => {
  inputs.value = [...newValue]
}, { immediate: true })

const handleInput = (index, event) => {
  const value = event.target.value
  const newInputs = [...inputs.value]
  
  // Only take the last character if multiple characters are pasted
  newInputs[index] = value.slice(-1)
  
  // Move to next input if value is entered
  if (value && index < 3) {
    inputRefs.value[index + 1].focus()
  }
  
  emit('update:modelValue', newInputs)
}

const handleKeydown = (index, event) => {
  // Move to previous input on backspace if current input is empty
  if (event.key === 'Backspace' && !inputs.value[index] && index > 0) {
    inputRefs.value[index - 1].focus()
  }
}

const handlePaste = (event) => {
  event.preventDefault()
  const paste = event.clipboardData.getData('text')
  const numbers = paste.match(/\d/g)
  
  if (numbers) {
    const newInputs = [...inputs.value]
    for (let i = 0; i < Math.min(numbers.length, 4); i++) {
      newInputs[i] = numbers[i]
    }
    emit('update:modelValue', newInputs)
  }
}
</script>

<template>
    <div class="flex justify-center gap-4">
        <template v-for="(digit, index) in 4" :key="index">
            <input
                type="text"
                :value="inputs[index]"
                @input="(e) => handleInput(index, e)"
                @keydown="(e) => handleKeydown(index, e)"
                @paste="handlePaste"
                class="w-14 h-14 text-center text-2xl font-bold border-2 border-dark rounded-lg focus:border-primary focus:outline-none"
                maxlength="1"
                inputmode="numeric"
                pattern="[0-9]*"
                :ref="el => inputRefs[index] = el"
            />
        </template>
    </div>
</template>