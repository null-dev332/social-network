<script setup>
import Pager from '@/Components/Pager.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    likes: Object,
});

function destroy(id) {
    if (!confirm('Delete this like?')) {
        return;
    }

    router.delete(route('admin.likes.destroy', id));
}
</script>

<template>
    <AdminLayout title="Likes">
        <p class="mb-4 text-sm text-neutral-500">
            A like is just a user and a post, so there is nothing to edit.
        </p>
        <div class="card overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Post</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="like in likes.data" :key="like.id">
                        <td class="whitespace-nowrap font-semibold">{{ like.user?.name }}</td>
                        <td class="max-w-md truncate text-neutral-600">{{ like.post?.content }}</td>
                        <td class="text-right">
                            <button type="button" class="link-delete" @click="destroy(like.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pager :paginator="likes" />
    </AdminLayout>
</template>
