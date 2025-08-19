<template>
    <BaseFileUploader v-model="proxiedModelValue" accept=".csv,text/csv">
        <template #default="{ selectFiles }">
            <div class="flex flex-col gap-4">
                <BaseButton
                    label="SELECT FILE"
                    type="outlined"
                    :left-mdi-icon="mdiFileOutline"
                    @click="selectFiles"
                />

                <div v-if="modelValue" class="file-list">
                    <div class="flex gap-8 justify-between">
                        <SvgIcon type="mdi" :path="mdiFileOutline" :size="20" />
                        <span class="text-lg">{{ modelValue.name }}</span>
                        <span class="text-sm">{{
                            formatSize(modelValue.size)
                        }}</span>
                    </div>
                </div>
            </div>
        </template>
    </BaseFileUploader>

    <BaseErrorsBlock v-if="errorExists" :errors="errors" />
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiFileOutline } from '@mdi/js'
import { computed } from 'vue'
import BaseButton from '../BaseButton.vue'
import BaseFileUploader from './BaseFileUploader.vue'
import BaseErrorsBlock from '../BaseErrorsBlock.vue'

const emits = defineEmits(['update:modelValue'])

const props = defineProps({
    modelValue: {
        type: [null, File, Array],
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    buttonText: {
        type: String,
        default: 'Select CSV file',
    },
    errors: {
        type: Array,
        default: () => [],
    },
})

const proxiedModelValue = computed({
    get: () => props.modelValue,
    set: value => emits('update:modelValue', value),
})

const formatSize = size => {
    if (size < 1024) return `${size} B`
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`
    return `${(size / (1024 * 1024)).toFixed(2)} MB`
}

const errorExists = computed(() => {
    return props.errors.find(obj => obj !== undefined) ?? false
})
</script>
