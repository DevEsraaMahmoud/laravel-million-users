<script setup>
import { computed } from 'vue';
import Toast from './Toast.vue';

const props = defineProps({
    toasts: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['remove']);

const removeToast = (index) => {
    emit('remove', index);
};
</script>

<template>
    <div
        aria-live="assertive"
        class="pointer-events-none fixed inset-0 z-50 flex items-end px-4 py-6 sm:items-start sm:p-6"
    >
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <Toast
                v-for="(toast, index) in toasts"
                :key="index"
                :message="toast.message"
                :type="toast.type"
                :duration="toast.duration"
                @close="removeToast(index)"
            />
        </div>
    </div>
</template>

