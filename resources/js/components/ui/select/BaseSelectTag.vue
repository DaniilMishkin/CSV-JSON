<template>
    <div
        class="flex gap-1 items-center bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded"
    >
        {{ item[labelField] }}
        <SvgIcon
            class="header__close-icon"
            :path="mdiClose"
            type="mdi"
            size="14"
            @click="$emit('remove', item)"
        />
    </div>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiClose } from '@mdi/js'

defineProps({
    item: {
        type: Object,
        required: true,
    },
    labelField: {
        type: String,
        required: true,
    },
})

defineEmits(['remove'])
</script>
