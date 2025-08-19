<template>
    <transition
        name="collapse"
        @enter="enter"
        @after-enter="afterEnter"
        @leave="leave"
    >
        <slot />
    </transition>
</template>

<script setup>
const enter = element => {
    element.style.height = 'auto'
    const height = getComputedStyle(element).height
    element.style.height = 0

    getComputedStyle(element)

    setTimeout(() => {
        element.style.height = height
    })
}

const afterEnter = element => {
    element.style.height = 'auto'
}

const leave = element => {
    element.style.height = getComputedStyle(element).height

    getComputedStyle(element)

    setTimeout(() => {
        element.style.height = 0
    })
}
</script>

<style scoped lang="sass">
.collapse-enter-active, .collapse-leave-active
  transition: height 0.15s ease
  overflow: hidden
</style>
