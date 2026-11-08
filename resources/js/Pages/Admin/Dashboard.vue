<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage } from '@inertiajs/vue3';

const isAdmin = usePage().props.auth.user.role === 'admin';
</script>

<template>
    <AdminLayout title="Admin">
        <div class="card space-y-3 p-6 text-sm leading-relaxed text-neutral-700">
            <p v-if="isAdmin">
                As an admin you can manage users, posts, comments, messages, likes and follows.
                You can change a user's role here, but not their password. The password hash
                in the database always stays the same.
            </p>
            <p v-else>
                As a manager you can edit and delete posts and comments. Users, roles and
                messages are managed by the admin.
            </p>
            <p class="text-neutral-500">
                Likes and follows are only links between records, so they can be deleted but not edited.
            </p>
        </div>
    </AdminLayout>
</template>
