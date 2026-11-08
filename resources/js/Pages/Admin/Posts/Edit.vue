<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    post: Object,
});

const form = useForm({
    content: props.post.content,
    image: null,
    remove_image: false,
    _method: 'put',
});

function pickImage(event) {
    form.image = event.target.files[0] || null;
}

function submit() {
    form.post(route('admin.posts.update', props.post.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <AdminLayout title="Edit post">
        <form class="card max-w-xl space-y-4 p-6" @submit.prevent="submit">
            <p v-if="post.author" class="text-sm text-neutral-500">
                Posted by <span class="font-semibold text-neutral-900">{{ post.author }}</span>
            </p>

            <div>
                <textarea v-model="form.content" rows="5" maxlength="2000" class="input resize-none" />
                <InputError class="mt-1" :message="form.errors.content" />
            </div>

            <img
                v-if="post.image && !form.remove_image"
                :src="'/storage/' + post.image"
                alt=""
                class="max-h-64 rounded-lg"
            />

            <label v-if="post.image" class="flex items-center gap-2 text-sm">
                <input v-model="form.remove_image" type="checkbox" class="rounded border-neutral-300 text-primary focus:ring-primary" />
                Remove photo
            </label>

            <div>
                <input type="file" accept="image/*" class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-neutral-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold" @change="pickImage" />
                <InputError class="mt-1" :message="form.errors.image" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Link :href="route('admin.posts.index')" class="text-sm text-neutral-500 hover:text-neutral-900">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>
