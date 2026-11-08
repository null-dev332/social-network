<script setup>
import Avatar from '@/Components/Avatar.vue';
import ConversationList from '@/Components/ConversationList.vue';
import Icon from '@/Components/Icon.vue';
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDate, formatTime } from '@/utils/time';
import { useEcho } from '@laravel/echo-vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
    other: Object,
    messages: Array,
    conversations: Array,
});

const me = usePage().props.auth.user;
const list = ref([...(props.messages || [])]);
const box = ref(null);

const form = useForm({
    content: '',
});

watch(
    () => props.messages,
    (value) => {
        list.value = [...(value || [])];
        scrollDown();
    },
);

function scrollDown() {
    nextTick(() => {
        if (box.value) {
            box.value.scrollTop = box.value.scrollHeight;
        }
    });
}

function onMessage(payload) {
    const message = payload?.message;

    if (!message) {
        return;
    }

    // my own messages are already on the screen, we only add the ones from the other person
    if (Number(message.sender_id) !== Number(props.other.id) || list.value.some((m) => m.id === message.id)) {
        return;
    }

    list.value.push(message);
    scrollDown();

    axios.post(route('messages.read', props.other.id)).catch(() => {});
}

useEcho(`chat.${me.id}`, '.message.sent', onMessage);

onMounted(scrollDown);

function submit() {
    const content = form.content.trim();

    if (!content) {
        return;
    }

    // show the message right away, the list is reloaded from the server after saving
    list.value.push({
        id: 'new-' + Date.now(),
        sender_id: me.id,
        receiver_id: props.other.id,
        content: content,
        created_at: new Date().toISOString(),
        pending: true,
    });
    scrollDown();

    form.post(route('messages.store', props.other.id), {
        preserveScroll: true,
        preserveState: true,
        async: true,
        onError: () => {
            list.value = list.value.filter((m) => !m.pending);
            form.content = content;
        },
    });
    form.content = '';
}

function isMine(message) {
    return Number(message.sender_id) === Number(me.id);
}

// show the date again if there was a longer pause between messages
function showTimeAbove(index) {
    if (index === 0) {
        return true;
    }

    const gap = new Date(list.value[index].created_at) - new Date(list.value[index - 1].created_at);

    return gap > 30 * 60 * 1000;
}

// avatar only next to the last message in a row from the other person
function showAvatar(index) {
    const next = list.value[index + 1];

    return !next || isMine(next);
}
</script>

<template>
    <Head :title="other.name" />

    <AuthenticatedLayout>
        <div class="flex h-[calc(100dvh-6.5rem)] md:h-screen">
            <div class="hidden w-[360px] shrink-0 border-r border-neutral-200 md:block">
                <ConversationList :conversations="conversations" :active-id="other.id" />
            </div>

            <div class="flex min-w-0 flex-1 flex-col">
                <div class="flex items-center gap-3 border-b border-neutral-200 px-4 py-3">
                    <Link :href="route('messages.index')" class="md:hidden" title="Back">
                        <Icon name="back" class="h-6 w-6" />
                    </Link>
                    <Link :href="route('users.show', other.id)" class="flex items-center gap-3">
                        <Avatar :user="other" :size="44" />
                        <span class="font-semibold">{{ other.name }}</span>
                    </Link>
                </div>

                <div ref="box" class="flex-1 overflow-y-auto px-4 py-4">
                    <div class="flex flex-col items-center py-6 text-center">
                        <Avatar :user="other" :size="96" />
                        <p class="mt-3 text-lg font-semibold">{{ other.name }}</p>
                        <Link :href="route('users.show', other.id)" class="btn-secondary mt-3">View profile</Link>
                    </div>

                    <template v-for="(message, index) in list" :key="message.id">
                        <p v-if="showTimeAbove(index)" class="my-4 text-center text-xs text-neutral-500">
                            {{ formatDate(message.created_at) }}, {{ formatTime(message.created_at) }}
                        </p>

                        <div class="mb-0.5 flex items-end gap-2" :class="isMine(message) ? 'justify-end' : 'justify-start'">
                            <div v-if="!isMine(message)" class="w-7 shrink-0">
                                <Avatar v-if="showAvatar(index)" :user="other" :size="28" />
                            </div>
                            <p
                                class="max-w-[70%] whitespace-pre-wrap break-words rounded-3xl px-3.5 py-2 text-[15px]"
                                :class="[isMine(message) ? 'bg-primary text-white' : 'bg-neutral-100', { 'opacity-60': message.pending }]"
                                :title="formatTime(message.created_at)"
                            >
                                {{ message.content }}
                            </p>
                        </div>
                    </template>
                </div>

                <form class="px-4 pb-4" @submit.prevent="submit">
                    <div class="flex items-center gap-2 rounded-full border border-neutral-300 px-4 py-1">
                        <input
                            v-model="form.content"
                            type="text"
                            maxlength="1000"
                            placeholder="Message..."
                            class="flex-1 border-0 px-0 py-2 text-sm focus:ring-0"
                            autofocus
                        />
                        <button
                            v-if="form.content.trim()"
                            type="submit"
                            class="text-sm font-semibold text-primary hover:text-primary-dark disabled:opacity-50"
                            >
                            Send
                        </button>
                    </div>
                    <InputError class="mt-1 px-4" :message="form.errors.content" />
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
