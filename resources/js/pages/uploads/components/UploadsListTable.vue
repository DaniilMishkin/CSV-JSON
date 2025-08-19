<template>
    <BaseTable
        v-model:server-options="serverOptions"
        :server-items-length="tableDataTotalLength"
        :items="tableData"
        :headers="UploadsListTableHeaders"
    >
        <template #item-action="{ item }">
            <a
                :href="
                    route(RouteNamesVocabulary.uploads.ajax.download, {
                        upload: item.id,
                    })
                "
                class="download"
            >
                <BaseButton
                    label="DOWNLOAD"
                    :disabled="item.status.id !== 'done'"
                    @click="handleDownload(item)"
                />
            </a>
        </template>
    </BaseTable>
</template>

<script setup>
import BaseTable from '@components/ui/table/BaseTable.vue'
import { UploadsListTableHeaders } from '../config/uploads-list-table-headers.js'
import { computed } from 'vue'
import BaseButton from '../../../components/ui/BaseButton.vue'
import { RouteNamesVocabulary } from '../../../helpers/route-names-vocabulary.js'

defineEmits(['update:serverOptions'])

const props = defineProps({
    items: {
        type: Object,
        default: () => ({}),
    },
    mainTab: {
        type: Object,
        default: () => ({}),
    },
})

const serverOptions = defineModel('serverOptions', {
    type: Object,
    default: () => ({}),
})

const tableData = computed(() => props.items?.data)
const tableDataTotalLength = computed(() => props.items?.meta?.total)
</script>
