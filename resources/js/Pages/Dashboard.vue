<script setup>
import Avatar from '@/Components/Avatar.vue';
import Pager from '@/Components/Pager.vue';
import PostCard from '@/Components/PostCard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    posts: Object,
    suggestions: Array,
});

const me = usePage().props.auth.user;
const followedIds = ref([]);

function follow(user) {
    followedIds.value.push(user.id);
    router.post(route('users.follow', user.id), {}, { preserveScroll: true, preserveState: true, async: true });
}
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="mx-auto flex max-w-[935px] justify-center gap-16 px-4 py-6 md:py-10">
            <div class="w-full max-w-[470px]">
                <div v-if="posts.data.length === 0" class="py-16 text-center">
                    <h2 class="text-xl font-bold">Welcome to SocSit</h2>
                    <p class="mt-2 text-sm text-neutral-500">
                        Follow people to see their posts here, or share your first one.
                    </p>
                    <Link :href="route('users.index')" class="btn-primary mt-5">
                        Find people
                    </Link>
                </div>

                <div class="space-y-4">
                    <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
                </div>

                <Pager :paginator="posts" />
            </div>

            <aside class="hidden w-80 shrink-0 lg:block">
                <div class="flex items-center gap-3">
                    <Link :href="route('users.show', me.id)">
                        <Avatar :user="me" :size="44" />
                    </Link>
                    <div class="min-w-0 flex-1 text-sm">
                        <Link :href="route('users.show', me.id)" class="block truncate font-semibold">
                            {{ me.name }}
                        </Link>
                        <p class="truncate text-neutral-500">{{ me.email }}</p>
                    </div>
                    <Link :href="route('profile.edit')" class="text-xs font-semibold text-primary hover:text-primary-dark">
                        Settings
                    </Link>
                </div>

                <template v-if="suggestions.length">
                    <div class="mb-3 mt-6 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-neutral-500">Suggested for you</h3>
                        <Link :href="route('users.index')" class="text-xs font-semibold hover:text-neutral-500">
                            See all
                        </Link>
                    </div>

                    <div v-for="user in suggestions" :key="user.id" class="flex items-center gap-3 py-2">
                        <Link :href="route('users.show', user.id)">
                            <Avatar :user="user" :size="36" />
                        </Link>
                        <div class="min-w-0 flex-1 text-sm">
                            <Link :href="route('users.show', user.id)" class="block truncate font-semibold">
                                {{ user.name }}
                            </Link>
                            <p class="text-xs text-neutral-500">
                                {{ user.followers_count }}
                                {{ user.followers_count === 1 ? 'follower' : 'followers' }}
                            </p>
                        </div>
                        <span v-if="followedIds.includes(user.id)" class="text-xs font-semibold text-neutral-500">
                            Following
                        </span>
                        <button
                            v-else
                            type="button"
                            class="text-xs font-semibold text-primary hover:text-primary-dark"
                            @click="follow(user)"
                        >
                            Follow
                        </button>
                    </div>
                </template>

                <p class="mt-8 text-xs text-neutral-400">© {{ new Date().getFullYear() }} SocSit</p>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
