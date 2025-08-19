<template>
    <div :class="inputClasses">
        <div v-if="label" class="base-input__label">{{ label }}</div>

        <InputTypePassword
            v-if="type === 'password'"
            :model-value="modelValue"
            :uniq-id="uniqId"
            :placeholder="placeholder"
            :disabled="disabled"
            @update:model-value="value => $emit('update:modelValue', value)"
        />
        <label v-else class="base-input__input-wrapper" :for="uniqId">
            <SvgIcon
                v-if="leftIcon"
                class="input-wrapper__icon"
                type="mdi"
                :path="leftIcon"
                @click="$emit('leftIconClick')"
            />

            <input
                :id="uniqId"
                :value="modelValue"
                class="input-wrapper__input"
                :type="type"
                :placeholder="placeholder"
                :disabled="disabled || readonly"
                @input="$emit('update:modelValue', $event.target.value)"
            />

            <SvgIcon
                v-if="rightIcon"
                class="input-wrapper__icon"
                type="mdi"
                :path="rightIcon"
                :color="rightIconColor"
                @click="$emit('rightIconClick')"
            />
        </label>

        <BaseErrorsBlock v-if="errorExists" :errors="errors" />
    </div>
</template>

<script setup>
import BaseErrorsBlock from '@components/ui/BaseErrorsBlock.vue'
import { computed, defineAsyncComponent, ref } from 'vue'
import SvgIcon from '@jamescoyle/vue-icon'

const InputTypePassword = defineAsyncComponent(
    () => import('./InputTypePassword.vue'),
)

const InputAppearances = {
    Default: 'default',
    Outstanding: 'outstanding',
}

defineEmits(['update:modelValue', 'leftIconClick', 'rightIconClick'])

const getUUID = () => {
    return (~~(Math.random() * 1e8)).toString(16)
}
const uniqId = ref(`input-${getUUID()}`)

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    appearance: {
        type: String,
        default: 'default',
        validator: type => ['default', 'outstanding'].includes(type),
    },
    placeholder: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
    rightIcon: {
        type: String,
        default: '',
    },
    rightIconColor: {
        type: String,
        default: '',
    },
    leftIcon: {
        type: String,
        default: '',
    },
    errors: {
        type: Array,
        default: () => [],
    },
})

const errorExists = computed(() => {
    return props.errors.find(obj => obj !== undefined) ?? false
})

const inputClasses = computed(() => {
    const classes = ['base-input']
    if (props.appearance === InputAppearances.Outstanding) {
        classes.push('base-input_outstanding')
    }
    if (errorExists.value) {
        classes.push('base-input_error')
    }
    if (props.readonly) {
        classes.push('base-input_readonly')
    }
    return classes
})
</script>

<style scoped lang="sass">
$value-color: var(--color-input-value)
$label-color: $value-color
$placeholder-color: var(--color-input-placeholder)

$border-color: var(--color-input-border)
$border-readonly-color: var(--color-input-border-readonly)
$border-focus-color: var(--color-input-border-focus)

$bg-color: transparent
$bg-color-outstanding: #D1AF774D
$bg-disabled-color: var(--color-input-disabled)

$error-color: var(--color-input-error)
$hint-color: var(--color-input-placeholder)

.base-input
  display: flex
  flex-direction: column
  gap: 8px

  &_readonly
    .base-input__input-wrapper
      &:has(input:disabled)
        background-color: $bg-color
        border: 1px solid $border-readonly-color

  &_error
    .base-input__input-wrapper
      border: 1px solid $error-color
    ::v-deep(.input-type-password)
      border: 1px solid $error-color

    .base-input__hint
      color: $error-color

  &_outstanding
    .base-input__input-wrapper
      background-color: $bg-color-outstanding !important
      border: none
      border-bottom: 1px solid $border-color
      border-radius: 4px 4px 0 0
      &:has(input:focus)
        border: none
        border-bottom: 1px solid $border-color

.base-input__label
  width: 100%
  text-align: start
  color: $label-color
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px

.base-input__input-wrapper
  width: 100%
  height: 56px
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

.input-wrapper__input
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
    background-color: transparent

.input-wrapper__icon
  width: 20px
  height: 20px
  flex: none

.base-input__hint
  width: 100%
  text-align: start
  color: $hint-color
  font-size: 12px
  font-style: normal
  font-weight: 400
  line-height: 16px
</style>
