<script setup>
import Avatar from '@/Components/Avatar.vue';
import Pager from '@/Components/Pager.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

defineProps({
    users: Object,
});

const me = usePage().props.auth.user;

function destroy(id) {
    if (!confirm('Delete this user and all of their posts?')) {
        return;
    }

    router.delete(route('admin.users.destroy', id));
}
</script>

<template>
    <AdminLayout title="Users">
        <div class="card overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                        <td>
                            <div class="flex items-center gap-3">
                                <Avatar :user="user" :size="28" />
                                <span class="font-semibold">{{ user.name }}</span>
                            </div>
                        </td>
                        <td class="text-neutral-600">{{ user.email }}</td>
                        <td>
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize"
                                :class="{
                                    'bg-violet-100 text-violet-700': user.role === 'admin',
                                    'bg-sky-100 text-sky-700': user.role === 'manager',
                                    'bg-neutral-100 text-neutral-600': user.role === 'user',
                                }"
                            >
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap text-right">
                            <Link :href="route('admin.users.edit', user.id)" class="link-edit">Edit</Link>
                            <button v-if="user.id !== me.id" type="button" class="link-delete" @click="destroy(user.id)">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pager :paginator="users" />
    </AdminLayout>
</template>
