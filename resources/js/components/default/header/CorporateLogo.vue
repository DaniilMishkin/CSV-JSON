<template>
    <a :href="route(RouteNamesVocabulary.uploads.page.list)">
        <div class="text-2xl text-white">CSV-to-JSON</div>
    </a>
</template>

<script setup>
import { RouteNamesVocabulary } from '../../../helpers/route-names-vocabulary.js'
import { route } from 'ziggy-js'
</script>
