<script setup>
import Avatar from '@/Components/Avatar.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    bio: user.bio || '',
});
</script>

<template>
    <section>
        <h2 class="mb-4 text-base font-bold">Edit profile</h2>

        <div class="mb-6 flex items-center gap-4 rounded-2xl bg-neutral-100 p-4">
            <Avatar :user="user" :size="56" />
            <div class="min-w-0">
                <p class="truncate font-bold">{{ user.name }}</p>
                <p class="truncate text-sm text-neutral-500">{{ user.email }}</p>
            </div>
        </div>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" type="text" v-model="form.name" required autocomplete="name" />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="bio" value="Bio" />
                <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="3"
                    maxlength="500"
                    class="input resize-none"
                    placeholder="Tell people a bit about yourself"
                />
                <div class="mt-1 flex">
                    <InputError :message="form.errors.bio" />
                    <span class="ms-auto text-xs text-neutral-400">{{ form.bio.length }}/500</span>
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="text-sm">
                <p>
                    Your email isn't verified yet.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="font-semibold text-primary hover:text-primary-dark"
                    >
                        Send the link again
                    </Link>
                </p>
                <p v-show="status === 'verification-link-sent'" class="mt-2 text-green-600">
                    A new verification link has been sent.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Transition leave-active-class="transition" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-neutral-500">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
