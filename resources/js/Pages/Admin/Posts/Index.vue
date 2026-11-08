<script setup>
import Pager from '@/Components/Pager.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    posts: Object,
});

function destroy(id) {
    if (!confirm('Delete this post?')) {
        return;
    }

    router.delete(route('admin.posts.destroy', id));
}
</script>

<template>
    <AdminLayout title="Posts">
        <div class="card overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Post</th>
                        <th>Comments</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="post in posts.data" :key="post.id">
                        <td class="whitespace-nowrap font-semibold">{{ post.user?.name }}</td>
                        <td class="max-w-md">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="post.image"
                                    :src="'/storage/' + post.image"
                                    alt=""
                                    class="h-10 w-10 shrink-0 rounded object-cover"
                                />
                                <Link :href="route('posts.show', post.id)" class="truncate hover:underline">
                                    {{ post.content }}
                                </Link>
                            </div>
                        </td>
                        <td class="text-neutral-600">{{ post.comments_count }}</td>
                        <td class="whitespace-nowrap text-right">
                            <Link :href="route('admin.posts.edit', post.id)" class="link-edit">Edit</Link>
                            <button type="button" class="link-delete" @click="destroy(post.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pager :paginator="posts" />
    </AdminLayout>
</template>
