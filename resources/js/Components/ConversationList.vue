<script setup>
import Avatar from '@/Components/Avatar.vue';
import { timeAgo } from '@/utils/time';
import { useEcho } from '@laravel/echo-vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    // id of the user whose chat is open right now
    activeId: {
        type: Number,
        default: null,
    },
});

const me = usePage().props.auth.user;
const rows = ref([...props.conversations]);

watch(
    () => props.conversations,
    (value) => (rows.value = [...(value || [])]),
);

// move the conversation to the top when a new message comes in
function onMessage(payload) {
    const message = payload?.message;

    if (!message) {
        return;
    }

    const mine = Number(message.sender_id) === Number(me.id);
    const partnerId = mine ? Number(message.receiver_id) : Number(message.sender_id);
    const partnerName = mine ? message.receiver_name : message.sender_name;
    const isOpen = partnerId === props.activeId;

    let row = rows.value.find((r) => Number(r.user.id) === partnerId);

    if (!row) {
        row = { user: { id: partnerId, name: partnerName || 'User' }, unread: 0 };
    }

    row.last_message = message.content;
    row.last_at = message.created_at;
    row.mine = mine;

    if (!mine && !isOpen) {
        row.unread = (row.unread || 0) + 1;
    }

    rows.value = [row, ...rows.value.filter((r) => Number(r.user.id) !== partnerId)];
}

useEcho(`chat.${me.id}`, '.message.sent', onMessage);
</script>

<template>
    <div class="flex h-full flex-col">
        <h2 class="px-6 pb-3 pt-6 text-xl font-bold">Messages</h2>

        <div v-if="rows.length === 0" class="px-6 py-4 text-sm text-neutral-500">
            No messages yet. Open someone's profile and press Message.
        </div>

        <div class="flex-1 overflow-y-auto">
            <Link
                v-for="row in rows"
                :key="row.user.id"
                :href="route('messages.show', row.user.id)"
                class="flex items-center gap-3 px-6 py-2 hover:bg-neutral-50"
                :class="{ 'bg-neutral-100 hover:bg-neutral-100': row.user.id === activeId }"
            >
                <Avatar :user="row.user" :size="56" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm" :class="row.unread ? 'font-bold' : ''">{{ row.user.name }}</p>
                    <p class="flex gap-1 text-sm" :class="row.unread ? 'font-semibold text-neutral-900' : 'text-neutral-500'">
                        <span class="truncate">
                            <template v-if="row.mine">You: </template>{{ row.last_message }}
                        </span>
                        <span class="shrink-0">· {{ timeAgo(row.last_at) }}</span>
                    </p>
                </div>
                <span v-if="row.unread" class="h-2 w-2 shrink-0 rounded-full bg-primary" />
            </Link>
        </div>
    </div>
</template>
