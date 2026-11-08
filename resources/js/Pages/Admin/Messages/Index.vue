<script setup>
import Pager from '@/Components/Pager.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    messages: Object,
});

function destroy(id) {
    if (!confirm('Delete this message?')) {
        return;
    }

    router.delete(route('admin.messages.destroy', id));
}
</script>

<template>
    <AdminLayout title="Messages">
        <div class="card overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Message</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="message in messages.data" :key="message.id">
                        <td class="whitespace-nowrap font-semibold">{{ message.sender?.name }}</td>
                        <td class="whitespace-nowrap">{{ message.receiver?.name }}</td>
                        <td class="max-w-md truncate text-neutral-600">{{ message.content }}</td>
                        <td class="whitespace-nowrap text-right">
                            <Link :href="route('admin.messages.edit', message.id)" class="link-edit">Edit</Link>
                            <button type="button" class="link-delete" @click="destroy(message.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pager :paginator="messages" />
    </AdminLayout>
</template>
