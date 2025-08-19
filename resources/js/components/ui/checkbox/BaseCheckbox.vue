<template>
    <div class="base-checkbox">
        <input
            :id="uuid"
            v-model="modelValue"
            class="base-checkbox__input"
            type="checkbox"
            :disabled="disabled"
        />
        <label :for="uuid" class="base-checkbox__label">
            <span>
                <svg width="12px" height="10px">
                    <use xlink:href="#check-4" />
                </svg>
            </span>
        </label>
        <svg class="base-checkbox__icon">
            <symbol id="check-4" viewBox="0 0 12 10">
                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
            </symbol>
        </svg>
    </div>
</template>

<script setup>
import { ref } from 'vue'

defineEmits(['update:modelValue'])

defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
})

const modelValue = defineModel('modelValue', { type: Boolean, default: false })

const getUUID = () => {
    return (~~(Math.random() * 1e8)).toString(16)
}

const uuid = ref('checkbox-' + getUUID())
</script>

<style scoped lang="sass">
$border-color: #5C5858
$check-color: white
$active-color: #11263B

.base-checkbox
  width: 20px
  height: 20px

.base-checkbox *
  box-sizing: border-box

.base-checkbox .base-checkbox__label
  -webkit-user-select: none
  user-select: none
  cursor: pointer
  border-radius: 6px
  overflow: hidden
  transition: all 0.2s ease
  display: inline-block

.base-checkbox .base-checkbox__label span
  float: left
  vertical-align: middle
  transform: translate3d(0, 0, 0)

.base-checkbox .base-checkbox__label span:first-child
  position: relative
  width: 20px
  height: 20px
  border-radius: 4px
  transform: scale(1)
  border: 2px solid $border-color
  transition: all 0.2s ease

.base-checkbox .base-checkbox__label span:first-child svg
  position: absolute
  top: 4px
  left: 3px
  fill: none
  stroke: $check-color
  stroke-width: 2
  stroke-linecap: round
  stroke-linejoin: round
  stroke-dasharray: 16px
  stroke-dashoffset: 16px
  transition: all 0.3s ease
  transition-delay: 0.1s
  transform: translate3d(0, 0, 0)

.base-checkbox .base-checkbox__label span:last-child
  padding-left: 8px
  line-height: 18px

.base-checkbox .base-checkbox__label:hover span:first-child
  border-color: $active-color

.base-checkbox .base-checkbox__input
  position: absolute
  visibility: hidden

.base-checkbox .base-checkbox__input:checked + .base-checkbox__label span:first-child
  background: $active-color
  border-color: $active-color
  animation: wave-4 0.4s ease

.base-checkbox .base-checkbox__input:checked + .base-checkbox__label span:first-child svg
  stroke-dashoffset: 0

.base-checkbox .base-checkbox__icon
  position: absolute
  width: 0
  height: 0
  pointer-events: none
  user-select: none

@-moz-keyframes wave-4
  50%
    transform: scale(0.9)

@-webkit-keyframes wave-4
  50%
    transform: scale(0.9)

@-o-keyframes wave-4
  50%
    transform: scale(0.9)

@keyframes wave-4
  50%
    transform: scale(0.9)
</style>
