<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="New password" />

        <h1 class="mb-5 text-center font-semibold">Create a new password</h1>

        <form class="space-y-2" @submit.prevent="submit">
            <div>
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    placeholder="Email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    placeholder="New password"
                    required
                    autofocus
                    autocomplete="new-password"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div>
                <TextInput
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    placeholder="Confirm new password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <button type="submit" class="btn-primary !mt-4 w-full" :disabled="form.processing">
                Reset password
            </button>
        </form>
    </GuestLayout>
</template>
