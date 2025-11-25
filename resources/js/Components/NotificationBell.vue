<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Dropdown from './Dropdown.vue';
import ToastContainer from './ToastContainer.vue';

const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:notifications']);

// Local notifications state for polling
const localNotifications = ref([...props.notifications]);

const toasts = ref([]);
const shownNotificationIds = ref(new Set());
const isUpdating = ref(false); // Flag to prevent loops during updates
const previousNotificationIds = ref(new Set()); // Track previous state
const unreadCount = computed(() => localNotifications.value.length);

// No polling - notifications are only fetched on page load/refresh

const addToast = (message, type = 'info', id = null) => {
    const toast = {
        id: id || Date.now(),
        message,
        type,
        duration: 5000, // Longer duration for notifications
    };
    toasts.value.push(toast);
};

const removeToast = (index) => {
    toasts.value.splice(index, 1);
};

// Format time ago
const timeAgo = (date) => {
    const now = new Date();
    const notificationDate = new Date(date);
    const seconds = Math.floor((now - notificationDate) / 1000);
    
    if (seconds < 60) return 'just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
};

// Mark notification as read
const markAsRead = async (notificationId) => {
    if (isUpdating.value) return;
    
    isUpdating.value = true;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            isUpdating.value = false;
            return;
        }

        const response = await fetch('/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                notification_ids: [notificationId],
            }),
        });
        
        if (response.ok) {
            const data = await response.json();
            // Update local notifications from response
            localNotifications.value = data.notifications || [];
            // Also update props via router.reload (only when user action, not polling)
            router.reload({
                only: ['notifications'],
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    isUpdating.value = false;
                },
            });
        } else {
            isUpdating.value = false;
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
        isUpdating.value = false;
    }
};

// Dismiss notification
const dismissNotification = async (notificationId) => {
    if (isUpdating.value) return;
    
    isUpdating.value = true;
    // Remove from shown list immediately
    shownNotificationIds.value.delete(notificationId);
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            isUpdating.value = false;
            return;
        }

        const response = await fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });
        
        if (response.ok) {
            const data = await response.json();
            // Update local notifications from response
            localNotifications.value = data.notifications || [];
            // Also update props via router.reload (only when user action, not polling)
            router.reload({
                only: ['notifications'],
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    isUpdating.value = false;
                },
            });
        } else {
            isUpdating.value = false;
        }
    } catch (error) {
        console.error('Error dismissing notification:', error);
        isUpdating.value = false;
    }
};

// Mark all as read
const markAllAsRead = async () => {
    if (isUpdating.value) return;
    
    const notificationIds = localNotifications.value.map(n => n.id);
    if (notificationIds.length === 0) return;
    
    isUpdating.value = true;
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            isUpdating.value = false;
            return;
        }

        const response = await fetch('/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                notification_ids: notificationIds,
            }),
        });
        
        if (response.ok) {
            const data = await response.json();
            // Update local notifications from response
            localNotifications.value = data.notifications || [];
            // Also update props via router.reload (only when user action, not polling)
            router.reload({
                only: ['notifications'],
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    isUpdating.value = false;
                },
            });
        } else {
            isUpdating.value = false;
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
        isUpdating.value = false;
    }
};

// Show new notifications as toasts
const showNewNotifications = () => {
    // Don't show toasts if we're updating (marking as read/dismissing)
    if (isUpdating.value) return;
    
    const currentIds = new Set(localNotifications.value.map(n => n.id));
    
    // Find truly new notifications (exist now but didn't exist before)
    localNotifications.value.forEach((notification) => {
        const isNew = !previousNotificationIds.value.has(notification.id);
        const notShown = !shownNotificationIds.value.has(notification.id);
        const isUnread = !notification.read;
        
        // Only show if it's a new notification, not shown before, and unread
        if (isNew && notShown && isUnread) {
            addToast(notification.message, 'info', notification.id);
            shownNotificationIds.value.add(notification.id);
        }
    });
    
    // Update previous IDs
    previousNotificationIds.value = currentIds;
};

// No polling - notifications are fetched only on page load/refresh

onMounted(() => {
    // Initialize tracking from props (notifications loaded on page refresh)
    props.notifications.forEach(n => {
        shownNotificationIds.value.add(n.id);
        previousNotificationIds.value.add(n.id);
    });
    localNotifications.value = [...props.notifications];
    
    // Show toasts for initial notifications
    props.notifications.forEach((notification) => {
        if (!notification.read) {
            addToast(notification.message, 'info', notification.id);
        }
    });
});

// Watch for prop changes and update local state (when page refreshes or user actions update notifications)
watch(
    () => props.notifications,
    (newNotifications) => {
        localNotifications.value = [...newNotifications];
        // Update tracking
        newNotifications.forEach(n => {
            if (!previousNotificationIds.value.has(n.id)) {
                previousNotificationIds.value.add(n.id);
                shownNotificationIds.value.add(n.id);
            }
        });
    },
    { deep: false } // Shallow watch to avoid unnecessary triggers
);
</script>

<template>
    <div class="relative">
        <Dropdown align="right" width="96">
            <template #trigger>
                <button
                    type="button"
                    class="relative rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <span class="sr-only">View notifications</span>
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                        />
                    </svg>
                    <span
                        v-if="unreadCount > 0"
                        class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"
                    />
                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"
                    >
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </span>
                </button>
            </template>

            <template #content>
                <div class="w-96 max-h-96 overflow-y-auto">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Notifications
                        </h3>
                        <button
                            v-if="unreadCount > 0"
                            @click.stop="markAllAsRead"
                            class="text-xs text-indigo-600 hover:text-indigo-900 font-medium"
                        >
                            Mark all as read
                        </button>
                    </div>

                    <!-- Notifications List -->
                    <div v-if="localNotifications.length > 0" class="divide-y divide-gray-200">
                        <div
                            v-for="notification in localNotifications"
                            :key="notification.id"
                            class="px-4 py-3 hover:bg-gray-50 transition-colors"
                            :class="{
                                'bg-blue-50': !notification.read,
                            }"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-2">
                                        <span
                                            v-if="!notification.read"
                                            class="mt-1.5 h-2 w-2 rounded-full bg-indigo-600 flex-shrink-0"
                                        />
                                        <div class="flex-1">
                                            <p
                                                class="text-sm"
                                                :class="{
                                                    'font-semibold text-gray-900': !notification.read,
                                                    'text-gray-700': notification.read,
                                                }"
                                            >
                                                {{ notification.message }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ timeAgo(notification.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                    <div class="ml-4 flex items-center gap-2">
                                    <button
                                        @click.stop="markAsRead(notification.id)"
                                        class="p-1 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded"
                                        title="Mark as read"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click.stop="dismissNotification(notification.id)"
                                        class="p-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded"
                                        title="Dismiss"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="px-4 py-8 text-center"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">
                            No new notifications
                        </p>
                    </div>
                </div>
            </template>
        </Dropdown>

        <!-- Toast Notifications (rendered at root level) -->
        <Teleport to="body">
            <ToastContainer :toasts="toasts" @remove="removeToast" />
        </Teleport>
    </div>
</template>

