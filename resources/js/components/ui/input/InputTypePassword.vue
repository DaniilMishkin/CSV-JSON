<template>
    <label class="input-type-password" :for="uniqId">
        <input
            :id="uniqId"
            :value="modelValue"
            class="input-type-password__input"
            :type="isPasswordVisible ? 'text' : 'password'"
            :placeholder="placeholder"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <SvgIcon
            class="input-type-password__icon"
            type="mdi"
            :path="isPasswordVisible ? mdiEyeOffOutline : mdiEyeOutline"
            :color="modelValue ? '' : '#919191'"
            @click="togglePasswordVisibility"
        />
    </label>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiEyeOffOutline, mdiEyeOutline } from '@mdi/js'
import { ref } from 'vue'

defineEmits(['update:modelValue'])

defineProps({
    modelValue: {
        type: [null, String, Number],
        default: '',
    },
    uniqId: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
})

const isPasswordVisible = ref(false)

const togglePasswordVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value
}
</script>

<style scoped lang="sass">
$value-color: var(--color-input-value)
$label-color: $value-color
$placeholder-color: var(--color-input-placeholder)

$border-color: var(--color-input-border)
$border-focus-color: var(--color-input-border-focus)

$bg-color: transparent
$bg-disabled-color: var(--color-input-disabled)

.input-type-password
  width: 100%
  padding: 16px 24px
  display: flex
  gap: 10px
  border: 1px solid $border-color
  border-radius: 8px
  background-color: $bg-color

  &:has(input:focus)
    border: 1px solid $border-focus-color
  &:has(input:disabled)
    background-color: $bg-disabled-color

.input-type-password__input
  width: 100%
  border: none
  outline: none
  overflow: hidden
  color: $value-color
  text-overflow: ellipsis
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px

  &::placeholder
    color: $placeholder-color
  &:disabled
    background-color: $bg-disabled-color

.input-type-password__icon
  cursor: pointer
  width: 20px
  height: 20px
  flex: none
</style>
