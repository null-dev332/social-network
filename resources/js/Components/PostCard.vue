<script setup>
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import Modal from '@/Components/Modal.vue';
import { timeAgo } from '@/utils/time';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const me = usePage().props.auth.user;
const showOptions = ref(false);
const showBigHeart = ref(false);
const expanded = ref(false);

const isOwner = computed(() => props.post.user_id === me.id);
const canDelete = computed(() => isOwner.value || ['admin', 'manager'].includes(me.role));
const isLong = computed(() => props.post.content.length > 120);

const liked = ref(props.post.liked);
const likesCount = ref(props.post.likes_count);

// keep it in sync when the page gets new data from the server
watch(
    () => props.post,
    (post) => {
        liked.value = post.liked;
        likesCount.value = post.likes_count;
    },
);

// change the heart right away, the request runs in the background
function toggleLike() {
    liked.value = !liked.value;
    likesCount.value += liked.value ? 1 : -1;

    router.post(route('posts.like', props.post.id), {}, { preserveScroll: true, preserveState: true, async: true });
}

// double click on the photo only likes, it never removes the like
function likeFromImage() {
    showBigHeart.value = true;
    setTimeout(() => (showBigHeart.value = false), 700);

    if (!liked.value) {
        toggleLike();
    }
}

function destroy() {
    if (!confirm('Delete this post?')) {
        return;
    }

    showOptions.value = false;
    router.delete(route('posts.destroy', props.post.id), { preserveScroll: true });
}
</script>

<template>
    <article class="border-b border-neutral-200 pb-4">
        <div class="flex items-center gap-3 py-3">
            <Link :href="route('users.show', post.user.id)">
                <Avatar :user="post.user" :size="32" />
            </Link>
            <div class="flex min-w-0 flex-1 items-center gap-1 text-sm">
                <Link :href="route('users.show', post.user.id)" class="truncate font-semibold">
                    {{ post.user.name }}
                </Link>
                <span class="text-neutral-500">• {{ timeAgo(post.created_at) }}</span>
            </div>
            <button
                v-if="canDelete"
                type="button"
                class="p-1 text-neutral-900 hover:text-neutral-500"
                title="More options"
                @click="showOptions = true"
            >
                <Icon name="dots" class="h-6 w-6" />
            </button>
        </div>

        <div
            v-if="post.image"
            class="relative overflow-hidden rounded border border-neutral-200 bg-neutral-100"
            @dblclick="likeFromImage"
        >
            <img
                :src="'/storage/' + post.image"
                alt=""
                class="max-h-[585px] w-full select-none object-cover"
            />
            <Transition
                enter-active-class="transition duration-200"
                enter-from-class="scale-50 opacity-0"
                leave-active-class="transition duration-300"
                leave-to-class="scale-110 opacity-0"
            >
                <Icon
                    v-if="showBigHeart"
                    name="heart"
                    filled
                    class="pointer-events-none absolute left-1/2 top-1/2 h-24 w-24 -translate-x-1/2 -translate-y-1/2 text-white drop-shadow-lg"
                />
            </Transition>
        </div>

        <p
            v-else
            class="whitespace-pre-wrap break-words rounded-lg bg-neutral-50 px-4 py-5 text-[15px] leading-relaxed"
        >
            {{ post.content }}
        </p>

        <div class="flex items-center gap-4 pt-3">
            <button
                type="button"
                class="transition active:scale-90"
                :class="liked ? 'text-like' : 'hover:text-neutral-500'"
                :title="liked ? 'Unlike' : 'Like'"
                @click="toggleLike"
            >
                <Icon name="heart" :filled="liked" class="h-7 w-7" />
            </button>
            <Link :href="route('posts.show', post.id)" class="hover:text-neutral-500" title="Comment">
                <Icon name="comment" class="h-7 w-7 -scale-x-100" />
            </Link>
        </div>

        <p class="mt-2 text-sm font-semibold">
            {{ likesCount }} {{ likesCount === 1 ? 'like' : 'likes' }}
        </p>

        <p v-if="post.image" class="mt-1 whitespace-pre-wrap break-words text-sm">
            <Link :href="route('users.show', post.user.id)" class="me-1 font-semibold">
                {{ post.user.name }}
            </Link>
            <template v-if="!isLong || expanded">{{ post.content }}</template>
            <template v-else>
                {{ post.content.slice(0, 120) }}...
                <button type="button" class="text-neutral-500" @click="expanded = true">more</button>
            </template>
        </p>

        <Link
            v-if="post.comments_count > 0"
            :href="route('posts.show', post.id)"
            class="mt-1 block text-sm text-neutral-500"
        >
            View {{ post.comments_count === 1 ? '1 comment' : 'all ' + post.comments_count + ' comments' }}
        </Link>
        <Link v-else :href="route('posts.show', post.id)" class="mt-1 block text-sm text-neutral-500">
            Add a comment...
        </Link>

        <Modal :show="showOptions" max-width="sm" @close="showOptions = false">
            <div class="divide-y divide-neutral-200 text-center text-sm">
                <button
                    v-if="canDelete"
                    type="button"
                    class="block w-full py-3.5 font-bold text-red-500"
                    @click="destroy"
                >
                    Delete
                </button>
                <Link v-if="isOwner" :href="route('posts.edit', post.id)" class="block py-3.5">
                    Edit
                </Link>
                <Link :href="route('posts.show', post.id)" class="block py-3.5">Go to post</Link>
                <button type="button" class="block w-full py-3.5" @click="showOptions = false">
                    Cancel
                </button>
            </div>
        </Modal>
    </article>
</template>
