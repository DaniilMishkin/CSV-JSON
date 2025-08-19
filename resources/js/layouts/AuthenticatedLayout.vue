<template>
    <div
        class="h-screen w-full bg-bg-main flex flex-col overflow-y-hidden overflow-x-hidden"
    >
        <Header />

        <div
            class="h-full w-full overflow-y-auto overflow-x-hidden py-8 px-6 desktop:px-10"
        >
            <slot />
        </div>
    </div>
</template>

<script setup>
import Header from '@/components/default/header/Header.vue'
</script>
