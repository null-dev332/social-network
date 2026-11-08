<script setup>
import ConversationList from '@/Components/ConversationList.vue';
import Icon from '@/Components/Icon.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    conversations: Array,
});
</script>

<template>
    <Head title="Messages" />

    <AuthenticatedLayout>
        <div class="flex h-[calc(100dvh-6.5rem)] md:h-screen">
            <div class="w-full border-neutral-200 md:w-[360px] md:shrink-0 md:border-r">
                <ConversationList :conversations="conversations" />
            </div>

            <div class="hidden flex-1 flex-col items-center justify-center text-center md:flex">
                <div class="flex h-24 w-24 items-center justify-center rounded-full border-2 border-neutral-900">
                    <Icon name="send" class="h-12 w-12 -rotate-12" />
                </div>
                <h2 class="mt-4 text-xl">Your messages</h2>
                <p class="mt-1 text-sm text-neutral-500">Send a private message to a friend.</p>
                <Link :href="route('users.index')" class="btn-primary mt-5">Send message</Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
