<script setup>
import Modal from '@/Components/Modal.vue';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    userId: {
        type: Number,
        default: null,
    },
    user: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);
const userData = ref(props.user);
const showDeleteConfirmation = ref(false);

// Use user data when modal opens
watch(
    () => [props.show, props.user],
    ([show, user]) => {
        if (show && user) {
            // Always use the provided user data (it already includes address from the table)
            userData.value = user;
        } else if (!show) {
            // Reset when modal closes
            userData.value = null;
            showDeleteConfirmation.value = false;
        }
    },
    { immediate: true }
);

const close = () => {
    emit('close');
};

const openDeleteConfirmation = () => {
    showDeleteConfirmation.value = true;
};

const closeDeleteConfirmation = () => {
    showDeleteConfirmation.value = false;
};

const confirmDelete = () => {
    router.delete(route('users.destroy', props.userId), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteConfirmation();
            close();
            router.reload({ only: ['users'] });
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="close" max-width="2xl">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">
                    User Details
                </h2>
                <button
                    @click="close"
                    class="text-gray-400 hover:text-gray-500"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- User Details -->
            <div v-if="userData" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- User Information -->
                    <div>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            User Information
                        </h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    First Name
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.first_name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Last Name
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.last_name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Email
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.email }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Address Information -->
                    <div>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            Address Information
                        </h3>
                        <dl
                            v-if="userData.address"
                            class="space-y-4"
                        >
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Street
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.address.street }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    City
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.address.city }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Post Code
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.address.post_code }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Country
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ userData.address.country }}
                                </dd>
                            </div>
                        </dl>
                        <p
                            v-else
                            class="text-sm text-gray-500"
                        >
                            No address information available.
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6">
                  
                    <Link
                        :href="route('users.edit', userData.id)"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500"
                        @click="close"
                    >
                        Edit User
                    </Link>
                    <button
                        @click="openDeleteConfirmation"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-500"
                    >
                        Delete User
                    </button>

                    <button
                        @click="close"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Close
                    </button>
                </div>
            </div>

            <!-- Error State -->
            <div v-else class="py-8 text-center">
                <p class="text-sm text-red-600">Failed to load user details.</p>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmationModal
            :show="showDeleteConfirmation"
            title="Delete User"
            :message="`Are you sure you want to delete ${userData?.first_name} ${userData?.last_name}? This action cannot be undone.`"
            confirm-text="Delete User"
            @close="closeDeleteConfirmation"
            @confirm="confirmDelete"
        />
    </Modal>
</template>

