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
                'pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl border shadow-xl backdrop-blur-sm',
                typeClasses,
            ]"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <i
                            v-if="type === 'success'"
                            class="fas fa-check-circle text-xl"
                            :class="iconClasses"
                        ></i>
                        <!-- Error Icon -->
                        <i
                            v-else-if="type === 'error'"
                            class="fas fa-exclamation-circle text-xl"
                            :class="iconClasses"
                        ></i>
                        <!-- Warning Icon -->
                        <i
                            v-else-if="type === 'warning'"
                            class="fas fa-exclamation-triangle text-xl"
                            :class="iconClasses"
                        ></i>
                        <!-- Info Icon -->
                        <i
                            v-else
                            class="fas fa-info-circle text-xl"
                            :class="iconClasses"
                        ></i>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button
                            @click="close"
                            class="inline-flex rounded-lg p-1 hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                            :class="[
                                type === 'success'
                                    ? 'text-green-600 hover:text-green-700 focus:ring-green-500'
                                    : type === 'error'
                                    ? 'text-red-600 hover:text-red-700 focus:ring-red-500'
                                    : type === 'warning'
                                    ? 'text-yellow-600 hover:text-yellow-700 focus:ring-yellow-500'
                                    : 'text-blue-600 hover:text-blue-700 focus:ring-blue-500',
                            ]"
                        >
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

