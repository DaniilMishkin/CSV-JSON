<template>
    <div
        class="w-[600px] flex flex-col gap-4 p-4 border-[1px solid #e0e0e0] rounded-md bg-bg-white"
    >
        <BaseCsvUploader
            :model-value="form.file"
            :errors="[form.errors.file]"
            @update:model-value="handleUploadFile"
        />
        <BaseInput
            v-model="form.name"
            label="File name"
            placeholder="Enter file name"
            :errors="[form.errors.name]"
        />
        <CheckboxWithLabel v-model="form.isPrivate" label="Is private" />
        <BaseButton label="Upload" @click="uploadFile" />
    </div>
</template>

<script setup>
import BaseCsvUploader from '../../../components/ui/file-uploader/BaseCsvUploader.vue'
import CheckboxWithLabel from '../../../components/ui/checkbox/CheckboxWithLabel.vue'
import BaseInput from '../../../components/ui/input/BaseInput.vue'
import BaseButton from '../../../components/ui/BaseButton.vue'
import { RouteNamesVocabulary } from '../../../helpers/route-names-vocabulary.js'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { route } from 'ziggy-js'

const form = useForm({
    file: null,
    name: null,
    isPrivate: false,
})

const handleUploadFile = file => {
    form.file = file
    form.name = file.name
}

const uploadFile = async () => {
    form.post(route(RouteNamesVocabulary.uploads.ajax.store), {
        onSuccess: () => toast.success('Uploaded'),
        onError: () => toast.error('Smth went wrong'),
    })
}
</script>
