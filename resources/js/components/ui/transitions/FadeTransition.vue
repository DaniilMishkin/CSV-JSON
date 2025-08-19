<template>
    <transition name="fade" v-bind="$attrs">
        <slot />
    </transition>
</template>

<style scoped lang="sass">
.fade-enter-active,
.fade-leave-active
  transition: opacity 0.3s cubic-bezier(0.25, 0.1, 0.25, 1)

.fade-enter-from,
.fade-leave-to
  opacity: 0
</style>
