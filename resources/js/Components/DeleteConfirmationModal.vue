<script setup>
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Are you sure?',
    },
    message: {
        type: String,
        default: 'This action cannot be undone.',
    },
    confirmText: {
        type: String,
        default: 'Delete',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
});

const emit = defineEmits(['close', 'confirm']);

const confirm = () => {
    emit('confirm');
};

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close" max-width="md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg
                    class="w-6 h-6 text-red-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    />
                </svg>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 text-center mb-2">
                {{ title }}
            </h2>

            <p class="text-sm text-gray-600 text-center mb-6">
                {{ message }}
            </p>

            <div class="flex items-center justify-end gap-3">
                <SecondaryButton @click="close">
                    {{ cancelText }}
                </SecondaryButton>
                <DangerButton @click="confirm">
                    {{ confirmText }}
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>

