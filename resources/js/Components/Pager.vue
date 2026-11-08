<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    paginator: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div
        v-if="paginator && paginator.last_page > 1"
        class="mt-6 flex items-center justify-center gap-4 text-sm"
    >
        <Link
            v-if="paginator.prev_page_url"
            :href="paginator.prev_page_url"
            class="btn-secondary"
        >
            Previous
        </Link>

        <span class="text-neutral-500">
            Page {{ paginator.current_page }} of {{ paginator.last_page }}
        </span>

        <Link
            v-if="paginator.next_page_url"
            :href="paginator.next_page_url"
            class="btn-secondary"
        >
            Next
        </Link>
    </div>
</template>
