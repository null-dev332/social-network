<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset password" />

        <h1 class="text-center font-semibold">Trouble logging in?</h1>
        <p class="mb-5 mt-2 text-center text-sm text-neutral-500">
            Enter your email and we'll send you a link to get back into your account.
        </p>

        <div v-if="status" class="mb-4 text-center text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <TextInput
                id="email"
                type="email"
                v-model="form.email"
                placeholder="Email"
                required
                autofocus
                autocomplete="username"
            />
            <InputError class="mt-1" :message="form.errors.email" />

            <button type="submit" class="btn-primary mt-4 w-full" :disabled="form.processing">
                Send reset link
            </button>
        </form>

        <template #footer>
            <Link :href="route('login')" class="font-semibold">Back to login</Link>
        </template>
    </GuestLayout>
</template>
