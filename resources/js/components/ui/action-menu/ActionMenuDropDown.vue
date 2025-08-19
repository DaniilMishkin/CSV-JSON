<template>
    <div
        v-click-away="onClickAway"
        class="action-menu-drop-down"
        @touchstart.stop
    >
        <div
            v-for="option in menuOptions"
            :key="option.label"
            class="action-menu-drop-down__option"
            @click="emit('selectMethod', option.methodName)"
        >
            <SvgIcon v-if="option.icon" :path="option.icon" type="mdi" />
            {{ option.label }}
        </div>
    </div>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { directive as vClickAway } from 'vue3-click-away'

const emit = defineEmits(['selectMethod', 'closeMenu'])

defineProps({
    /**
     *  Array of options objects.
     *  Object must contain at least 'label' field.
     *  'label' can also be locale message key.
     */
    menuOptions: {
        type: Array,
        default: () => [],
    },
})

const onClickAway = () => emit('closeMenu')
</script>

<style scoped lang="sass">
$drop-down-bg-color: white
$drop-down-shadow: 0 17px 30px 0 rgba(0, 0, 0, 0.10)
$option-color: #1D1B20
$option-hover-color: #212529
$option-hover-bg-color: #F1E7D6

.action-menu-drop-down
  position: absolute
  z-index: 10
  display: flex
  flex-direction: column
  align-items: center
  justify-content: flex-start
  min-width: 233px
  width: fit-content
  padding: 0.5rem 0
  border-radius: 8px
  background: $drop-down-bg-color
  box-shadow: $drop-down-shadow

.action-menu-drop-down__option
  cursor: pointer
  width: 100%
  display: flex
  gap: 16px
  align-items: center
  justify-content: start
  text-align: start
  padding: 12px 16px
  color: $option-color
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px
  &:hover
    color: $option-hover-color
    background: $option-hover-bg-color
    cursor: pointer
</style>
