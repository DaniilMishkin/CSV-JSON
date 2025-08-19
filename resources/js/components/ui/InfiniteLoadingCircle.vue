<template>
    <div class="infinite-loader">
        <div class="infinite-loader__label">{{ label }}</div>
        <div class="infinite-loader__loader">
            <div class="loader__inner-shadow" />
            <div class="loader__rotator" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: '',
    },
})
</script>

<style scoped lang="sass">
$label-color: #5C5858
$loader-shadow: 0 1px 4px #11263B
$loader-inner-shadow: inset 0 1px 4px #11263B
$loader-bg: radial-gradient(50% 50% at 50% 50%, #FFFFFF 0%, #FFFFFF 83.25%, #E0E0E0 100%)
$rotator-color: #11263B

.infinite-loader
  display: flex
  flex-direction: column
  gap: 8px
  justify-content: center
  align-items: center

.infinite-loader__label
  color: $label-color
  font-size: 14px
  font-style: normal
  font-weight: 500
  line-height: 20px

.infinite-loader__loader
  position: relative
  width: 22px
  height: 22px
  border-radius: 50%
  box-shadow: 0 1px 4px $loader-shadow
  background: $loader-bg

.loader__inner-shadow
  position: absolute
  top: 3px
  left: 3px
  box-sizing: content-box
  width: 16px
  height: 16px
  border-radius: 50%
  box-shadow: inset 0 1px 4px $loader-inner-shadow

.loader__rotator
  box-sizing: border-box
  width: 22px
  height: 22px
  border-radius: 50%
  border: 4px solid transparent
  border-top: 4px solid $rotator-color
  -webkit-animation: spin 1.5s forwards infinite
  animation: spin 1.5s forwards infinite

@-webkit-keyframes spin
  0%
    -webkit-transform: rotate(0deg)
  100%
    -webkit-transform: rotate(360deg)

@keyframes spin
  0%
    transform: rotate(0deg)
  100%
    transform: rotate(360deg)
</style>
