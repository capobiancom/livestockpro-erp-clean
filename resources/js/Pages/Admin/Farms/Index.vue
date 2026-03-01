<template>
    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">
                        Farms Directory
                    </h1>
                    <p class="text-sm text-gray-500">
                        Super Admin only • View all farms and open details
                    </p>
                </div>

                <div class="hidden sm:flex items-center gap-2">
                    <div
                        class="px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100"
                    >
                        Total: {{ farms?.length ?? 0 }}
                    </div>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Hero / Search -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
            >
                <div
                    class="p-5 sm:p-6 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center gap-4"
                    >
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 rounded-xl bg-white/10 ring-1 ring-white/15 flex items-center justify-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-white"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M10 2a6 6 0 00-6 6v1.586l-.707.707A1 1 0 004 12h12a1 1 0 00.707-1.707L16 9.586V8a6 6 0 00-6-6z"
                                        />
                                        <path d="M8 14a2 2 0 104 0H8z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-white font-semibold">
                                        Manage farms at a glance
                                    </h2>
                                    <p class="text-white/70 text-sm">
                                        Search by farm name, plan, or status.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full sm:w-96">
                            <div class="relative">
                                <input
                                    v-model="query"
                                    type="text"
                                    placeholder="Search farms..."
                                    class="w-full rounded-xl bg-white/10 text-white placeholder-white/60 border border-white/15 focus:border-white/30 focus:ring-2 focus:ring-white/20 px-4 py-2.5 pl-10 outline-none"
                                />
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white/70 absolute left-3 top-1/2 -translate-y-1/2"
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

                <div class="p-5 sm:p-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div
                            class="rounded-xl border border-gray-200 bg-gradient-to-b from-white to-gray-50 p-4"
                        >
                            <div class="text-xs font-semibold text-gray-500">
                                Active farms
                            </div>
                            <div class="mt-1 text-2xl font-bold text-gray-900">
                                {{ activeCount }}
                            </div>
                        </div>
                        <div
                            class="rounded-xl border border-gray-200 bg-gradient-to-b from-white to-gray-50 p-4"
                        >
                            <div class="text-xs font-semibold text-gray-500">
                                Inactive farms
                            </div>
                            <div class="mt-1 text-2xl font-bold text-gray-900">
                                {{ inactiveCount }}
                            </div>
                        </div>
                        <div
                            class="rounded-xl border border-gray-200 bg-gradient-to-b from-white to-gray-50 p-4"
                        >
                            <div class="text-xs font-semibold text-gray-500">
                                Total users (all farms)
                            </div>
                            <div class="mt-1 text-2xl font-bold text-gray-900">
                                {{ totalUsers }}
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div
                        class="mt-6 overflow-hidden rounded-2xl border border-gray-200"
                    >
                        <div class="overflow-x-auto bg-white">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            Farm
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            Plan
                                        </th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            Users
                                        </th>
                                        <th class="px-5 py-3"></th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    <tr
                                        v-for="farm in filteredFarms"
                                        :key="farm.id"
                                        class="hover:bg-gray-50/70 transition"
                                    >
                                        <td class="px-5 py-4">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div
                                                    class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center font-bold shadow-sm"
                                                >
                                                    {{ initials(farm.name) }}
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-semibold text-gray-900"
                                                    >
                                                        {{ farm.name }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500"
                                                    >
                                                        ID: {{ farm.id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-5 py-4">
                                            <span
                                                :class="[
                                                    'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border',
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
                                                {{
                                                    farm.is_active
                                                        ? "Active"
                                                        : "Inactive"
                                                }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-4">
                                            <div
                                                class="text-sm text-gray-900 font-medium"
                                            >
                                                {{
                                                    farm.subscription?.plan
                                                        ?.name ?? "—"
                                                }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{
                                                    farm.subscription?.is_active
                                                        ? "Subscription active"
                                                        : farm.subscription
                                                          ? "Subscription inactive"
                                                          : "No subscription"
                                                }}
                                            </div>
                                        </td>

                                        <td class="px-5 py-4">
                                            <div
                                                class="text-sm font-semibold text-gray-900"
                                            >
                                                {{ farm.users_count ?? 0 }}
                                            </div>
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.farms.show',
                                                        farm.id,
                                                    )
                                                "
                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold shadow-sm hover:bg-gray-800 transition"
                                            >
                                                View
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10.293 15.707a1 1 0 010-1.414L13.586 11H4a1 1 0 110-2h9.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </Link>
                                        </td>
                                    </tr>

                                    <tr v-if="filteredFarms.length === 0">
                                        <td
                                            colspan="5"
                                            class="px-5 py-10 text-center"
                                        >
                                            <div
                                                class="text-gray-900 font-semibold"
                                            >
                                                No farms found
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Try a different search query.
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-gray-500">
                        Note: This directory is restricted to Super Admin.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Pages/Layout/AppLayout.vue";

const props = defineProps({
    farms: {
        type: Array,
        default: () => [],
    },
});

const query = ref("");

const filteredFarms = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return props.farms;

    return props.farms.filter((f) => {
        const name = (f.name ?? "").toLowerCase();
        const plan = (f.subscription?.plan?.name ?? "").toLowerCase();
        const status = f.is_active ? "active" : "inactive";
        return (
            name.includes(q) ||
            plan.includes(q) ||
            status.includes(q) ||
            String(f.id).includes(q)
        );
    });
});

const activeCount = computed(
    () => props.farms.filter((f) => !!f.is_active).length,
);
const inactiveCount = computed(
    () => props.farms.filter((f) => !f.is_active).length,
);
const totalUsers = computed(() =>
    props.farms.reduce((sum, f) => sum + (Number(f.users_count) || 0), 0),
);

function initials(name) {
    const parts = String(name || "")
        .trim()
        .split(/\s+/)
        .filter(Boolean);

    if (parts.length === 0) return "F";
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
}
</script>
