<template>
    <div
        ref="baseSelectRef"
        v-click-away="closeDropDown"
        :class="baseSelectClasses"
    >
        <div v-if="label" class="base-select__label">
            {{ label }}
        </div>

        <div
            :class="selectInputClasses"
            @click.capture="openDropDown"
            @pointerdown="openDropDown"
        >
            <div
                v-if="taggable && proxiedModelValue.length"
                class="select-input__value select-input__value__taggable"
            >
                <div class="flex flex-wrap gap-2 h-full">
                    <BaseSelectTag
                        v-for="item in proxiedModelValue"
                        :key="item[trackBy]"
                        :item="item"
                        :label-field="labelField"
                        @remove="handleRemoveTag"
                    />
                </div>
            </div>
            <div v-else class="w-full flex gap-2">
                <template v-if="searchable && isOpened">
                    <SvgIcon type="mdi" :path="mdiMagnify" />
                    <input
                        v-model="searchQuery"
                        class="select-input__value"
                        :placeholder="placeholder"
                        type="text"
                        @input="handleInputQuery"
                    />
                </template>

                <div v-else class="select-input__value">
                    {{ value }}
                </div>
            </div>

            <BaseSelectControls
                v-if="!readonly"
                :is-opened="isOpened"
                :is-loading="isLoading"
                :is-any-option-selected="isAnyOptionSelected"
                :clearable="clearable"
                @clear="handleClearSelect"
            />
        </div>

        <MultiselectDropDown
            v-if="multiple"
            v-model="proxiedModelValue"
            :is-opened="isOpened"
            :search-query="searchQuery"
            :options="options"
            :async-options="asyncOptions"
            :track-by="trackBy"
            :label-field="labelField"
            :clearable="clearable"
            :pagination="pagination"
            :drop-down-max-height="dropDownMaxHeight"
            :drop-down-position="dropDownPosition"
            :teleport-ref="teleport ? baseSelectRef : null"
        />

        <SelectDropDown
            v-else
            v-model="proxiedModelValue"
            :is-opened="isOpened"
            :search-query="searchQuery"
            :options="options"
            :async-options="asyncOptions"
            :pagination="pagination"
            :track-by="trackBy"
            :label-field="labelField"
            :clearable="clearable"
            :drop-down-max-height="dropDownMaxHeight"
            :drop-down-position="dropDownPosition"
            :teleport-ref="teleport ? baseSelectRef : null"
        >
            <template v-if="$slots.option" #option="{ option }">
                <slot name="option" v-bind="option" />
            </template>
        </SelectDropDown>

        <BaseErrorsBlock v-if="errorExists" :errors="errors" />
    </div>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { computed, defineAsyncComponent, ref, watch } from 'vue'
import { directive as vClickAway } from 'vue3-click-away'
import { mdiMagnify } from '@mdi/js'

const MultiselectDropDown = defineAsyncComponent(
    () => import('./MultiselectDropDown.vue'),
)
const SelectDropDown = defineAsyncComponent(
    () => import('./SelectDropDown.vue'),
)
const BaseErrorsBlock = defineAsyncComponent(
    () => import('../BaseErrorsBlock.vue'),
)
const BaseSelectControls = defineAsyncComponent(
    () => import('./BaseSelectControls.vue'),
)
const BaseSelectTag = defineAsyncComponent(() => import('./BaseSelectTag.vue'))

const emits = defineEmits(['update:modelValue', 'load'])

const props = defineProps({
    modelValue: {
        type: [null, Number, String, Object, Array],
        required: true,
    },
    /**
     * Array of options
     * Add isBold field to option Object to make label bold
     */
    options: {
        type: Array,
        default: () => [],
        // required: true,
    },
    asyncOptions: {
        type: [Function, null],
        default: null,
    },
    multiple: {
        /** in case if multiple === true then typeof modelValue should be Array */
        type: Boolean,
        default: false,
    },
    trackBy: {
        /**
         * A key for tracking unique values within `options`,
         * used if the elements in the array are objects.
         * Type: String, optional.
         * Specify a unique key if `options` contains objects!
         */
        type: String,
        default: '',
    },
    labelField: {
        /**
         * Specifies the field or function used to retrieve the label for each item in `options`.
         * If a string, it represents the key name within each option object.
         * If a function, it should return a label for each option.
         * Type: String or Function, optional.
         */
        type: [String, Function],
        default: '',
    },
    returnObject: {
        type: Boolean,
        default: true,
    },
    placeholder: {
        type: String,
        default: 'Select option',
    },
    label: {
        type: String,
        default: '',
    },
    clearable: {
        type: Boolean,
        default: true,
    },
    searchable: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    closeAfterSelect: {
        type: Boolean,
        default: true,
    },
    errors: {
        type: Array,
        default: () => [],
    },
    dropDownMaxHeight: {
        type: Number,
        default: 350,
    },
    dropDownPosition: {
        type: String,
        /** Available values: bottom, top  */
        default: 'bottom',
    },
    pagination: {
        type: [Boolean, Number],
        default: false,
    },
    teleport: {
        type: Boolean,
        default: false,
    },
    taggable: {
        type: Boolean,
        default: false,
    },
})

