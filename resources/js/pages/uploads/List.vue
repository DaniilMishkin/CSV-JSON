<template>
    <AuthenticatedLayout>
        <div class="w-full flex flex-col gap-8">
            <UploadsListTable
                v-model:server-options="serverOptions"
                :items="items"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '../../layouts/AuthenticatedLayout.vue'
import UploadsListTable from './components/UploadsListTable.vue'
import { RouteNamesVocabulary } from '../../helpers/route-names-vocabulary.js'
import { fillFormFromRouteQuery } from '../../helpers/fill-form-from-route-query.js'
import { tidyEmptyObjectFields } from '../../helpers/tidy-empty-object-fields.js'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ref, watch } from 'vue'

const DEFAULT_PER_PAGE = 10

defineProps({
    items: {
        type: Object,
        default: () => ({}),
    },
})

const searchParams = ref({
    page: 1,
    perPage: DEFAULT_PER_PAGE,
})

fillFormFromRouteQuery(searchParams.value)

const serverOptions = ref({
    page: searchParams.value.page,
    rowsPerPage: searchParams.value.perPage,
})

watch(
    () => serverOptions.value,
    val => {
        searchParams.value.page = val.page
        searchParams.value.perPage = val.rowsPerPage
        handleFilter(false)
    },
    {
        deep: true,
    },
)

const handleFilter = (preserveState = true) => {
    useForm(tidyEmptyObjectFields(searchParams.value)).get(
        route(RouteNamesVocabulary.uploads.page.list),
        {
            replace: true,
            preserveState,
            preserveScroll: true,
            ...(preserveState ? { only: ['items'] } : {}),
        },
    )
}
</script>
