<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const isAdmin = usePage().props.auth.user.role === 'admin';

// "active" is the route pattern, so the tab stays highlighted on the edit pages too
// managers only see posts and comments
const items = [
    { label: 'Overview', name: 'admin.dashboard', active: 'admin.dashboard', show: true },
    { label: 'Users', name: 'admin.users.index', active: 'admin.users.*', show: isAdmin },
    { label: 'Posts', name: 'admin.posts.index', active: 'admin.posts.*', show: true },
    { label: 'Comments', name: 'admin.comments.index', active: 'admin.comments.*', show: true },
    { label: 'Messages', name: 'admin.messages.index', active: 'admin.messages.*', show: isAdmin },
    { label: 'Likes', name: 'admin.likes.index', active: 'admin.likes.*', show: isAdmin },
    { label: 'Follows', name: 'admin.follows.index', active: 'admin.follows.*', show: isAdmin },
];
</script>

<template>
    <nav class="-mx-4 mb-6 mt-4 flex gap-6 overflow-x-auto border-b border-neutral-200 px-4 text-sm">
        <template v-for="item in items" :key="item.name">
            <Link
                v-if="item.show"
                :href="route(item.name)"
                class="-mb-px whitespace-nowrap border-b-2 py-3"
                :class="
                    route().current(item.active)
                        ? 'border-neutral-900 font-semibold'
                        : 'border-transparent text-neutral-500 hover:text-neutral-900'
                "
            >
                {{ item.label }}
            </Link>
        </template>
    </nav>
</template>
