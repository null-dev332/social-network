<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Settings" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-[700px] space-y-10 px-4 py-6 md:py-10">
            <h1 class="text-xl font-bold">Settings</h1>

            <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />

            <hr class="border-neutral-200" />

            <UpdatePasswordForm />

            <hr class="border-neutral-200" />

            <DeleteUserForm />
        </div>
    </AuthenticatedLayout>
</template>
