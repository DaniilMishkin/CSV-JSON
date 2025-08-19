<template>
    <div class="base-file-uploader">
        <slot :uploader="fileUploader" :select-files="selectFiles" />

        <input
            ref="fileUploader"
            class="hidden"
            type="file"
            :accept="accept"
            @change="handleUploadFile"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { convertFileToBase64 } from '../../../helpers/convert-file-to-base-64.js'

const emits = defineEmits(['update:modelValue', 'loaded'])

const props = defineProps({
    modelValue: {
        type: [null, Array, File],
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    withBase64: {
        type: Boolean,
        default: false,
    },
    accept: {
        type: String,
        default: '',
    },
})

const fileUploader = ref()

const selectFiles = () => fileUploader.value.click()

const handleUploadFile = async event => {
    const uploadFiles = [...event.target.files]

    if (!props.withBase64) {
        if (props.multiple) {
            emits('update:modelValue', uploadFiles)
            return
        }

        emits('update:modelValue', uploadFiles[0])
        return
    }

    if (props.multiple) {
        const readerFiles = []

        for (const file of uploadFiles) {
            const base64 = await convertFileToBase64(file)
            readerFiles.push({ file, base64 })
        }

        emits('update:modelValue', readerFiles)
        return
    }

    const base64 = await convertFileToBase64(uploadFiles[0])
    emits('update:modelValue', { file: uploadFiles[0], base64 })
}
</script>
