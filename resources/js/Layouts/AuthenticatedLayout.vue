<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Avatar from '@/Components/Avatar.vue';
import CreatePostModal from '@/Components/CreatePostModal.vue';
import Icon from '@/Components/Icon.vue';
import { useEcho } from '@laravel/echo-vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isModerator = computed(() => ['admin', 'manager'].includes(user.value.role));

const showCreate = ref(false);
const showMenu = ref(false);
const newMessages = ref(0);

const unread = computed(() => (user.value.unread_messages || 0) + newMessages.value);

// count messages that arrive while the user is on another page
useEcho(`chat.${page.props.auth.user.id}`, '.message.sent', (payload) => {
    const message = payload?.message;

    if (!message || message.receiver_id !== user.value.id) {
        return;
    }

    const chatIsOpen =
        page.component === 'Messages/Show' &&
        Number(page.props.other?.id) === Number(message.sender_id);

    if (!chatIsOpen) {
        newMessages.value += 1;
    }
});

const toast = ref('');
let toastTimer = null;

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash?.success) {
            return;
        }

        toast.value = flash.success;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => (toast.value = ''), 3000);
    },
    { immediate: true },
);

function isProfilePage() {
    return route().current('users.show', { user: user.value.id });
}
</script>

<template>
    <div class="min-h-screen bg-white">
        <!-- desktop sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 hidden w-[72px] flex-col border-r border-neutral-200 bg-white px-3 pb-5 pt-8 md:flex xl:w-60"
        >
            <Link :href="route('dashboard')" class="mb-8 px-3">
                <ApplicationLogo class="hidden text-3xl xl:block" />
                <span class="font-logo text-3xl xl:hidden">S</span>
            </Link>

            <nav class="flex flex-1 flex-col gap-1">
                <Link
                    :href="route('dashboard')"
                    class="sidebar-link"
                    :class="{ 'font-bold': route().current('dashboard') }"
                >
                    <Icon name="home" class="h-6 w-6" :stroke-width="route().current('dashboard') ? 2.5 : 1.8" />
                    <span class="hidden xl:inline">Home</span>
                </Link>

                <Link
                    :href="route('users.index')"
                    class="sidebar-link"
                    :class="{ 'font-bold': route().current('users.index') }"
                >
                    <Icon name="search" class="h-6 w-6" />
                    <span class="hidden xl:inline">Search</span>
                </Link>

                <Link
                    :href="route('messages.index')"
                    class="sidebar-link"
                    :class="{ 'font-bold': route().current('messages.*') }"
                >
                    <span class="relative">
                        <Icon name="send" class="h-6 w-6 -rotate-12" />
                        <span v-if="unread" class="badge absolute -right-2 -top-2">{{ unread }}</span>
                    </span>
                    <span class="hidden xl:inline">Messages</span>
                </Link>

                <button type="button" class="sidebar-link" @click="showCreate = true">
                    <Icon name="plus" class="h-6 w-6" />
                    <span class="hidden xl:inline">Create</span>
                </button>

                <Link
                    :href="route('users.show', user.id)"
                    class="sidebar-link"
                    :class="{ 'font-bold': isProfilePage() }"
                >
                    <Avatar
                        :user="user"
                        :size="24"
                        :class="{ 'ring-2 ring-neutral-900 ring-offset-1': isProfilePage() }"
                    />
                    <span class="hidden xl:inline">Profile</span>
                </Link>

                <Link
                    v-if="isModerator"
                    :href="route('admin.dashboard')"
                    class="sidebar-link"
                    :class="{ 'font-bold': route().current('admin.*') }"
                >
                    <Icon name="shield" class="h-6 w-6" />
                    <span class="hidden xl:inline">Admin</span>
                </Link>
            </nav>

            <div class="relative">
                <div
                    v-if="showMenu"
                    class="absolute bottom-14 left-0 z-40 w-56 overflow-hidden rounded-xl border border-neutral-200 bg-white py-1 shadow-lg"
                >
                    <Link :href="route('profile.edit')" class="menu-item">Settings</Link>
                    <Link :href="route('logout')" method="post" as="button" class="menu-item">
                        Log out
                    </Link>
                </div>
                <button type="button" class="sidebar-link w-full" @click="showMenu = !showMenu">
                    <Icon name="menu" class="h-6 w-6" />
                    <span class="hidden xl:inline">More</span>
                </button>
            </div>
        </aside>

        <!-- mobile top bar -->
        <header
            class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-neutral-200 bg-white px-4 md:hidden"
        >
            <Link :href="route('dashboard')">
                <ApplicationLogo class="text-[28px]" />
            </Link>

            <div class="relative flex items-center gap-4">
                <Link :href="route('messages.index')" class="relative">
                    <Icon name="send" class="h-6 w-6 -rotate-12" />
                    <span v-if="unread" class="badge absolute -right-2 -top-2">{{ unread }}</span>
                </Link>
                <button type="button" @click="showMenu = !showMenu">
                    <Icon name="menu" class="h-6 w-6" />
                </button>

                <div
                    v-if="showMenu"
                    class="absolute right-0 top-9 z-40 w-48 overflow-hidden rounded-xl border border-neutral-200 bg-white py-1 shadow-lg"
                >
                    <Link v-if="isModerator" :href="route('admin.dashboard')" class="menu-item">
                        Admin
                    </Link>
                    <Link :href="route('profile.edit')" class="menu-item">Settings</Link>
                    <Link :href="route('logout')" method="post" as="button" class="menu-item">
                        Log out
                    </Link>
                </div>
            </div>
        </header>

        <!-- click anywhere to close the menu -->
        <div v-if="showMenu" class="fixed inset-0 z-20" @click="showMenu = false" />

        <main class="pb-12 md:ml-[72px] md:pb-0 xl:ml-60">
            <slot />
        </main>

        <!-- mobile bottom bar -->
        <nav
            class="fixed inset-x-0 bottom-0 z-30 flex h-12 items-center justify-around border-t border-neutral-200 bg-white md:hidden"
        >
            <Link :href="route('dashboard')" class="p-2">
                <Icon name="home" class="h-6 w-6" :stroke-width="route().current('dashboard') ? 2.5 : 1.8" />
            </Link>
            <Link :href="route('users.index')" class="p-2">
                <Icon name="search" class="h-6 w-6" :stroke-width="route().current('users.index') ? 2.5 : 1.8" />
            </Link>
            <button type="button" class="p-2" @click="showCreate = true">
                <Icon name="plus" class="h-6 w-6" />
            </button>
            <Link :href="route('users.show', user.id)" class="p-2">
                <Avatar
                    :user="user"
                    :size="26"
                    :class="{ 'ring-2 ring-neutral-900 ring-offset-1': isProfilePage() }"
                />
            </Link>
        </nav>

        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="translate-y-4 opacity-0"
            leave-active-class="transition duration-200"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div
                v-if="toast"
                class="fixed bottom-16 left-1/2 z-50 -translate-x-1/2 rounded-lg bg-neutral-900 px-4 py-2.5 text-sm text-white shadow-lg md:bottom-6"
            >
                {{ toast }}
            </div>
        </Transition>

        <CreatePostModal :show="showCreate" @close="showCreate = false" />
    </div>
</template>

<style scoped>
.sidebar-link {
    @apply flex items-center gap-4 rounded-lg p-3 text-[15px] transition hover:bg-neutral-100;
}

.menu-item {
    @apply block w-full px-4 py-3 text-left text-sm hover:bg-neutral-100;
}

.badge {
    @apply flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-like px-1 text-[11px] font-semibold text-white;
}
</style>
