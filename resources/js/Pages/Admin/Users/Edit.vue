<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    bio: props.user.bio || '',
    role: props.user.role,
});

function submit() {
    form.put(route('admin.users.update', props.user.id));
}
</script>

<template>
    <AdminLayout title="Edit user">
        <form class="card max-w-xl space-y-5 p-6" @submit.prevent="submit">
            <p class="rounded-lg bg-neutral-50 px-4 py-3 text-sm text-neutral-600">
                The password can't be changed here. The user keeps the password from sign up
                or from a password reset.
            </p>

            <div>
                <InputLabel for="name" value="Name" />
                <input id="name" v-model="form.name" type="text" class="input" />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <input id="email" v-model="form.email" type="email" class="input" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="bio" value="Bio" />
                <textarea id="bio" v-model="form.bio" rows="3" maxlength="500" class="input resize-none" />
                <InputError class="mt-1" :message="form.errors.bio" />
            </div>

            <div>
                <InputLabel for="role" value="Role" />
                <select id="role" v-model="form.role" class="input">
                    <option value="user">User</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
                <InputError class="mt-1" :message="form.errors.role" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Link :href="route('admin.users.index')" class="text-sm text-neutral-500 hover:text-neutral-900">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>
