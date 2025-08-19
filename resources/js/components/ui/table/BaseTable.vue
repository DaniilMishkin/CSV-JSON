<template>
    <EasyDataTable
        ref="baseTable"
        v-bind="tableProps"
        v-model:items-selected="itemsSelectedComputed"
        class="w-full"
        v-on="dataTableListeners"
    >
        <template
            v-for="header in headers"
            #[`item-${header.value}`]="item"
            :key="header.value"
        >
            <template v-if="checkIfSlotNeeded(`item-${header.value}`)">
                <slot :name="`item-${header.value}`" :item="item" />
            </template>

            <div v-else :class="item">
                {{ getRightItemValue(item, header.value) }}
            </div>
        </template>

        <template v-if="checkIfSlotNeeded('expand')" #expand="item">
            <slot name="expand" :item="item" />
        </template>

        <template #loading>
            <InfiniteLoadingCircle label="Loading" />
        </template>

        <template #empty-message>
            <div>No data</div>
        </template>

        <template #pagination="{ prevPage, nextPage, isFirstPage, isLastPage }">
            <TablePagination
                :total="serverItemsLength"
                :current-page="currentPage"
                :last-page="lastPage"
                :is-last-page="isLastPage"
                :is-first-page="isFirstPage"
                @select-page="args => baseTable.updatePage(args)"
                @go-next="nextPage"
                @go-prev="prevPage"
            />
        </template>
    </EasyDataTable>
</template>

<script setup>
import InfiniteLoadingCircle from '../InfiniteLoadingCircle.vue'
import TablePagination from './TablePagination.vue'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import {
    computed,
    onBeforeUnmount,
    onMounted,
    reactive,
    ref,
    toRefs,
    useSlots,
} from 'vue'

const baseTable = ref()

const lastPage = computed(() => baseTable.value?.maxPaginationNumber ?? 1)
const currentPage = computed(
    () => baseTable.value?.currentPaginationNumber ?? 0,
)

const checkIfSlotNeeded = slotName => {
    return slots.value.includes(slotName)
}

const getRightItemValue = (item, value) => {
    const rightVal = value.split('.').reduce((obj, key) => {
        return obj && obj[key] !== undefined ? obj[key] : ''
    }, item)
    return rightVal === null || rightVal === '' ? '-' : rightVal
}

const slots = computed(() => Object.keys(useSlots()))

