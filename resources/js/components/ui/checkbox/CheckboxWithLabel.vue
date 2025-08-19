<template>
    <div class="checkbox-with-label">
        <BaseCheckbox v-model="modelValue" :disabled="disabled" />
        <span class="checkbox-with-label__label">{{ label }}</span>
    </div>
</template>

<script setup>
import BaseCheckbox from '@components/ui/checkbox/BaseCheckbox.vue'

defineEmits(['update:modelValue'])

defineProps({
    label: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const modelValue = defineModel('modelValue', { type: Boolean, default: false })
</script>

<style scoped lang="sass">
.checkbox-with-label
  display: flex
  gap: 12px
  align-items: center
  justify-content: start

.checkbox-with-label__label
  color: var(--color-text-black)
  font-size: 16px
  font-style: normal
  font-weight: 400
  line-height: 24px
  letter-spacing: 0.5px
</style>
