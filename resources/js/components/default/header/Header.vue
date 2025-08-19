<template>
    <div
        class="w-full h-[88px] flex justify-between items-center px-8 py-4 bg-black"
    >
        <CorporateLogo />

        <div class="flex gap-8 items-center">
            <a :href="route(RouteNamesVocabulary.uploads.page.create)">
                <BaseButton label="UPLOAD FILE" />
            </a>
            <AvatarMenu />
        </div>
    </div>
</template>

<script setup>
import AvatarMenu from '@components/default/header/AvatarMenu.vue'
import CorporateLogo from '@components/default/header/CorporateLogo.vue'
import BaseButton from '../../ui/BaseButton.vue'
import { RouteNamesVocabulary } from '../../../helpers/route-names-vocabulary.js'
import { route } from 'ziggy-js'
</script>