const props = defineProps({
    /**
     * Array of options objects
     * Object must contain 'text' and 'value' fields
     * 'text' can also be locale message key
     */
    headers: {
        type: Array,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    totalItems: {
        type: Number,
        default: 0,
    },
    page: {
        type: Number,
        default: 1,
    },
    rowsPerPage: {
        type: Number,
        default: 10,
    },
    rowsPerPageMessage: {
        type: String,
        default: 'Rows per page',
    },
    rowsItems: {
        type: Array,
        default: () => [10, 25, 50, 100],
    },
    hideFooter: {
        type: Boolean,
        default: false,
    },
    serverOptions: {
        type: Object,
        default: null,
    },
    serverItemsLength: {
        type: Number,
        default: undefined,
    },
    headerType: {
        type: String,
        default: 'default',
        validator: value => ['default', 'clear'].includes(value),
    },
    multiSort: {
        type: Boolean,
        default: false,
    },
    sortType: {
        type: Array,
        default: () => [],
    },
    bulkActions: {
        type: Array,
        default: () => [],
    },
    enableSelect: {
        type: Boolean,
        default: false,
    },
    alternating: {
        type: Boolean,
        default: true,
    },
    themeColor: {
        type: String,
        default: '#11263B',
    },
    tableClassName: {
        type: String,
        default: 'customize-table',
    },
    bodyRowClassName: {
        type: Function,
        default: () => '',
    },
})

const tableProps = reactive({ ...toRefs(props) })

const emit = defineEmits([
    'click-row',
    'expand-row',
    'update-sort',
    'update-page-items',
    'contextmenu-row',
    'select-all',
    'update:server-options',
    'scroll',

    'select-bulk-action',
])

const dataTableListeners = {
    'click-row': rowData => emit('click-row', rowData),
    'expand-row': (rowId, isExpanded) => emit('expand-row', rowId, isExpanded),
    'update-sort': (sortField, sortType) =>
        emit('update-sort', sortField, sortType),
    'update-page-items': pageItems => emit('update-page-items', pageItems),
    'contextmenu-row': (rowId, event) => emit('contextmenu-row', rowId, event),
    'select-all': selectedRows => emit('select-all', selectedRows),
    'update:server-options': value => emit('update:server-options', value),
}

const itemsSelectedComputed = computed({
    get() {
        return props.enableSelect ? itemsSelected.value : null
    },
    set(newVal) {
        if (props.enableSelect) {
            itemsSelected.value = newVal
        }
    },
})
const itemsSelected = ref([])

onMounted(() => {
    baseTable.value.$el
        .querySelector('.vue3-easy-data-table__main')
        .addEventListener('scroll', () => emit('scroll'))
})

onBeforeUnmount(() => {
    baseTable.value.$el
        .querySelector('.vue3-easy-data-table__main')
        .removeEventListener('scroll', () => emit('scroll'))
})
</script>

<style scoped lang="sass">
.customize-table
  ::v-deep(.vue3-easy-data-table__main)
    min-height: fit-content
    //max-height: 67.2vh
    width: 100%

  ::v-deep(table)
    z-index: 0

::v-deep(.vue3-easy-data-table__header)
  position: sticky
  top: 0
  background: var(--color-bg-white)
  z-index: 9
  padding: 0
  cursor: pointer

  th
    color: var(--grey-darken-1, #757575)
    text-overflow: ellipsis
    white-space: nowrap
    font-family: var(--font-main), sans-serif
    font-size: 12px
    font-style: normal
    font-weight: 700
    line-height: 20px
    letter-spacing: 0.4px
    padding: 16px 24px 16px 10px

    &.sortable span .sortType-icon, &.sortable span .sortType-icon::before
      position: relative
      visibility: hidden

    &.sortable span .sortType-icon::after
      visibility: visible
      content: url("/icons/sort-asc.svg")
      transform: translate(-2px, -10px) scaleY(1)
      display: block

    &.sortable.desc span .sortType-icon::after
      transform: translate(-22px, -10px) scaleY(-1) scaleX(-1) rotate(180deg)

::v-deep(.vue3-easy-data-table__body)
  height: 100%

  .customize-table__row-hidden
    td
      background-color: #E6E8EC

  tr
    //overflow: hidden
    color: var(--black-087, rgba(0, 0, 0, 0.87))
    text-overflow: ellipsis
    white-space: nowrap

    /* v-text/body-2 */
    font-family: var(--font-main), sans-serif
    font-size: 14px
    font-style: normal
    font-weight: 400
    line-height: 20px
    /* 142.857% */
    letter-spacing: 0.25px

  tr:has(td.expand)
    background: transparent
    height: 80px !important

  td
    padding: 16px 10px

    &.expand
      padding: 0

      &:hover
        background: transparent !important

::v-deep(.vue3-easy-data-table__footer)
  padding-left: 16px
  padding-right: 16px
  height: 58px

  .pagination__rows-per-page
    width: 100%
    color: var(--black-087, rgba(0, 0, 0, 0.87))
    text-align: right
    font-family: var(--font-main), sans-serif
    font-size: 12px
    font-style: normal
    font-weight: 400
    line-height: 20px
    letter-spacing: 0.4px

    .easy-data-table__rows-selector
      .select-items.show
        li
          padding: 0.5rem 0.75rem

          &.selected, &:hover
            background-color: var(--color-background-champagne)
            color: var(--color-black)

  .pagination__items-index
    display: none
</style>
