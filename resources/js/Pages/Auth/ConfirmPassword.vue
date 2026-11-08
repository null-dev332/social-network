<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm password" />

        <p class="mb-5 text-center text-sm text-neutral-500">
            Please enter your password again before you continue.
        </p>

        <form @submit.prevent="submit">
            <TextInput
                id="password"
                type="password"
                v-model="form.password"
                placeholder="Password"
                required
                autocomplete="current-password"
                autofocus
            />
            <InputError class="mt-1" :message="form.errors.password" />

            <button type="submit" class="btn-primary mt-4 w-full" :disabled="form.processing">
                Confirm
            </button>
        </form>
    </GuestLayout>
</template>
