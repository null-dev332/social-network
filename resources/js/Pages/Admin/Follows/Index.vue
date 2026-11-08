<script setup>
import Pager from '@/Components/Pager.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    follows: Object,
});

function destroy(id) {
    if (!confirm('Remove this follow?')) {
        return;
    }

    router.delete(route('admin.follows.destroy', id));
}
</script>

<template>
    <AdminLayout title="Follows">
        <p class="mb-4 text-sm text-neutral-500">
            A follow is a link between two users, so there is nothing to edit.
        </p>
        <div class="card overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Follower</th>
                        <th>Follows</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="follow in follows.data" :key="follow.id">
                        <td class="whitespace-nowrap font-semibold">{{ follow.follower?.name }}</td>
                        <td class="whitespace-nowrap">{{ follow.followed?.name }}</td>
                        <td class="text-right">
                            <button type="button" class="link-delete" @click="destroy(follow.id)">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pager :paginator="follows" />
    </AdminLayout>
</template>
