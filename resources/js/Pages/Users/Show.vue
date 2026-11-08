<script setup>
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import Pager from '@/Components/Pager.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    profile: Object,
    posts: Object,
});

// local copies so the button changes right away
const isFollowing = ref(props.profile.is_following);
const followersCount = ref(props.profile.followers_count);

watch(
    () => props.profile,
    (profile) => {
        isFollowing.value = profile.is_following;
        followersCount.value = profile.followers_count;
    },
);

// background for posts without a photo
const tileColors = [
    'from-rose-100 to-orange-100',
    'from-sky-100 to-indigo-100',
    'from-emerald-100 to-teal-100',
    'from-amber-100 to-yellow-50',
    'from-violet-100 to-fuchsia-100',
];

function follow() {
    isFollowing.value = true;
    followersCount.value++;
    router.post(route('users.follow', props.profile.id), {}, { preserveScroll: true, preserveState: true, async: true });
}

function unfollow() {
    isFollowing.value = false;
    followersCount.value--;
    router.delete(route('users.unfollow', props.profile.id), { preserveScroll: true, preserveState: true, async: true });
}

function plural(count, word) {
    return count === 1 ? word : word + 's';
}
</script>

<template>
    <Head :title="profile.name" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-[935px] px-4 py-6 md:py-10">
            <header class="flex items-center gap-6 md:gap-20 md:px-12">
                <Avatar :user="profile" :size="150" class="hidden md:inline-flex" />
                <Avatar :user="profile" :size="80" class="md:hidden" />

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="mr-2 truncate text-xl">{{ profile.name }}</h1>

                        <template v-if="profile.is_self">
                            <Link :href="route('profile.edit')" class="btn-secondary">Edit profile</Link>
                        </template>
                        <template v-else>
                            <button v-if="isFollowing" type="button" class="btn-secondary" @click="unfollow">
                                Following
                            </button>
                            <button v-else type="button" class="btn-primary" @click="follow">Follow</button>
                            <Link :href="route('messages.show', profile.id)" class="btn-secondary">Message</Link>
                        </template>
                    </div>

                    <ul class="mt-5 hidden gap-10 md:flex">
                        <li><b>{{ profile.posts_count }}</b> {{ plural(profile.posts_count, 'post') }}</li>
                        <li><b>{{ followersCount }}</b> {{ plural(followersCount, 'follower') }}</li>
                        <li><b>{{ profile.following_count }}</b> following</li>
                    </ul>

                    <p v-if="profile.bio" class="mt-4 hidden whitespace-pre-wrap text-sm md:block">{{ profile.bio }}</p>
                </div>
            </header>

            <p v-if="profile.bio" class="mt-4 whitespace-pre-wrap text-sm md:hidden">{{ profile.bio }}</p>
            <p v-if="$page.props.errors.user" class="mt-3 text-sm text-red-500">{{ $page.props.errors.user }}</p>

            <!-- stats on mobile -->
            <ul class="mt-4 grid grid-cols-3 border-t border-neutral-200 py-3 text-center text-sm md:hidden">
                <li><b class="block">{{ profile.posts_count }}</b><span class="text-neutral-500">{{ plural(profile.posts_count, 'post') }}</span></li>
                <li><b class="block">{{ followersCount }}</b><span class="text-neutral-500">{{ plural(followersCount, 'follower') }}</span></li>
                <li><b class="block">{{ profile.following_count }}</b><span class="text-neutral-500">following</span></li>
            </ul>

            <div class="mt-2 flex justify-center border-t border-neutral-200 md:mt-10">
                <span class="-mt-px flex items-center gap-1.5 border-t border-neutral-900 py-4 text-xs font-semibold uppercase tracking-widest">
                    <Icon name="grid" class="h-3.5 w-3.5" />
                    Posts
                </span>
            </div>

            <div v-if="posts.data.length === 0" class="py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-neutral-900">
                    <Icon name="photo" class="h-8 w-8" />
                </div>
                <p class="mt-4 text-2xl font-extrabold">No posts yet</p>
            </div>

            <div class="grid grid-cols-3 gap-1">
                <Link
                    v-for="post in posts.data"
                    :key="post.id"
                    :href="route('posts.show', post.id)"
                    class="group relative aspect-square overflow-hidden bg-neutral-100"
                >
                    <img v-if="post.image" :src="'/storage/' + post.image" alt="" class="h-full w-full object-cover" />
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center bg-gradient-to-br p-3 text-center sm:p-6"
                        :class="tileColors[post.id % tileColors.length]"
                    >
                        <p class="line-clamp-4 text-xs text-neutral-800 sm:text-base">{{ post.content }}</p>
                    </div>

                    <div
                        class="absolute inset-0 hidden items-center justify-center gap-6 bg-black/30 font-bold text-white group-hover:flex"
                    >
                        <span class="flex items-center gap-1.5">
                            <Icon name="heart" filled class="h-5 w-5" /> {{ post.likes_count }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <Icon name="comment" filled class="h-5 w-5 -scale-x-100" /> {{ post.comments_count }}
                        </span>
                    </div>
                </Link>
            </div>

            <Pager :paginator="posts" />
        </div>
    </AuthenticatedLayout>
</template>
