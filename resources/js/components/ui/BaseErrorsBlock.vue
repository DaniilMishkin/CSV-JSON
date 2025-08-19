<template>
    <div v-for="message in errors" :key="message" class="base-errors-block">
        {{ message }}
    </div>
</template>

<script setup>
defineProps({
    errors: {
        type: Array,
        default: () => [],
    },
})
</script>

<style scoped lang="sass">
.base-errors-block
  width: 100%
  text-align: start
  color: var(--color-input-error)
  font-size: 12px
  font-style: normal
  font-weight: 400
  line-height: 16px
</style>
