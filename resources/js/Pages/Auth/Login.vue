<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm text-green-600">
            {{ status }}
        </div>

        <form class="space-y-2" @submit.prevent="submit">
            <TextInput
                id="email"
                type="email"
                v-model="form.email"
                placeholder="Email"
                required
                autofocus
                autocomplete="username"
            />

            <TextInput
                id="password"
                type="password"
                v-model="form.password"
                placeholder="Password"
                required
                autocomplete="current-password"
            />

            <label class="flex items-center gap-2 pt-1 text-xs text-neutral-600">
                <Checkbox name="remember" v-model:checked="form.remember" />
                Remember me
            </label>

            <button type="submit" class="btn-primary !mt-4 w-full" :disabled="form.processing">
                Log in
            </button>

            <InputError class="pt-2 text-center" :message="form.errors.email || form.errors.password" />
        </form>

        <div v-if="canResetPassword" class="mt-5">
            <div class="flex items-center gap-4 text-xs font-semibold text-neutral-500">
                <span class="h-px flex-1 bg-neutral-300" />
                OR
                <span class="h-px flex-1 bg-neutral-300" />
            </div>
            <Link
                :href="route('password.request')"
                class="mt-5 block text-center text-sm text-neutral-800 hover:underline"
            >
                Forgot password?
            </Link>
        </div>

        <template #footer>
            Don't have an account?
            <Link :href="route('register')" class="font-semibold text-primary">Sign up</Link>
        </template>
    </GuestLayout>
</template>
