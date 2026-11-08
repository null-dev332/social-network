<script setup>
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import Pager from '@/Components/Pager.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.q || '');
let timer = null;

// wait a bit after typing so we don't send a request on every key
function onInput() {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            route('users.index'),
            search.value ? { q: search.value } : {},
            { preserveState: true, replace: true },
        );
    }, 300);
}

function clear() {
    search.value = '';
    onInput();
}
</script>

<template>
    <Head title="Search" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-[600px] px-4 py-6 md:py-10">
            <h1 class="mb-5 text-2xl font-semibold">Search</h1>

            <div class="relative">
                <Icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search people"
                    class="block w-full rounded-lg border-0 bg-neutral-100 py-2.5 pl-9 pr-9 text-sm placeholder:text-neutral-500 focus:ring-0"
                    autofocus
                    @input="onInput"
                />
                <button
                    v-if="search"
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-neutral-400 p-0.5 text-white"
                    title="Clear"
                    @click="clear"
                >
                    <Icon name="close" class="h-3 w-3" />
                </button>
            </div>

            <p v-if="users.data.length === 0" class="py-12 text-center text-sm text-neutral-500">
                No results found.
            </p>

            <div class="mt-4">
                <Link
                    v-for="user in users.data"
                    :key="user.id"
                    :href="route('users.show', user.id)"
                    class="-mx-3 flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-neutral-50"
                >
                    <Avatar :user="user" :size="44" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ user.name }}</p>
                        <p class="truncate text-sm text-neutral-500">
                            <template v-if="user.bio">{{ user.bio }} • </template>
                            {{ user.followers_count }} {{ user.followers_count === 1 ? 'follower' : 'followers' }}
                        </p>
                    </div>
                </Link>
            </div>

            <Pager :paginator="users" />
        </div>
    </AuthenticatedLayout>
</template>
