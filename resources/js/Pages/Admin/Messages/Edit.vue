<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    message: Object,
});

const form = useForm({
    content: props.message.content,
});

function submit() {
    form.put(route('admin.messages.update', props.message.id));
}
</script>

<template>
    <AdminLayout title="Edit message">
        <form class="card max-w-xl space-y-4 p-6" @submit.prevent="submit">
            <p class="text-sm text-neutral-500">
                <span class="font-semibold text-neutral-900">{{ message.sender }}</span>
                →
                <span class="font-semibold text-neutral-900">{{ message.receiver }}</span>
            </p>

            <div>
                <textarea v-model="form.content" rows="4" maxlength="1000" class="input resize-none" />
                <InputError class="mt-1" :message="form.errors.content" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Link :href="route('admin.messages.index')" class="text-sm text-neutral-500 hover:text-neutral-900">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>
