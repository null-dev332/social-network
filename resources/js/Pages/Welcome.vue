<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const user = usePage().props.auth.user;

// just a decoration that looks like a profile grid
const tiles = [
    'from-rose-200 to-orange-100',
    'from-sky-200 to-indigo-100',
    'from-amber-100 to-yellow-50',
    'from-emerald-200 to-teal-100',
    'from-violet-200 to-fuchsia-100',
    'from-orange-200 to-rose-100',
    'from-teal-100 to-sky-100',
    'from-fuchsia-100 to-rose-100',
    'from-indigo-100 to-violet-200',
];
</script>

<template>
    <Head title="Welcome" />

    <div class="flex min-h-screen items-center justify-center bg-white px-4 py-12">
        <div class="flex w-full max-w-[860px] items-center justify-center gap-16">
            <div class="hidden w-[380px] shrink-0 grid-cols-3 gap-1.5 md:grid">
                <div
                    v-for="(tile, index) in tiles"
                    :key="index"
                    class="aspect-square rounded-md bg-gradient-to-br"
                    :class="tile"
                />
            </div>

            <div class="w-full max-w-[350px] text-center">
                <ApplicationLogo class="text-6xl" />
                <p class="mt-4 text-lg text-neutral-600">
                    Share what's going on, post photos and stay in touch with your classmates.
                </p>

                <div class="mt-8 space-y-3">
                    <Link v-if="user" :href="route('dashboard')" class="btn-primary w-full py-2">
                        Open your feed
                    </Link>
                    <template v-else>
                        <Link v-if="canLogin" :href="route('login')" class="btn-primary w-full py-2">
                            Log in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')" class="btn-secondary w-full py-2">
                            Create new account
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
