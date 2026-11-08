<script setup>
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const user = usePage().props.auth.user;
const fileInput = ref(null);
const preview = ref(null);

const form = useForm({
    content: '',
    image: null,
});

function pickImage(event) {
    const file = event.target.files[0];

    if (!file) {
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        form.setError('image', 'The image can be at most 2 MB.');
        event.target.value = '';
        return;
    }

    form.clearErrors('image');
    form.image = file;
    preview.value = URL.createObjectURL(file);
}

function removeImage() {
    form.image = null;
    preview.value = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function close() {
    form.reset();
    form.clearErrors();
    removeImage();
    emit('close');
}

function submit() {
    if (!form.content.trim()) {
        form.setError('content', 'Write something first.');
        return;
    }

    form.post(route('posts.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <Modal :show="show" max-width="xl" @close="close">
        <form @submit.prevent="submit">
            <div class="flex items-center justify-between border-b border-neutral-200 px-4 py-2.5">
                <button type="button" class="text-sm" @click="close">Cancel</button>
                <h2 class="font-semibold">Create new post</h2>
                <button
                    type="submit"
                    class="text-sm font-semibold text-primary hover:text-primary-dark disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Share
                </button>
            </div>

            <div v-if="preview" class="relative bg-neutral-100">
                <img :src="preview" alt="" class="max-h-[420px] w-full object-contain" />
                <button
                    type="button"
                    class="absolute right-3 top-3 rounded-full bg-black/60 p-1.5 text-white hover:bg-black/80"
                    title="Remove photo"
                    @click="removeImage"
                >
                    <Icon name="close" class="h-4 w-4" />
                </button>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center gap-3 border-b border-neutral-200 py-10"
            >
                <Icon name="photo" class="h-14 w-14 text-neutral-800" />
                <p class="text-neutral-600">Add a photo to your post (optional)</p>
                <button type="button" class="btn-primary" @click="fileInput.click()">
                    Select from computer
                </button>
            </div>

            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="pickImage"
            />
            <InputError class="px-4 pt-2" :message="form.errors.image" />

            <div class="p-4">
                <div class="mb-2 flex items-center gap-2">
                    <Avatar :user="user" :size="28" />
                    <span class="text-sm font-semibold">{{ user.name }}</span>
                </div>
                <textarea
                    v-model="form.content"
                    rows="4"
                    maxlength="2000"
                    class="block w-full resize-none border-0 p-0 text-sm focus:ring-0"
                    placeholder="Write a caption..."
                />
                <div class="mt-1 flex justify-between">
                    <InputError :message="form.errors.content" />
                    <span class="ms-auto text-xs text-neutral-400">
                        {{ form.content.length }}/2000
                    </span>
                </div>
            </div>
        </form>
    </Modal>
</template>
