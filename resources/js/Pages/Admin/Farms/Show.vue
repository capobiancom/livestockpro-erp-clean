<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between w-full">
                <div>
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('admin.farms.index')"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Farms
                        </Link>

                        <span class="text-gray-300">/</span>

                        <span class="text-sm font-semibold text-gray-900">
                            {{ farm.name }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 mt-1">
                        Farm details and user login identifiers (email) + roles
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span
                        :class="[
                            'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border',
                            farm.is_active
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-rose-50 text-rose-700 border-rose-200',
                        ]"
                    >
                        <span
                            :class="[
                                'h-1.5 w-1.5 rounded-full',
                                farm.is_active
                                    ? 'bg-emerald-500'
                                    : 'bg-rose-500',
                            ]"
                        ></span>
                        {{ farm.is_active ? "Active" : "Inactive" }}
                    </span>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Summary cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5"
                >
                    <div class="text-xs font-semibold text-gray-500">
                        Subscription Plan
                    </div>
                    <div class="mt-1 text-lg font-bold text-gray-900">
                        {{ farm.subscription?.plan?.name ?? "—" }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold">Starts:</span>
                        {{ farm.subscription?.starts_on ?? "—" }}
                        <span class="mx-2 text-gray-300">•</span>
                        <span class="font-semibold">Ends:</span>
                        {{ farm.subscription?.ends_on ?? "—" }}
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        {{
                            farm.subscription?.is_active
                                ? "Subscription active"
                                : farm.subscription
                                  ? "Subscription inactive"
                                  : "No subscription"
                        }}
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5"
                >
                    <div class="text-xs font-semibold text-gray-500">
                        Farm Metadata
                    </div>
                    <div class="mt-2 text-sm text-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Farm ID</span>
                            <span class="font-semibold text-gray-900">{{
                                farm.id
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-gray-500">Created</span>
                            <span class="font-semibold text-gray-900">{{
                                formatDateTime(farm.created_at)
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-gray-500">Updated</span>
                            <span class="font-semibold text-gray-900">{{
                                formatDateTime(farm.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-900 to-gray-800 shadow-sm p-5 text-white"
                >
                    <div class="text-xs font-semibold text-white/70">
                        Users on this farm
                    </div>
                    <div class="mt-1 text-3xl font-extrabold">
                        {{ users?.length ?? 0 }}
                    </div>
                    <div class="mt-2 text-sm text-white/70">
                        Includes farm owner/admin/staff accounts.
                    </div>
                </div>
            </div>

            <!-- Users table -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
            >
                <div class="p-5 sm:p-6 border-b border-gray-100">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-4"
                    >
                        <div class="flex-1">
                            <h2 class="text-lg font-bold text-gray-900">
                                Users & Credentials
                            </h2>
                            <p class="text-sm text-gray-500">
                                For security, passwords are never shown. Email
                                is the login identifier.
                            </p>
                        </div>

                        <div class="w-full sm:w-80">
                            <div class="relative">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search users..."
                                    class="w-full rounded-xl border border-gray-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 px-4 py-2.5 pl-10 outline-none"
                                />
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    User
                                </th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Email (Login)
                                </th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Roles
                                </th>
                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                >
                                    Created
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="u in filteredUsers"
                                :key="u.id"
                                class="hover:bg-gray-50/70 transition"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center font-bold shadow-sm"
                                        >
                                            {{ initials(u.name) }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ u.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ID: {{ u.id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ u.email }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="r in u.roles || []"
                                            :key="r"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100"
                                        >
                                            {{ r }}
                                        </span>
                                        <span
                                            v-if="(u.roles || []).length === 0"
                                            class="text-sm text-gray-500"
                                        >
                                            —
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="text-sm text-gray-700">
                                        {{ formatDateTime(u.created_at) }}
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="4" class="px-5 py-10 text-center">
                                    <div class="text-gray-900 font-semibold">
                                        No users found
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Try a different search query.
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-5 sm:p-6 bg-gray-50 border-t border-gray-100">
                    <div class="text-xs text-gray-500">
                        Access restricted to Super Admin.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    farm: { type: Object, required: true },
    users: { type: Array, default: () => [] },
});

const query = ref("");

const filteredUsers = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return props.users;

    return props.users.filter((u) => {
        const name = (u.name ?? "").toLowerCase();
        const email = (u.email ?? "").toLowerCase();
        const roles = (u.roles ?? []).join(" ").toLowerCase();
        return (
            name.includes(q) ||
            email.includes(q) ||
            roles.includes(q) ||
            String(u.id).includes(q)
        );
    });
});

function initials(name) {
    const parts = String(name || "")
        .trim()
        .split(/\s+/)
        .filter(Boolean);

    if (parts.length === 0) return "U";
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
}

function formatDateTime(value) {
    if (!value) return "—";
    try {
        return new Date(value).toLocaleString();
    } catch (e) {
        return String(value);
    }
}
</script>
