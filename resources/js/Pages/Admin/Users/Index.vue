<template>
    <Head title="Users Management" />

    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                        Users
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage user roles and direct permissions.
                    </p>
                </div>

                <div class="hidden md:flex items-center gap-2">
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 shadow-sm"
                    >
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500"
                        ></span>
                        <span>{{ users.total }} total</span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Success Message -->
        <div
            v-if="$page.props.flash?.success"
            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-sm"
        >
            <div class="flex items-start gap-3">
                <div
                    class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700"
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
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-emerald-900">
                        Success
                    </p>
                    <p class="text-sm text-emerald-800">
                        {{ $page.props.flash.success }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Switch Farm Owner Dashboard (Super Admin) -->
        <div
            v-if="farmOwners && farmOwners.length"
            class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
        >
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Switch Farm Owner Dashboard
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Select a farm owner to open their farm productivity
                    dashboard.
                </p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-9">
                    <label
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Farm Owner
                    </label>
                    <select
                        v-model="selectedFarmOwnerId"
                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30"
                    >
                        <option :value="null">Select farm owner...</option>
                        <option
                            v-for="o in farmOwners"
                            :key="o.id"
                            :value="o.id"
                        >
                            {{ o.name }} — {{ o.farm?.name ?? "No farm" }}
                        </option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <button
                        @click="openFarmOwnerDashboard"
                        :disabled="!selectedFarmOwnerId"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M10.293 15.707a1 1 0 010-1.414L12.586 12H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            />
                        </svg>
                        Open Dashboard
                    </button>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div
            class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
        >
            <div
                class="p-6 border-b border-gray-100 flex items-center justify-between gap-3"
            >
                <h3 class="text-lg font-semibold text-gray-900">All Users</h3>

                <div
                    class="md:hidden inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 shadow-sm"
                >
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>{{ users.total }} total</span>
                </div>
            </div>

            <div v-if="users.data.length === 0" class="text-center py-14 px-6">
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-500"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7"
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
                <h3 class="mt-4 text-lg font-semibold text-gray-900">
                    No users found
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    No registered users in the system.
                </p>
            </div>

            <div v-else class="divide-y divide-gray-100">
                <div
                    v-for="u in users.data"
                    :key="u.id"
                    class="p-6 transition hover:bg-gray-50/70"
                >
                    <div class="flex flex-col xl:flex-row gap-6">
                        <!-- User Info -->
                        <div class="flex items-center gap-4 xl:w-[320px]">
                            <div
                                class="h-12 w-12 flex-shrink-0 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm shadow-sm"
                            >
                                {{ getInitials(u.name) }}
                            </div>
                            <div class="min-w-0">
                                <div
                                    class="font-semibold text-gray-900 truncate"
                                >
                                    {{ u.name }}
                                </div>
                                <div class="text-sm text-gray-500 truncate">
                                    {{ u.email }}
                                </div>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span
                                        v-for="r in u.roles"
                                        :key="r.id"
                                        class="inline-flex items-center rounded-full border border-gray-200 bg-white px-2 py-0.5 text-[11px] font-semibold text-gray-700"
                                    >
                                        {{ r.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Roles & Permissions -->
                        <div class="flex-1 space-y-5">
                            <!-- Roles Section -->
                            <div
                                :id="`roles-${u.id}`"
                                class="rounded-2xl border border-gray-200 bg-white p-4"
                            >
                                <div
                                    class="flex items-center justify-between gap-3 mb-3"
                                >
                                    <div>
                                        <div
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            Roles
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Assign roles to control access.
                                        </div>
                                    </div>

                                    <button
                                        @click="updateRoles(u.id)"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                                    >
                                        Save Roles
                                    </button>
                                </div>

                                <div
                                    class="grid grid-cols-2 md:grid-cols-4 gap-2"
                                >
                                    <label
                                        v-for="r in roles"
                                        :key="r.id"
                                        class="group flex items-center gap-2 rounded-xl border border-gray-200 bg-white p-2 text-sm cursor-pointer transition hover:border-indigo-200 hover:bg-indigo-50/40"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="r.name"
                                            :checked="
                                                u.roles
                                                    .map((x) => x.name)
                                                    .includes(r.name)
                                            "
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span
                                            class="min-w-0 flex-1 break-words text-gray-700 group-hover:text-gray-900"
                                            >{{ r.name }}</span
                                        >
                                    </label>
                                </div>
                            </div>

                            <!-- Permissions Section -->
                            <div
                                :id="`perms-${u.id}`"
                                class="rounded-2xl border border-gray-200 bg-white p-4"
                            >
                                <div
                                    class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-3"
                                >
                                    <div>
                                        <div
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            Direct permissions
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            These permissions apply in addition
                                            to role permissions.
                                        </div>
                                    </div>

                                    <div class="w-full md:max-w-sm">
                                        <div class="relative">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 text-gray-400"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M12.9 14.32a8 8 0 111.414-1.414l3.387 3.387a1 1 0 01-1.414 1.414l-3.387-3.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </div>

                                            <input
                                                :value="
                                                    getPermissionSearch(u.id)
                                                "
                                                @input="
                                                    setPermissionSearch(
                                                        u.id,
                                                        $event.target.value,
                                                    )
                                                "
                                                type="text"
                                                placeholder="Search permissions..."
                                                class="w-full rounded-xl border border-gray-200 bg-white pl-9 pr-20 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30"
                                            />

                                            <button
                                                v-if="getPermissionSearch(u.id)"
                                                type="button"
                                                @click="
                                                    clearPermissionSearch(u.id)
                                                "
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs font-semibold text-gray-500 hover:text-gray-700"
                                                title="Clear"
                                            >
                                                Clear
                                            </button>
                                        </div>

                                        <div
                                            class="mt-1 flex items-center justify-between text-xs text-gray-500"
                                        >
                                            <span>
                                                Showing
                                                <span
                                                    class="font-semibold text-gray-700"
                                                    >{{
                                                        filteredPermissionsForUser(
                                                            u.id,
                                                        ).length
                                                    }}</span
                                                >
                                                of
                                                <span
                                                    class="font-semibold text-gray-700"
                                                    >{{
                                                        permissions.length
                                                    }}</span
                                                >
                                            </span>

                                            <span
                                                v-if="
                                                    getPermissionSearch(u.id) &&
                                                    filteredPermissionsForUser(
                                                        u.id,
                                                    ).length === 0
                                                "
                                            >
                                                No matches
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="grid grid-cols-2 md:grid-cols-4 gap-2"
                                >
                                    <label
                                        v-for="p in filteredPermissionsForUser(
                                            u.id,
                                        )"
                                        :key="p.id"
                                        class="group flex items-center gap-2 rounded-xl border border-gray-200 bg-white p-2 text-sm cursor-pointer transition hover:border-purple-200 hover:bg-purple-50/40"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="p.name"
                                            :checked="
                                                isPermissionSelected(u, p)
                                            "
                                            @change="
                                                togglePermissionSelection(
                                                    u.id,
                                                    p.name,
                                                    $event.target.checked,
                                                )
                                            "
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        />
                                        <span
                                            class="min-w-0 flex-1 break-words text-gray-700 group-hover:text-gray-900"
                                            >{{ p.name }}</span
                                        >
                                    </label>
                                </div>

                                <div class="mt-3 flex items-center justify-end">
                                    <button
                                        @click="updatePermissions(u.id)"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-800"
                                    >
                                        Save Permissions
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            <Pagination :links="users.links" />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Pagination from "@/Pages/Shared/Pagination.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";

const props = defineProps({
    users: Object,
    roles: Array,
    permissions: Array,
    farmOwners: {
        type: Array,
        default: () => [],
    },
});

const farmOwners = props.farmOwners;

const selectedFarmOwnerId = ref(null);

// Per-user selected permissions (keeps selections even when filtering/searching)
const selectedPermissionsByUser = ref({});

// Per-user search for Direct Permissions
const permissionSearchByUser = ref({});

const getPermissionSearch = (userId) =>
    String(permissionSearchByUser.value?.[userId] ?? "");

const setPermissionSearch = (userId, value) => {
    permissionSearchByUser.value = {
        ...(permissionSearchByUser.value || {}),
        [userId]: value,
    };
};

const clearPermissionSearch = (userId) => setPermissionSearch(userId, "");

const ensureUserPermissionSelection = (user) => {
    const userId = user?.id;
    if (!userId) return;

    // Initialize once from server-provided direct permissions
    if (!Array.isArray(selectedPermissionsByUser.value?.[userId])) {
        selectedPermissionsByUser.value = {
            ...(selectedPermissionsByUser.value || {}),
            [userId]: (user.permissions || []).map((x) => x.name),
        };
    }
};

const isPermissionSelected = (user, permission) => {
    ensureUserPermissionSelection(user);
    const userId = user?.id;
    const selected = selectedPermissionsByUser.value?.[userId] || [];
    return selected.includes(permission?.name);
};

const togglePermissionSelection = (userId, permissionName, checked) => {
    const current = Array.isArray(selectedPermissionsByUser.value?.[userId])
        ? selectedPermissionsByUser.value[userId]
        : [];

    const next = checked
        ? Array.from(new Set([...current, permissionName]))
        : current.filter((x) => x !== permissionName);

    selectedPermissionsByUser.value = {
        ...(selectedPermissionsByUser.value || {}),
        [userId]: next,
    };
};

const filteredPermissionsForUser = (userId) => {
    const q = getPermissionSearch(userId).trim().toLowerCase();
    if (!q) return props.permissions || [];
    return (props.permissions || []).filter((p) =>
        String(p.name || "")
            .toLowerCase()
            .includes(q),
    );
};

const openFarmOwnerDashboard = () => {
    if (!selectedFarmOwnerId.value) return;

    const owner = props.farmOwners.find(
        (o) => String(o.id) === String(selectedFarmOwnerId.value),
    );

    if (!owner?.farm_id) return;

    window.location.href = `/farm-productivity-dashboard?farm_id=${owner.farm_id}`;
};

const getInitials = (name) => {
    const words = name.split(" ");
    if (words.length >= 2) {
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

function updateRoles(userId) {
    const checked = Array.from(
        document.querySelectorAll(
            `#roles-${userId} input[type=checkbox]:checked`,
        ),
    ).map((n) => n.value);

    const form = useForm({ roles: checked });
    form.post(route("admin.users.updateRoles", userId), {
        preserveScroll: true,
    });
}

function updatePermissions(userId) {
    const checked = Array.isArray(selectedPermissionsByUser.value?.[userId])
        ? selectedPermissionsByUser.value[userId]
        : [];

    const form = useForm({ permissions: checked });
    form.post(route("admin.users.updatePermissions", userId), {});
}
</script>
