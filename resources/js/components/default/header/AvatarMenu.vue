<template>
    <div v-if="currentUser" class="relative">
        <div
            class="text-white text-2xl cursor-pointer"
            @click="isMenuVisible = true"
        >
            {{ currentUser.name }}
        </div>

        <ActionMenuDropDown
            v-if="isMenuVisible"
            class="top-[calc(100%+12px)] right-0"
            :menu-options="AvatarMenuOptions"
            @select-method="handleSelectMethod"
            @close-menu="isMenuVisible = false"
        />
    </div>
</template>

<script setup>
import ActionMenuDropDown from '@components/ui/action-menu/ActionMenuDropDown.vue'
import { RouteNamesVocabulary } from '../../../helpers/route-names-vocabulary.js'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route } from 'ziggy-js'
import { AvatarMenuOptions } from './config/avatar-menu-options.js'

const currentUser = computed(() => usePage().props?.auth?.user)

const isMenuVisible = ref(false)

const handleSelectMethod = methodName => methods[methodName]()

const methods = {
    storeFile() {
        router.visit(route(RouteNamesVocabulary.uploads.page.create))
    },
    logout() {
        useForm().post(route(RouteNamesVocabulary.auth.logout))
    },
}
</script>
