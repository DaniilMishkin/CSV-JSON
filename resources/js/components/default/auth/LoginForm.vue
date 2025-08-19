<template>
    <div
        class="flex flex-col p-8 items-center justify-center rounded-md bg-white"
    >
        <form class="w-full flex flex-col gap-8" @submit.prevent="submit">
            <div class="flex flex-col gap-4">
                <BaseInput
                    v-model="form.email"
                    class="mb-2"
                    label="Username"
                    placeholder="Enter username"
                    :errors="[form.errors.email]"
                />
                <BaseInput
                    v-model="form.password"
                    type="password"
                    label="Password"
                    placeholder="Enter password"
                    :errors="[form.errors.password]"
                />
                <CheckboxWithLabel
                    v-model="form.remember"
                    label="Remember me"
                />
            </div>

            <BaseButton
                id="submit"
                class="w-full"
                type="contained"
                label="Login"
            />
        </form>
    </div>
</template>

<script setup>
import CheckboxWithLabel from '@components/ui/checkbox/CheckboxWithLabel.vue'
import BaseInput from '@components/ui/input/BaseInput.vue'
import BaseButton from '@components/ui/BaseButton.vue'
import { RouteNamesVocabulary } from '../../../helpers/route-names-vocabulary.js'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = async () => {
    form.post(route(RouteNamesVocabulary.auth.login.handler), {
        onSuccess: () => {},
        onError: () => {},
    })
}
</script>
