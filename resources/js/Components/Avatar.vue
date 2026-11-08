<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    size: {
        type: Number,
        default: 32,
    },
});

// there are no profile pictures, so every user gets a color based on id
const colors = [
    'bg-rose-200 text-rose-800',
    'bg-amber-200 text-amber-800',
    'bg-emerald-200 text-emerald-800',
    'bg-sky-200 text-sky-800',
    'bg-violet-200 text-violet-800',
    'bg-orange-200 text-orange-800',
    'bg-teal-200 text-teal-800',
];

const initials = computed(() => {
    const parts = (props.user.name || '?').trim().split(/\s+/);
    const first = parts[0]?.[0] || '';
    const second = parts.length > 1 ? parts[parts.length - 1][0] : '';

    return (first + second).toUpperCase();
});

const color = computed(() => colors[(props.user.id || 0) % colors.length]);
</script>

<template>
    <span
        class="inline-flex shrink-0 select-none items-center justify-center rounded-full font-semibold"
        :class="color"
        :style="{
            width: size + 'px',
            height: size + 'px',
            fontSize: Math.round(size * 0.38) + 'px',
        }"
    >
        {{ initials }}
    </span>
</template>
