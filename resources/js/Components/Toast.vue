<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
    },
    duration: {
        type: Number,
        default: 3000,
    },
});

const emit = defineEmits(['close']);
const show = ref(true);

const typeClasses = computed(() => {
    return {
        success: 'bg-green-50 text-green-800 border-green-200',
        error: 'bg-red-50 text-red-800 border-red-200',
        warning: 'bg-yellow-50 text-yellow-800 border-yellow-200',
        info: 'bg-blue-50 text-blue-800 border-blue-200',
    }[props.type];
});

const iconClasses = computed(() => {
    return {
        success: 'text-green-400',
        error: 'text-red-400',
        warning: 'text-yellow-400',
        info: 'text-blue-400',
    }[props.type];
});

onMounted(() => {
    if (props.duration > 0) {
        setTimeout(() => {
            close();
        }, props.duration);
    }
});

const close = () => {
    show.value = false;
    setTimeout(() => {
        emit('close');
    }, 300);
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="opacity-100 translate-y-0 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 sm:translate-x-0"
        leave-to-class="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
    >
        <div
            v-if="show"
            :class="[
                'pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg border shadow-lg',
                typeClasses,
            ]"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg
                            v-if="type === 'success'"
                            class="h-6 w-6"
                            :class="iconClasses"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <!-- Error Icon -->
                        <svg
                            v-else-if="type === 'error'"
                            class="h-6 w-6"
                            :class="iconClasses"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <!-- Warning Icon -->
                        <svg
                            v-else-if="type === 'warning'"
                            class="h-6 w-6"
                            :class="iconClasses"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                            />
                        </svg>
                        <!-- Info Icon -->
                        <svg
                            v-else
                            class="h-6 w-6"
                            :class="iconClasses"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"
                            />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button
                            @click="close"
                            class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="[
                                type === 'success'
                                    ? 'text-green-500 hover:text-green-600 focus:ring-green-500'
                                    : type === 'error'
                                    ? 'text-red-500 hover:text-red-600 focus:ring-red-500'
                                    : type === 'warning'
                                    ? 'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500'
                                    : 'text-blue-500 hover:text-blue-600 focus:ring-blue-500',
                            ]"
                        >
                            <span class="sr-only">Close</span>
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

