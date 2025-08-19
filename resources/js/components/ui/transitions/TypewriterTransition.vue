<template>
    <Transition name="typewriter" appear>
        <div v-if="isVisible" class="typewriter-wrapper">
            <slot />
        </div>
    </Transition>
</template>

<script setup>
defineProps({
    isVisible: {
        type: Boolean,
        default: true,
    },
})
</script>

<style scoped lang="sass">
.typewriter-wrapper
  display: inline-block
  overflow: hidden
  white-space: nowrap

.typewriter-enter-active
  animation: reveal-left-to-right 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards

.typewriter-leave-active
  animation: hide-right-to-left 0.2s cubic-bezier(0.4, 0, 0.2, 1) forwards

.typewriter-enter-from
  clip-path: inset(0 100% 0 0)

.typewriter-enter-to
  clip-path: inset(0 0 0 0)

.typewriter-leave-from
  clip-path: inset(0 0 0 0)

.typewriter-leave-to
  clip-path: inset(0 100% 0 0)

@keyframes reveal-left-to-right
  from
    clip-path: inset(0 100% 0 0)
  to
    clip-path: inset(0 0 0 0)

@keyframes hide-right-to-left
  from
    clip-path: inset(0 0 0 0)
  to
    clip-path: inset(0 100% 0 0)
</style>
