<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    comment: Object,
});

const form = useForm({
    content: props.comment.content,
});

function submit() {
    form.put(route('admin.comments.update', props.comment.id));
}
</script>

<template>
    <AdminLayout title="Edit comment">
        <form class="card max-w-xl space-y-4 p-6" @submit.prevent="submit">
            <p v-if="comment.author" class="text-sm text-neutral-500">
                Written by <span class="font-semibold text-neutral-900">{{ comment.author }}</span>
            </p>

            <div>
                <textarea v-model="form.content" rows="4" maxlength="500" class="input resize-none" />
                <InputError class="mt-1" :message="form.errors.content" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Link :href="route('admin.comments.index')" class="text-sm text-neutral-500 hover:text-neutral-900">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>
