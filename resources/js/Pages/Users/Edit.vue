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
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold text-gray-900">
                    Edit User
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white/80 backdrop-blur-sm shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-200/50"
                >
                    <div class="p-8 text-gray-900">
                        <div class="mb-8">
                            <Link
                                :href="route('users.index')"
                                class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                            >
                                <i class="fas fa-arrow-left"></i>
                                Back to Users Dashboard
                            </Link>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- User Information -->
                            <div class="mb-8">
                                <h3 class="mb-6 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-indigo-700 text-white">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.first_name,
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.last_name,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.last_name"
                                            class="mt-1 text-sm text-red-600"
                                        >
                                            {{ form.errors.last_name }}
                                        </p>
                                    </div>

                                    <div class="md:col-span-1">
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.email,
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
                            <div class="mb-8">
                                <h3 class="mb-6 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-indigo-700 text-white">
                                        <i class="fas fa-map-marker-alt text-sm"></i>
                                    </div>
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors['address.street'],
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors['address.city'],
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors['address.post_code'],
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
                                            class="mt-2 block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{
                                                'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors['address.country'],
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
                            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200/50">
                                <Link
                                    :href="route('users.index')"
                                    class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-all duration-200"
                                >
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 hover:shadow-indigo-600/40 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                >
                                    <i v-if="form.processing" class="fas fa-spinner fa-spin"></i>
                                    <i v-else class="fas fa-save"></i>
                                    {{ form.processing ? 'Updating...' : 'Update User' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

