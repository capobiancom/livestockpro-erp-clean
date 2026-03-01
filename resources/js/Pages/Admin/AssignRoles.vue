<template>
    <Head title="Assign Roles" />

    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Assign Roles to Users
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage user role assignments efficiently
                    </p>
                </div>
            </div>
        </template>

        <!-- Success Message -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg"
        >
            <div class="flex items-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"
                    />
                </svg>
                <p class="text-green-700 font-medium">
                    {{ $page.props.flash.success }}
                </p>
            </div>
        </div>

        <!-- Assign Roles Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-indigo-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                    </svg>
                    Select User & Assign Roles
                </h3>
                <p class="text-sm text-gray-600 ml-8">
                    Choose a user from the list below and assign or remove roles
                </p>
            </div>

            <form @submit.prevent="updateUserRoles" class="space-y-6">
                <!-- User Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select User
                    </label>
                    <select
                        v-model="roleForm.userId"
                        @change="loadUserRoles"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">-- Select a User --</option>
                        <option v-for="u in users" :key="u.id" :value="u.id">
                            {{ u.name }} ({{ u.email }})
                        </option>
                    </select>
                </div>

                <!-- Selected User Info Card -->
                <div v-if="selectedUser" class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 flex-shrink-0 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ getInitials(selectedUser.name) }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 text-lg">{{ selectedUser.name }}</h4>
                            <p class="text-sm text-gray-600">{{ selectedUser.email }}</p>
                            <p class="text-xs text-indigo-600 mt-1">
                                Current Roles:
                                <span v-if="selectedUser.roles && selectedUser.roles.length > 0" class="font-semibold">
                                    {{ selectedUser.roles.map(r => r.name).join(', ') }}
                                </span>
                                <span v-else class="font-semibold">No roles assigned</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div v-if="roleForm.userId">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Assign Roles
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <label
                            v-for="role in roles"
                            :key="role.id"
                            class="flex items-center gap-3 text-sm hover:bg-indigo-50 p-4 rounded-lg cursor-pointer border border-gray-200 transition-all"
                            :class="{ 'bg-indigo-50 border-indigo-300': roleForm.roles.includes(role.name) }"
                        >
                            <input
                                type="checkbox"
                                :value="role.name"
                                v-model="roleForm.roles"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5"
                            />
                            <div class="flex items-center gap-2">
                                <div class="bg-indigo-500 rounded-full p-1.5">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3 text-white"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <span class="text-gray-700 font-medium">{{ role.name }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div v-if="roleForm.userId" class="flex items-center gap-4 pt-4 border-t">
                    <button
                        type="submit"
                        :disabled="!roleForm.userId || roleForm.processing"
                        class="bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="roleForm.processing">Updating...</span>
                        <span v-else>Update User Roles</span>
                    </button>
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-if="roleForm.recentlySuccessful"
                            class="text-sm text-green-600 font-medium flex items-center gap-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Roles updated successfully!
                        </p>
                    </Transition>
                </div>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800">{{ users.length }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-blue-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Available Roles</p>
                        <p class="text-3xl font-bold text-gray-800">{{ roles.length }}</p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-indigo-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Users with Roles</p>
                        <p class="text-3xl font-bold text-gray-800">{{ usersWithRoles }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-green-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { computed } from "vue";

const props = defineProps({
    users: Array,
    roles: Array,
});

const roleForm = useForm({
    userId: '',
    roles: [],
});

const selectedUser = computed(() => {
    if (!roleForm.userId) return null;
    return props.users.find(u => u.id == roleForm.userId);
});

const usersWithRoles = computed(() => {
    return props.users.filter(u => u.roles && u.roles.length > 0).length;
});

const getInitials = (name) => {
    if (!name) return "??";
    const words = name.split(" ");
    if (words.length >= 2) {
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

// Load user's current roles when a user is selected
const loadUserRoles = () => {
    if (!roleForm.userId) {
        roleForm.roles = [];
        return;
    }

    const user = props.users.find(u => u.id == roleForm.userId);
    if (user && user.roles) {
        roleForm.roles = user.roles.map(r => r.name);
    } else {
        roleForm.roles = [];
    }
};

// Update user roles
const updateUserRoles = () => {
    if (!roleForm.userId) return;

    roleForm.post(route('admin.users.updateRoles', roleForm.userId), {
        preserveScroll: true,
        onSuccess: () => {
            // Keep the form open but show success message
        },
    });
};
</script>
