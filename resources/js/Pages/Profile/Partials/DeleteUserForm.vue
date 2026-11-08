<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <h2 class="text-base font-bold">Delete account</h2>
        <p class="mt-1 text-sm text-neutral-500">
            This deletes your account together with your posts, comments and messages. It can't be undone.
        </p>

        <button type="button" class="mt-4 text-sm font-semibold text-red-500 hover:text-red-600" @click="confirmUserDeletion">
            Delete my account
        </button>

        <Modal :show="confirmingUserDeletion" max-width="md" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold">Delete your account?</h2>
                <p class="mt-1 text-sm text-neutral-500">Enter your password to confirm.</p>

                <div class="mt-5">
                    <InputLabel for="password" value="Password" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton :disabled="form.processing" @click="deleteUser">Delete account</DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