const baseSelectRef = ref(null)

const proxiedModelValue = ref(
    props.returnObject
        ? props.modelValue
        : props.options.find(obj => obj[props.trackBy] === props.modelValue),
)

const searchQuery = ref(null)
const handleInputQuery = event => (searchQuery.value = event.target.value)

watch(
    () => props.modelValue,
    val => {
        proxiedModelValue.value = props.returnObject
            ? val
            : props.options.find(obj => obj[props.trackBy] === val)
    },
    { deep: true },
)

watch(
    () => proxiedModelValue.value,
    val => {
        const newVal = props.returnObject
            ? val
            : val && typeof val === 'object'
              ? val[props.trackBy]
              : val

        emits('update:modelValue', newVal)
        if (props.closeAfterSelect && isOpened.value) closeDropDown()
    },
    { deep: true },
)

const isOpened = ref(false)

const isDisabled = computed(
    () =>
        props.disabled ||
        props.isLoading ||
        (!props.options.length && !props.asyncOptions) ||
        props.readonly,
)

const openDropDown = () => (isOpened.value = true)
const closeDropDown = () => {
    isOpened.value = false
    searchQuery.value = null
}

const labelField = computed(() => props.labelField || props.trackBy)
const isLabelFieldAFunction = computed(
    () => typeof labelField.value === typeof Function,
)

const value = computed(() => {
    if (props.multiple) {
        return proxiedModelValue.value.length
            ? isLabelFieldAFunction.value
                ? [...proxiedModelValue.value]
                      .map(item => labelField.value(item))
                      .join(', ')
                : [...proxiedModelValue.value]
                      .map(item => item[labelField.value])
                      .join(', ')
            : props.placeholder
    }

    return proxiedModelValue.value
        ? labelField.value
            ? isLabelFieldAFunction.value
                ? labelField.value(proxiedModelValue.value)
                : proxiedModelValue.value[labelField.value]
            : proxiedModelValue.value
        : props.placeholder
})

const isAnyOptionSelected = computed(() => value.value !== props.placeholder)

const errorExists = computed(() => {
    return props.errors.find(obj => obj !== undefined) ?? false
})

const baseSelectClasses = computed(() => {
    const classes = ['base-select']
    props.readonly && classes.push('base-select_readonly')
    errorExists.value && classes.push('base-select_error')

    return classes
})

const selectInputClasses = computed(() => {
    const classes = ['base-select__select-input']
    isDisabled.value && classes.push('base-select__select-input_disabled')
    isAnyOptionSelected.value &&
        classes.push('base-select__select-input_selected')
    isOpened.value && classes.push('base-select__select-input_opened')

    return classes
})

watch(
    () => value.value,
    val => {
        if (props.searchable) {
            searchQuery.value = val !== props.placeholder ? val : null
        }
    },
    { deep: true },
)

const handleClearSelect = () => {
    proxiedModelValue.value = props.multiple ? [] : null
    emits('update:modelValue', proxiedModelValue.value)
}

const handleRemoveTag = item => {
    proxiedModelValue.value = proxiedModelValue.value.filter(tag => {
        return tag && tag[props.trackBy] !== item[props.trackBy]
    })

    emits('update:modelValue', proxiedModelValue.value)
}
</script>

<style scoped lang="sass">
$value-color: var(--color-input-value)
$label-color: $value-color
$placeholder-color: var(--color-input-placeholder)

$border-color: var(--color-input-border)
$border-readonly-color: var(--color-input-border-readonly)
$border-focus-color: var(--color-input-border-focus)

$bg-color: transparent
$bg-disabled-color: var(--color-input-disabled)

$error-color: var(--color-input-error)

$option-hover-bg-color: #566A60
$selected-option-bg-color: #566A60
$selected-option-hover-bg-color: #3C4A43

.base-select
  position: relative
  display: flex
  flex-direction: column
  align-items: center
  justify-content: center
  gap: 8px

  &_readonly
    .base-select__select-input_disabled
      background-color: $bg-color
      border: 1px solid $border-readonly-color

  &_error
    .base-select__select-input
      border: 1px solid $error-color

.base-select__label
  width: 100%
  text-align: start
  color: $label-color
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px

.base-select__select-input
  width: 100%
  cursor: pointer
  display: flex
  height: 56px
  padding: 16px 24px
  align-items: center
  gap: 10px
  border-radius: 8px
  border: 1px solid $border-color
  background: $bg-color

  &_disabled
    background-color: $bg-disabled-color
    pointer-events: none
    cursor: default

  &_selected
    .select-input__value
      color: $value-color

  &_opened
    border: 1px solid $border-focus-color

.select-input__value
  overflow: hidden
  width: calc(100% - 32px)
  white-space: nowrap
  color: $placeholder-color
  text-overflow: ellipsis
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px

input.select-input__value
  border: none !important
  display: inline-block
  overflow: hidden
  width: calc(100% - 32px)
  white-space: nowrap
  color: $placeholder-color
  text-overflow: ellipsis
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px
  &:focus
    border: none
    outline: none
</style>
