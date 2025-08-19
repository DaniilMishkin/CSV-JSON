<template>
    <template v-if="!isLoading">
        <SvgIcon
            v-if="isAnyOptionSelected && isOpened && clearable"
            class="select-input__icon"
            :class="{ 'select-input__icon_active': isOpened }"
            type="mdi"
            :path="mdiClose"
            size="22"
            @click="$emit('clear')"
        />
        <SvgIcon
            v-else
            class="select-input__icon"
            :class="{ 'select-input__icon_active': isOpened }"
            type="mdi"
            :path="mdiMenuDown"
            size="22"
        />
    </template>
    <InfiniteLoadingCircle v-else />
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiClose, mdiMenuDown } from '@mdi/js'
import { defineAsyncComponent } from 'vue'

const InfiniteLoadingCircle = defineAsyncComponent(
    () => import('../InfiniteLoadingCircle.vue'),
)

defineEmits(['clear'])

defineProps({
    isOpened: {
        type: Boolean,
        default: false,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    isAnyOptionSelected: {
        type: Boolean,
        default: false,
    },
    clearable: {
        type: Boolean,
        default: false,
    },
})
</script>

<style scoped lang="sass">
.select-input__icon
  flex: none
  transition: all ease 300ms

  &_active
    rotate: 180deg
</style>
