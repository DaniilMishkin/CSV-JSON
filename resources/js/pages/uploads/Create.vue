<template>
    <AuthenticatedLayout>
        <div class="w-full flex flex-col items-center gap-8">
            <h1 class="w-full text-center text-2xl text-black">Upload CSV</h1>
            <UploadCreateForm v-bind="formOptions" />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '../../layouts/AuthenticatedLayout.vue'
import UploadCreateForm from './components/UploadCreateForm.vue'
import { reactive, toRefs } from 'vue'

const { options } = defineProps({
    options: {
        type: Array,
        default: () => [],
    },
})

const formOptions = reactive({ ...toRefs(options) })
</script>
