<template>
    <Teleport to="body" :disabled="!teleportRef">
        <FadeTransition>
            <div
                v-if="isOpened"
                ref="selectDropDown"
                :class="['multiselect-drop-down', dropDownPositionClass]"
                :style="{
                    maxHeight: dropDownMaxHeight + 'px',
                    ...dropdownStyle,
                }"
            >
                <div
                    v-for="option in filteredOptions"
                    :key="trackBy ? option[trackBy] : option"
                    class="multiselect-drop-down__option-item"
                    @click.prevent="handleSelectOption(option)"
                >
                    <BaseCheckbox :model-value="getIfOptionSelected(option)" />
                    <div
                        :class="[
                            'option-item__label',
                            { 'option-item__label_bold': option.isBold },
                        ]"
                    >
                        {{ getLabelField(option) }}
                    </div>
                </div>

                <InfiniteLoading
                    v-if="!!asyncOptions"
                    target="multiselect-drop-down"
                    :identifier="infiniteIdentifier"
                    @infinite="loadOptions"
                >
                    <template #complete>&nbsp;</template>
                    <template #spinner><InfiniteLoadingCircle /></template>
                </InfiniteLoading>
            </div>
        </FadeTransition>
    </Teleport>
</template>

<script setup>
import InfiniteLoadingCircle from '../InfiniteLoadingCircle.vue'
import FadeTransition from '../transitions/FadeTransition.vue'
import BaseCheckbox from '../checkbox/BaseCheckbox.vue'
import InfiniteLoading from 'v3-infinite-loading'
import { computed, ref, watch } from 'vue'
import { debounce } from 'lodash'

const emits = defineEmits(['update:modelValue'])

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    isOpened: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        default: () => [],
        // required: true,
    },
    asyncOptions: {
        type: [Function, null],
        default: null,
    },
    searchQuery: {
        type: String,
        default: '',
    },
    trackBy: {
        type: String,
        required: true,
    },
    labelField: {
        type: [String, Function],
        default: '',
    },
    clearable: {
        type: Boolean,
        default: true,
    },
    dropDownMaxHeight: {
        type: Number,
        default: 350,
    },
    dropDownPosition: {
        type: String,
        default: 'bottom',
    },
    pagination: {
        type: [Boolean, Number],
        default: false,
    },
    teleportRef: {
        type: String,
        default: '',
    },
})

const selectDropDown = ref(null)
const infiniteIdentifier = ref(+new Date())

const dropdownStyle = computed(() => {
    if (!props.teleportRef) return {}

    const rect = props.teleportRef.getBoundingClientRect()
    return {
        top: `${rect.bottom + window.scrollY}px`,
        left: `${rect.left + window.scrollX}px`,
        width: `${rect.width}px`,
        zIndex: 1000,
    }
})

const dropDownPositionClass = computed(() => {
    switch (props.dropDownPosition) {
        case 'top':
            return 'select-drop-down_top'
        case 'bottom':
            return 'select-drop-down_bottom'
        default:
            return ''
    }
})

const labelField = computed(() => props.labelField || props.trackBy)
const isLabelFieldAFunction = computed(
    () => typeof labelField.value === typeof Function,
)

const filteredOptions = computed(() => {
    if (props.asyncOptions) {
        return loadedOptions.value
    }

    return props.options.filter(option => {
        if (labelField.value) {
            return String(
                isLabelFieldAFunction.value
                    ? labelField.value(option)
                    : option[labelField.value],
            )
                .toLowerCase()
                .includes(String(props.searchQuery ?? '').toLowerCase())
        }

        return String(option).includes(
            String(props.searchQuery ?? '').toLowerCase(),
        )
    })
})

const getLabelField = option =>
    labelField.value
        ? isLabelFieldAFunction.value
            ? labelField.value(option)
            : option[labelField.value]
        : option

const getIfOptionSelected = option => {
    return !!props.modelValue.find(
        item => item[props.trackBy] === option[props.trackBy],
    )
}

const handleSelectOption = option => {
    const optionExists =
        props.modelValue.length &&
        props.modelValue.some(
            item => item[props.trackBy] === option[props.trackBy],
        )

    if (optionExists && props.modelValue.length === 1 && !props.clearable) {
        return
    }

    const uniqueArray = optionExists
        ? [
              ...props.modelValue.filter(
                  item => item[props.trackBy] !== option[props.trackBy],
              ),
          ]
        : [...props.modelValue, option]

    emits('update:modelValue', uniqueArray)
}

const loadedOptions = ref([])
const currentPage = ref(1)

const loadOptions = debounce(async $state => {
    try {
        if (!props.pagination) await handleLoad($state)
        else await handlePaginatedLoad($state)
    } catch {
        $state.error()
    }
}, 300)

const handlePaginatedLoad = async $state => {
    const queryAtRequest = props.searchQuery
    try {
        const resp = await props.asyncOptions(queryAtRequest, currentPage.value)
        if (queryAtRequest === props.searchQuery) {
            const data = resp.data
            const meta = resp.meta
            loadedOptions.value.push(...data)
            if (currentPage.value >= meta.last_page) {
                $state.complete()
            } else {
                $state.loaded()
            }
            currentPage.value++
        }
    } catch {
        $state.error()
    }
}

const handleLoad = async $state => {
    const queryAtRequest = props.searchQuery
    try {
        const resp = await props.asyncOptions(queryAtRequest, currentPage.value)
        if (queryAtRequest === props.searchQuery) {
            loadedOptions.value.push(...resp)
            $state.complete()
        }
    } catch {
        $state.error()
    }
}

watch(
    () => props.searchQuery,
    () => {
        if (props.asyncOptions) {
            loadedOptions.value = []
            currentPage.value = 1
            infiniteIdentifier.value++
        }
    },
)
</script>

<style scoped lang="sass">
$drop-down-bg-color: white
$option-hover-color: #1C1F22
$option-bg-color: white
$option-hover-bg-color: #F1E7D6
$selected-option-bg-color: #F1E7D6
$selected-option-hover-bg-color: #F1E0C4

.multiselect-drop-down
  z-index: 10
  position: absolute
  top: calc(100% + 8px)
  left: 0
  width: 100%
  max-height: 350px
  overflow-y: auto
  display: flex
  flex-direction: column
  padding: 16px
  gap: 10px
  border-radius: 8px
  background: $drop-down-bg-color
  box-shadow: 0 17px 30px 0 rgba(0, 0, 0, 0.10)

.multiselect-drop-down__option-item
  width: 100%
  cursor: pointer
  padding: 16px
  border-radius: 8px
  background-color: $option-bg-color
  display: flex
  gap: 12px
  align-items: center
  justify-content: start

  &:hover
    background-color: $option-hover-bg-color

    .option-item__label
      color: $option-hover-color

.option-item__label
  width: calc(100% - 12px)
  text-align: start
  text-overflow: ellipsis
  color: var(--color-input-value)
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px
  &_bold
    font-weight: 600
</style>
