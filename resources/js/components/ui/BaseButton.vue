<template>
    <button
        class="base-button"
        :class="buttonClasses"
        :disabled="isDisabled"
        @click="$emit('click')"
    >
        <InfiniteLoadingCircle v-if="isLoading" class="base-button__label" />
        <template v-else>
            <SvgIcon
                v-if="leftMdiIcon"
                class="base-button__icon"
                type="mdi"
                :path="leftMdiIcon"
            />
            <span v-if="label" class="base-button__label">{{ label }}</span>
            <SvgIcon
                v-if="rightMdiIcon"
                class="base-button__icon"
                type="mdi"
                :path="rightMdiIcon"
            />
        </template>
    </button>
</template>

<script setup>
import { computed, defineAsyncComponent } from 'vue'

const InfiniteLoadingCircle = defineAsyncComponent(
    () => import('@components/ui/InfiniteLoadingCircle.vue'),
)
const SvgIcon = defineAsyncComponent(() => import('@jamescoyle/vue-icon'))

defineEmits(['click'])

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'contained',
        validator: value => ['contained', 'outlined', 'clear'].includes(value),
    },
    leftMdiIcon: {
        type: String,
        default: '',
    },
    rightMdiIcon: {
        type: String,
        default: '',
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
})

const buttonClasses = computed(() => {
    return 'base-button_' + props.type
})

const isDisabled = computed(() => {
    return props.disabled || props.isLoading
})
</script>

<style scoped lang="sass">
$contained-bg: #11263B
$contained-color: #FFF
$contained-bg-disabled: #B3BEBE
$contained-bg-hover: #0A1723
$contained-bg-focus: linear-gradient(180deg, #0A1723 0%, rgba(17, 38, 59, 0.70) 100%)

$outlined-color: #11263B
$outlined-color-disabled: #B3BEBE
$outlined-color-hover: #0A1723
$outlined-color-focus: linear-gradient(180deg, #0A1723 0%, rgba(17, 38, 59, 0.70) 100%)

$clear-color: #11263B
$clear-color-disabled: #B3BEBE
$clear-color-hover: #0A1723
$clear-color-focus: linear-gradient(180deg, #0A1723 0%, rgba(17, 38, 59, 0.70) 100%)

.base-button
  cursor: pointer
  display: flex
  min-height: 46px
  padding: 8px 24px
  justify-content: center
  align-items: center
  gap: 12px
  border-radius: 8px

  &_contained
    background-color: $contained-bg
    .base-button__icon, .base-button__label
      color: $contained-color
    &:hover
      background-color: $contained-bg-hover
    &:focus
      background-color: $contained-bg-focus
    &:disabled
      background-color: $contained-bg-disabled

  &_outlined
    background-color: transparent
    border: 1px solid $outlined-color
    .base-button__icon, .base-button__label
      color: $outlined-color
    &:hover
      border-color: $outlined-color-hover
      .base-button__icon, .base-button__label
        color: $outlined-color-hover
    &:focus
      border-color: $outlined-color-focus
      .base-button__icon, .base-button__label
        color: $outlined-color-focus
    &:disabled
      border-color: $outlined-color-disabled
      .base-button__icon, .base-button__label
        color: $outlined-color-disabled

  &_clear
    .base-button__icon, .base-button__label
      color: $clear-color
    &:hover
      .base-button__icon, .base-button__label
        color: $clear-color-hover
    &:focus
      .base-button__icon, .base-button__label
        color: $clear-color-focus
    &:disabled
      .base-button__icon, .base-button__label
        color: $clear-color-disabled

.base-button__icon
  flex: none

.base-button__label
  text-align: center
  font-size: 16px
  font-style: normal
  font-weight: 500
  line-height: 24px
</style>
