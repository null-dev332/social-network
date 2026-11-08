<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-neutral-50 px-4 py-10">
        <div class="w-full max-w-[350px]">
            <div class="border border-neutral-300 bg-white px-8 pb-6 pt-10 sm:px-10">
                <Link href="/" class="mb-8 block text-center">
                    <ApplicationLogo class="text-[52px]" />
                </Link>

                <slot />
            </div>

            <div v-if="$slots.footer" class="mt-3 border border-neutral-300 bg-white py-5 text-center text-sm">
                <slot name="footer" />
            </div>
        </div>
    </div>
</template>
