<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign up" />

        <p class="-mt-4 mb-5 text-center font-semibold text-neutral-500">
            Sign up to share posts and keep up with your friends.
        </p>

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
                    id="name"
                    type="text"
                    v-model="form.name"
                    placeholder="Full name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div>
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    placeholder="Password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div>
                <TextInput
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    placeholder="Confirm password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <button type="submit" class="btn-primary !mt-4 w-full" :disabled="form.processing">
                Sign up
            </button>
        </form>

        <template #footer>
            Have an account?
            <Link :href="route('login')" class="font-semibold text-primary">Log in</Link>
        </template>
    </GuestLayout>
</template>
