<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
});

const showDeleteConfirmation = ref(false);

const openDeleteConfirmation = () => {
    showDeleteConfirmation.value = true;
};

const closeDeleteConfirmation = () => {
    showDeleteConfirmation.value = false;
};

const confirmDelete = () => {
    router.delete(route('users.destroy', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route('users.index'));
        },
    });
};
</script>

<template>
    <Head :title="`User: ${user.first_name} ${user.last_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    User Details
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('users.edit', user.id)"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Edit User
                    </Link>
                    <button
                        @click="openDeleteConfirmation"
                        class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                    >
                        Delete User
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <Link
                                :href="route('users.index')"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                ← Back to Users
                            </Link>
                        </div>

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
                                            {{ user.first_name }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Last Name
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.last_name }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Email
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.email }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            User ID
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.id }}
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
                                    v-if="user.address"
                                    class="space-y-4"
                                >
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Street
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.address.street }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            City
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.address.city }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Post Code
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.address.post_code }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">
                                            Country
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ user.address.country }}
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmationModal
            :show="showDeleteConfirmation"
            title="Delete User"
            :message="`Are you sure you want to delete ${user.first_name} ${user.last_name}? This action cannot be undone.`"
            confirm-text="Delete User"
            @close="closeDeleteConfirmation"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>

