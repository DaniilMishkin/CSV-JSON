<template>
    <div class="table-pagination">
        <div class="table-pagination__total">{{ total }} results</div>

        <SvgIcon
            :class="[
                'table-pagination__icon',
                { 'table-pagination__icon_disabled': isFirstPage },
            ]"
            type="mdi"
            :path="mdiChevronLeft"
            @click="emits('goPrev')"
        />
        <div class="table-pagination__pages-list">
            <div
                v-for="page in pagesList"
                :key="page"
                :class="[
                    'pages-list__page',
                    { 'pages-list__page_active': page === currentPage },
                    {
                        'pages-list__page_disabled':
                            page === PAGES_LIST_GAP_CONTENT,
                    },
                ]"
                @click="emits('selectPage', page)"
            >
                {{ page }}
            </div>
        </div>
        <SvgIcon
            :class="[
                'table-pagination__icon',
                { 'table-pagination__icon_disabled': isLastPage },
            ]"
            type="mdi"
            :path="mdiChevronRight"
            @click="emits('goNext')"
        />
    </div>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiChevronLeft, mdiChevronRight } from '@mdi/js'
import { computed } from 'vue'

const PAGES_LIST_MAX_VISIBLE_PAGES = 5
const PAGES_LIST_MAX_VISIBLE_PAGES_WITH_GAP = 3
const PAGES_LIST_GAP_CONTENT = '...'

const emits = defineEmits(['goNext', 'goPrev', 'selectPage'])

const { total, currentPage, lastPage, ...props } = defineProps({
    total: {
        type: Number,
        default: 0,
    },
    currentPage: {
        type: Number,
        required: true,
    },
    lastPage: {
        type: Number,
        required: true,
    },
    isFirstPage: {
        type: [null, Boolean],
        default: null,
    },
    isLastPage: {
        type: [null, Boolean],
        default: null,
    },
})

const isFirstPage = computed(() => props.isFirstPage ?? currentPage === 1)
const isLastPage = computed(() => props.isLastPage ?? currentPage >= lastPage)

const pagesList = computed(() => {
    if (total === 0) {
        return [1]
    }

    if (lastPage <= PAGES_LIST_MAX_VISIBLE_PAGES) {
        return Array.from({ length: lastPage }, (_, i) => i + 1)
    }

    if (currentPage <= PAGES_LIST_MAX_VISIBLE_PAGES_WITH_GAP) {
        return [1, 2, 3, 4, PAGES_LIST_GAP_CONTENT, lastPage]
    }
    if (currentPage > lastPage - PAGES_LIST_MAX_VISIBLE_PAGES_WITH_GAP) {
        return [
            1,
            PAGES_LIST_GAP_CONTENT,
            lastPage - 3,
            lastPage - 2,
            lastPage - 1,
            lastPage,
        ]
    }
    return [
        1,
        PAGES_LIST_GAP_CONTENT,
        currentPage - 1,
        currentPage,
        currentPage + 1,
        PAGES_LIST_GAP_CONTENT,
        lastPage,
    ]
})
</script>

<style scoped lang="sass">
$total-text-color: #1C1F22

$page-text: rgba(0, 0, 0, 0.87)
$page-text-active: #FFF
$page-text-hover: rgba(0, 0, 0, 0.87)

$page-bg: transparent
$page-bg-active: var(--color-bg-gold)
$page-bg-hover: #E6E8EC

.table-pagination
  width: 100%
  height: 100%
  display: flex
  align-items: center
  justify-content: end
  gap: 34px
  padding-top: 2px

.table-pagination__total
  text-align: right
  color: $total-text-color
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 24px
  letter-spacing: 0.4px

.table-pagination__icon
  cursor: pointer
  color: #0000008A
  transition: all ease 200ms
  &_disabled
    pointer-events: none
    cursor: default
    color: #00000033

.table-pagination__pages-list
  display: flex
  gap: 12px

.pages-list__page
  width: 32px
  height: 32px
  padding: 2px 4px
  border-radius: 4px
  cursor: pointer
  display: flex
  justify-content: center
  align-items: center
  color: $page-text
  font-size: 14px
  font-style: normal
  font-weight: 400
  line-height: 20px
  transition: all ease 200ms
  background-color: $page-bg
  &:hover
    color: $page-text-hover
    background-color: $page-bg-hover
  &_active
    cursor: default
    color: $page-text-active
    background-color: $page-bg-active
    &:hover
      color: $page-text-active
      background-color: $page-bg-active
  &_disabled
    pointer-events: none
</style>
