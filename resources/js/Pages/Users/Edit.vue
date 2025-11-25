<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email || '',
    address: {
        country: props.user.address?.country || '',
        city: props.user.address?.city || '',
        post_code: props.user.address?.post_code || '',
        street: props.user.address?.street || '',
    },
});

const submit = () => {
    form.put(route('users.update', props.user.id), {
        onSuccess: () => {
            // Redirect to dashboard is handled by the controller
        },
    });
};
</script>

<template>
    <Head :title="`Edit User: ${user.first_name} ${user.last_name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <Link
                                :href="route('users.show', user.id)"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                ← Back to User Details
                            </Link>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- User Information -->
                            <div class="mb-6">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900">
                                    User Information
                                </h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <label
                                            for="first_name"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            First Name *
                                        </label>
                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors.first_name,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.first_name"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.first_name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="last_name"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Last Name *
                                        </label>
                                        <input
                                            id="last_name"
                                            v-model="form.last_name"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors.last_name,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.last_name"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.last_name }}
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label
                                            for="email"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Email *
                                        </label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors.email,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.email"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.email }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="mb-6">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900">
                                    Address Information
                                </h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <label
                                            for="street"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Street *
                                        </label>
                                        <input
                                            id="street"
                                            v-model="form.address.street"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors['address.street'],
                                            }"
                                        />
                                        <p
                                            v-if="form.errors['address.street']"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors['address.street'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="city"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            City *
                                        </label>
                                        <input
                                            id="city"
                                            v-model="form.address.city"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors['address.city'],
                                            }"
                                        />
                                        <p
                                            v-if="form.errors['address.city']"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors['address.city'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="post_code"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Post Code *
                                        </label>
                                        <input
                                            id="post_code"
                                            v-model="form.address.post_code"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors['address.post_code'],
                                            }"
                                        />
                                        <p
                                            v-if="form.errors['address.post_code']"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors['address.post_code'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            for="country"
                                            class="block text-sm font-medium text-gray-700"
                                        >
                                            Country *
                                        </label>
                                        <input
                                            id="country"
                                            v-model="form.address.country"
                                            type="text"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{
                                                'border-red-500': form.errors['address.country'],
                                            }"
                                        />
                                        <p
                                            v-if="form.errors['address.country']"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors['address.country'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('users.show', user.id)"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                >
                                    Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

