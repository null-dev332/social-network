<script setup>
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDate, timeAgo } from '@/utils/time';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    post: Object,
});

const me = usePage().props.auth.user;
const commentInput = ref(null);
const showOptions = ref(false);

const isOwner = computed(() => props.post.user_id === me.id);
const isModerator = ['admin', 'manager'].includes(me.role);

// local copies so the page can change right away, before the server answers
const liked = ref(props.post.liked);
const likesCount = ref(props.post.likes_count);
const comments = ref([...props.post.comments]);

watch(
    () => props.post,
    (post) => {
        liked.value = post.liked;
        likesCount.value = post.likes_count;
        comments.value = [...post.comments];
    },
);

const form = useForm({
    content: '',
});

function submit() {
    const content = form.content.trim();

    if (!content) {
        return;
    }

    // show the comment right away, it gets replaced by the saved one
    comments.value.push({
        id: 'new-' + Date.now(),
        user_id: me.id,
        user: me,
        content: content,
        created_at: new Date().toISOString(),
        pending: true,
    });

    form.post(route('comments.store', props.post.id), {
        preserveScroll: true,
        preserveState: true,
        async: true,
        onError: () => {
            comments.value.pop();
            form.content = content;
        },
    });
    form.content = '';
}

function toggleLike() {
    liked.value = !liked.value;
    likesCount.value += liked.value ? 1 : -1;

    router.post(route('posts.like', props.post.id), {}, { preserveScroll: true, preserveState: true, async: true });
}

// double click on the photo only likes, it never removes the like
function likeFromImage() {
    if (!liked.value) {
        toggleLike();
    }
}

function deletePost() {
    if (!confirm('Delete this post?')) {
        return;
    }

    router.delete(route('posts.destroy', props.post.id));
}

function deleteComment(id) {
    if (!confirm('Delete this comment?')) {
        return;
    }

    router.delete(route('comments.destroy', id), { preserveScroll: true });
}

function canDeleteComment(comment) {
    return comment.user_id === me.id || isModerator;
}
</script>

<template>
    <Head :title="post.user.name + ' on SocSit'" />

    <AuthenticatedLayout>
        <div class="mx-auto px-0 py-0 md:px-4 md:py-10" :class="post.image ? 'max-w-[935px]' : 'max-w-[600px]'">
            <div class="flex flex-col border-neutral-200 bg-white md:flex-row md:border">
                <div v-if="post.image" class="flex items-center justify-center bg-black md:w-[58%]">
                    <img
                        :src="'/storage/' + post.image"
                        alt=""
                        class="max-h-[600px] w-full object-contain"
                        @dblclick="likeFromImage"
                    />
                </div>

                <div class="flex flex-1 flex-col md:max-h-[600px]" :class="{ 'md:min-h-[450px]': post.image }">
                    <div class="flex items-center gap-3 border-b border-neutral-200 px-4 py-3">
                        <Link :href="route('users.show', post.user.id)">
                            <Avatar :user="post.user" :size="32" />
                        </Link>
                        <Link :href="route('users.show', post.user.id)" class="flex-1 text-sm font-semibold">
                            {{ post.user.name }}
                        </Link>
                        <button
                            v-if="isOwner || isModerator"
                            type="button"
                            class="p-1 hover:text-neutral-500"
                            title="More options"
                            @click="showOptions = true"
                        >
                            <Icon name="dots" class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="flex-1 space-y-4 overflow-y-auto px-4 py-4">
                        <p
                            v-if="!post.image"
                            class="whitespace-pre-wrap break-words text-[17px] leading-relaxed"
                        >
                            {{ post.content }}
                        </p>

                        <div v-else class="flex gap-3">
                            <Avatar :user="post.user" :size="32" />
                            <div class="min-w-0 text-sm">
                                <p class="whitespace-pre-wrap break-words">
                                    <Link :href="route('users.show', post.user.id)" class="me-1 font-semibold">
                                        {{ post.user.name }}
                                    </Link>
                                    {{ post.content }}
                                </p>
                                <p class="mt-1 text-xs text-neutral-500">{{ timeAgo(post.created_at) }}</p>
                            </div>
                        </div>

                        <div v-for="comment in comments" :key="comment.id" class="flex gap-3" :class="{ 'opacity-50': comment.pending }">
                            <Link :href="route('users.show', comment.user.id)">
                                <Avatar :user="comment.user" :size="32" />
                            </Link>
                            <div class="min-w-0 flex-1 text-sm">
                                <p class="whitespace-pre-wrap break-words">
                                    <Link :href="route('users.show', comment.user.id)" class="me-1 font-semibold">
                                        {{ comment.user.name }}
                                    </Link>
                                    {{ comment.content }}
                                </p>
                                <div class="mt-1 flex gap-3 text-xs text-neutral-500">
                                    <span>{{ timeAgo(comment.created_at) }}</span>
                                    <button
                                        v-if="!comment.pending && canDeleteComment(comment)"
                                        type="button"
                                        class="font-semibold hover:text-red-500"
                                        @click="deleteComment(comment.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="comments.length === 0" class="py-6 text-center">
                            <p class="font-semibold">No comments yet.</p>
                            <p class="text-sm text-neutral-500">Start the conversation.</p>
                        </div>
                    </div>

                    <div class="border-t border-neutral-200 px-4 pt-3">
                        <div class="flex items-center gap-4">
                            <button
                                type="button"
                                class="transition active:scale-90"
                                :class="liked ? 'text-like' : 'hover:text-neutral-500'"
                                :title="liked ? 'Unlike' : 'Like'"
                                @click="toggleLike"
                            >
                                <Icon name="heart" :filled="liked" class="h-7 w-7" />
                            </button>
                            <button type="button" class="hover:text-neutral-500" title="Comment" @click="commentInput.focus()">
                                <Icon name="comment" class="h-7 w-7 -scale-x-100" />
                            </button>
                        </div>
                        <p class="mt-2 text-sm font-semibold">
                            {{ likesCount }} {{ likesCount === 1 ? 'like' : 'likes' }}
                        </p>
                        <p class="mb-3 mt-1 text-xs uppercase tracking-wide text-neutral-500">
                            {{ formatDate(post.created_at) }}
                        </p>
                    </div>

                    <form class="border-t border-neutral-200 px-4 py-2" @submit.prevent="submit">
                        <div class="flex items-center gap-3">
                            <input
                                ref="commentInput"
                                v-model="form.content"
                                type="text"
                                maxlength="500"
                                placeholder="Add a comment..."
                                class="flex-1 border-0 px-0 py-2 text-sm focus:ring-0"
                            />
                            <button
                                type="submit"
                                class="text-sm font-semibold text-primary hover:text-primary-dark disabled:opacity-40"
                                :disabled="!form.content.trim()"
                            >
                                Post
                            </button>
                        </div>
                        <InputError class="pb-1" :message="form.errors.content" />
                    </form>
                </div>
            </div>
        </div>

        <Modal :show="showOptions" max-width="sm" @close="showOptions = false">
            <div class="divide-y divide-neutral-200 text-center text-sm">
                <button type="button" class="block w-full py-3.5 font-bold text-red-500" @click="deletePost">
                    Delete
                </button>
                <Link v-if="isOwner" :href="route('posts.edit', post.id)" class="block py-3.5">Edit</Link>
                <button type="button" class="block w-full py-3.5" @click="showOptions = false">Cancel</button>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
