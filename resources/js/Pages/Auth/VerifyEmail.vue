<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const linkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verify email" />

        <h1 class="text-center font-semibold">Check your email</h1>
        <p class="mb-5 mt-2 text-center text-sm text-neutral-500">
            We sent you a link to verify your email address. Didn't get it? We can send another one.
        </p>

        <p v-if="linkSent" class="mb-4 text-center text-sm text-green-600">
            A new verification link has been sent.
        </p>

        <form @submit.prevent="submit">
            <button type="submit" class="btn-primary w-full" :disabled="form.processing">
                Resend email
            </button>
        </form>

        <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="mt-4 block w-full text-center text-sm text-neutral-500 hover:underline"
        >
            Log out
        </Link>
    </GuestLayout>
</template>
