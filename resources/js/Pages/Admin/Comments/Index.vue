<script setup>
import Pager from '@/Components/Pager.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    comments: Object,
});

function destroy(id) {
    if (!confirm('Delete this comment?')) {
        return;
    }

    router.delete(route('admin.comments.destroy', id));
}
</script>

<template>
    <AdminLayout title="Comments">
        <div class="card overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Comment</th>
                        <th>On post</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="comment in comments.data" :key="comment.id">
                        <td class="whitespace-nowrap font-semibold">{{ comment.user?.name }}</td>
                        <td class="max-w-xs truncate">{{ comment.content }}</td>
                        <td class="max-w-xs truncate text-neutral-500">{{ comment.post?.content }}</td>
                        <td class="whitespace-nowrap text-right">
                            <Link :href="route('admin.comments.edit', comment.id)" class="link-edit">Edit</Link>
                            <button type="button" class="link-delete" @click="destroy(comment.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pager :paginator="comments" />
    </AdminLayout>
</template>
