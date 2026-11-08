<script setup>
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    post: Object,
});

const preview = ref(null);

const form = useForm({
    content: props.post.content,
    image: null,
    remove_image: false,
    _method: 'put',
});

function pickImage(event) {
    const file = event.target.files[0] || null;

    if (file && file.size > 2 * 1024 * 1024) {
        form.setError('image', 'The image can be at most 2 MB.');
        event.target.value = '';
        return;
    }

    form.image = file;
    form.remove_image = false;
    preview.value = file ? URL.createObjectURL(file) : null;
}

function submit() {
    if (!form.content.trim()) {
        form.setError('content', 'Write something first.');
        return;
    }

    // files have to be sent with POST, laravel reads _method and treats it as PUT
    form.post(route('posts.update', props.post.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Edit post" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-xl px-4 py-6 md:py-10">
            <form class="card overflow-hidden" @submit.prevent="submit">
                <div class="flex items-center justify-between border-b border-neutral-200 px-4 py-2.5">
                    <Link :href="route('posts.show', post.id)" class="text-sm">Cancel</Link>
                    <h1 class="font-semibold">Edit post</h1>
                    <button
                        type="submit"
                        class="text-sm font-semibold text-primary hover:text-primary-dark disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Done
                    </button>
                </div>

                <img v-if="preview" :src="preview" alt="" class="max-h-[420px] w-full bg-neutral-100 object-contain" />
                <img
                    v-else-if="post.image && !form.remove_image"
                    :src="'/storage/' + post.image"
                    alt=""
                    class="max-h-[420px] w-full bg-neutral-100 object-contain"
                />

                <div class="space-y-4 p-4">
                    <div>
                        <textarea
                            v-model="form.content"
                            rows="5"
                            maxlength="2000"
                            class="input resize-none"
                            placeholder="Write a caption..."
                        />
                        <InputError class="mt-1" :message="form.errors.content" />
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <label class="btn-secondary cursor-pointer">
                            {{ post.image ? 'Change photo' : 'Add photo' }}
                            <input type="file" accept="image/*" class="hidden" @change="pickImage" />
                        </label>
                        <label v-if="post.image && !preview" class="flex items-center gap-2 text-neutral-700">
                            <input v-model="form.remove_image" type="checkbox" class="rounded border-neutral-300 text-primary focus:ring-primary" />
                            Remove photo
                        </label>
                    </div>
                    <InputError :message="form.errors.image" />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
