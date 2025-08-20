<template>
    <AuthenticatedLayout>
        <div class="w-full flex flex-col items-center gap-8">
            <h1 class="w-full text-center text-2xl text-black">Upload CSV</h1>
            <UploadCreateForm />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '../../layouts/AuthenticatedLayout.vue'
import UploadCreateForm from './components/UploadCreateForm.vue'
</script>
