<script setup>
import AdminNav from '@/Components/AdminNav.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="title + ' • Admin'" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl px-4 py-6 md:py-10">
            <h1 class="text-2xl font-bold">{{ title }}</h1>
            <AdminNav />

            <p v-if="$page.props.errors.user" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600">
                {{ $page.props.errors.user }}
            </p>

            <slot />
        </div>
    </AuthenticatedLayout>
</template>
