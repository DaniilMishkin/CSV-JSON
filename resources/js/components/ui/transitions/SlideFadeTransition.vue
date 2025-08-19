<template>
    <Transition
        name="collapse"
        @enter="enter"
        @after-enter="afterEnter"
        @leave="leave"
    >
        <slot />
    </Transition>
</template>

<script setup>
const enter = element => {
    element.style.width = 'auto'
    const width = getComputedStyle(element).width
    element.style.width = 0

    getComputedStyle(element)

    setTimeout(() => {
        element.style.width = width
    })
}

const afterEnter = element => {
    element.style.width = 'auto'
}

const leave = element => {
    element.style.width = getComputedStyle(element).width

    getComputedStyle(element)

    setTimeout(() => {
        element.style.width = 0
    })
}
</script>

<style scoped lang="sass">
.collapse-enter-active, .collapse-leave-active
  transition: width 0.1s ease
  overflow: hidden
</style>
