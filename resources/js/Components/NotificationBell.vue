<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Dropdown from './Dropdown.vue';

const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:notifications']);

// Local notifications state
const localNotifications = ref([...props.notifications]);

const shownNotificationIds = ref(new Set());
const isUpdating = ref(false); // Flag to prevent loops during updates
const previousNotificationIds = ref(new Set()); // Track previous state
const unreadCount = computed(() => localNotifications.value.length);

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

// Notifications are only shown in the dropdown, not as toasts

// No polling - notifications are fetched only on page load/refresh

onMounted(() => {
    // Initialize tracking from props (notifications loaded on page refresh)
    props.notifications.forEach(n => {
        shownNotificationIds.value.add(n.id);
        previousNotificationIds.value.add(n.id);
    });
    localNotifications.value = [...props.notifications];
    
    // Don't show toasts automatically - notifications only visible in dropdown
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
    <div class="relative z-[10000]">
        <Dropdown align="right" width="96">
            <template #trigger>
                <button
                    type="button"
                    class="relative rounded-full bg-white p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-all duration-200 border-0"
                >
                    <span class="sr-only">View notifications</span>
                    <i class="fas fa-bell text-lg"></i>
                    <span
                        v-if="unreadCount > 0"
                        class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"
                    />
                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-red-600 text-xs font-bold text-white shadow-lg"
                    >
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </span>
                </button>
            </template>

            <template #content>
                <div class="w-96 max-h-96 overflow-y-auto bg-white rounded-lg shadow-xl border border-gray-200/50">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-gray-200/50 bg-gradient-to-r from-gray-50 to-white px-5 py-4">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-bell text-indigo-600"></i>
                            Notifications
                        </h3>
                        <button
                            v-if="unreadCount > 0"
                            @click.stop="markAllAsRead"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition-colors flex items-center gap-1"
                        >
                            <i class="fas fa-check-double"></i>
                            Mark all as read
                        </button>
                    </div>

                    <!-- Notifications List -->
                    <div v-if="localNotifications.length > 0" class="divide-y divide-gray-200/50">
                        <div
                            v-for="notification in localNotifications"
                            :key="notification.id"
                            class="px-5 py-4 hover:bg-indigo-50/30 transition-colors"
                            :class="{
                                'bg-gradient-to-r from-indigo-50/50 to-white': !notification.read,
                            }"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3">
                                        <div
                                            v-if="!notification.read"
                                            class="mt-1.5 flex-shrink-0"
                                        >
                                            <div class="h-2.5 w-2.5 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-700"></div>
                                        </div>
                                        <div v-else class="mt-1.5 flex-shrink-0 w-2.5"></div>
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm leading-relaxed"
                                                :class="{
                                                    'font-semibold text-gray-900': !notification.read,
                                                    'text-gray-700': notification.read,
                                                }"
                                            >
                                                {{ notification.message }}
                                            </p>
                                            <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                                                <i class="fas fa-clock text-gray-400"></i>
                                                {{ timeAgo(notification.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center gap-1.5">
                                    <button
                                        @click.stop="markAsRead(notification.id)"
                                        class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-100 rounded-lg transition-all duration-200"
                                        title="Mark as read"
                                    >
                                        <i class="fas fa-check text-sm"></i>
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
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mx-auto mb-3">
                            <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900">No new notifications</p>
                        <p class="mt-1 text-xs text-gray-500">You're all caught up!</p>
                    </div>
                </div>
            </template>
        </Dropdown>
    </div>
</template>

