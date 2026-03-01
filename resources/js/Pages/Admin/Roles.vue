<template>
    <Head title="Roles Management" />

    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Role Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Create and manage user roles and their permissions
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

        <!-- Create New Role -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                Create New Role
            </h3>
            <form @submit.prevent="submit" class="flex items-end gap-4">
                <div class="flex-1">
                    <label
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Role Name
                    </label>
                    <input
                        v-model="form.name"
                        placeholder="e.g., Manager, Veterinarian, etc."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    <p
                        v-if="form.errors.name"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-6 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50"
                >
                    <span v-if="form.processing">Creating...</span>
                    <span v-else>Create Role</span>
                </button>
            </form>
        </div>

        <!-- Existing Roles -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold text-gray-800">
                    Existing Roles ({{ roles.length }})
                </h3>
            </div>

            <div v-if="roles.length === 0" class="text-center py-12">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 mx-auto text-gray-300 mb-4"
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
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Roles Found
                </h3>
                <p class="text-gray-500">
                    Create your first role to get started with role-based access
                    control.
                </p>
            </div>

            <div v-else class="divide-y divide-gray-200">
                <div
                    v-for="role in roles"
                    :key="role.id"
                    class="p-6 hover:bg-gray-50 transition"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800"
                                >
                                    {{ role.name }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ (role.permissions || []).length }}
                                    permissions
                                </span>
                            </div>
                        </div>
                        <button
                            @click="deleteRole(role.id)"
                            class="text-red-600 hover:text-red-800 font-semibold transition flex items-center gap-1"
                            title="Delete Role"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Delete
                        </button>
                    </div>

                    <div :id="`perms-role-${role.id}`" class="mt-2">
                        <div
                            class="flex items-center justify-between gap-4 mb-3"
                        >
                            <div class="text-sm font-semibold text-gray-700">
                                Assign Permissions
                            </div>

                            <div class="w-full max-w-md">
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
                                        :value="getRoleQuery(role.id)"
                                        @input="
                                            setRoleQuery(
                                                role.id,
                                                $event.target.value,
                                            )
                                        "
                                        type="text"
                                        placeholder="Search permissions..."
                                        class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-20 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30"
                                    />

                                    <button
                                        v-if="getRoleQuery(role.id)"
                                        type="button"
                                        @click="setRoleQuery(role.id, '')"
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
                                                filteredPermissionsForRole(
                                                    role.id,
                                                ).length
                                            }}</span
                                        >
                                        of
                                        <span
                                            class="font-semibold text-gray-700"
                                            >{{
                                                (permissions || []).length
                                            }}</span
                                        >
                                    </span>

                                    <span
                                        v-if="
                                            getRoleQuery(role.id) &&
                                            filteredPermissionsForRole(role.id)
                                                .length === 0
                                        "
                                    >
                                        No matches
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2"
                        >
                            <label
                                v-for="p in filteredPermissionsForRole(role.id)"
                                :key="p.id"
                                class="group flex items-center gap-2 text-sm p-2 rounded-lg cursor-pointer border border-transparent hover:border-gray-200 hover:bg-gray-50 transition"
                            >
                                <input
                                    type="checkbox"
                                    :value="p.name"
                                    :checked="
                                        (role.permissions || [])
                                            .map((x) => x.name)
                                            .includes(p.name)
                                    "
                                    @change="togglePermission(role.id, p.name)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />

                                <span class="text-gray-700">
                                    <template
                                        v-for="(part, idx) in highlightMatch(
                                            p.name,
                                            getRoleQuery(role.id),
                                        )"
                                        :key="idx"
                                    >
                                        <span
                                            v-if="part.match"
                                            class="rounded bg-yellow-100 px-1 text-gray-900"
                                        >
                                            {{ part.text }}
                                        </span>
                                        <span v-else>{{ part.text }}</span>
                                    </template>
                                </span>
                            </label>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-xs text-gray-500">
                                Tip: search by module name (e.g. “animals”,
                                “sales”, “admin”)
                            </p>

                            <button
                                @click="
                                    updateRolePermissions(role.name, role.id)
                                "
                                class="bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-5 py-2 rounded-lg font-semibold shadow transition duration-200"
                            >
                                Update Permissions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, ref } from "vue";

const props = defineProps({
    roles: Array,
    permissions: Array,
});

const form = useForm({ name: "" });

const permissionQueryByRole = ref({});

const getRoleQuery = (roleId) => permissionQueryByRole.value[roleId] || "";

const setRoleQuery = (roleId, value) => {
    permissionQueryByRole.value = {
        ...permissionQueryByRole.value,
        [roleId]: value,
    };
};

const filteredPermissionsForRole = (roleId) => {
    const q = getRoleQuery(roleId).trim().toLowerCase();
    if (!q) return props.permissions || [];
    return (props.permissions || []).filter((p) =>
        String(p.name || "")
            .toLowerCase()
            .includes(q),
    );
};

const highlightMatch = (text, query) => {
    const t = String(text ?? "");
    const q = String(query ?? "").trim();
    if (!q) return [{ text: t, match: false }];

    const lowerT = t.toLowerCase();
    const lowerQ = q.toLowerCase();
    const idx = lowerT.indexOf(lowerQ);
    if (idx === -1) return [{ text: t, match: false }];

    return [
        { text: t.slice(0, idx), match: false },
        { text: t.slice(idx, idx + q.length), match: true },
        { text: t.slice(idx + q.length), match: false },
    ];
};

const submit = () => {
    form.post(route("admin.roles.store"), {
        onSuccess: () => form.reset(),
    });
};

function deleteRole(id) {
    if (
        !confirm(
            "Are you sure you want to delete this role? This action cannot be undone.",
        )
    )
        return;
    Inertia.delete(route("admin.roles.destroy", id));
}

function togglePermission(roleId, permissionName) {
    const role = (props.roles || []).find((r) => r.id === roleId);
    if (!role) return;

    const current = (role.permissions || []).map((x) => x.name);
    const next = current.includes(permissionName)
        ? current.filter((n) => n !== permissionName)
        : [...current, permissionName];

    role.permissions = next.map((name) => ({ name }));
}

function updateRolePermissions(roleName, roleId) {
    const role = (props.roles || []).find((r) => r.id === roleId);
    const checked = (role?.permissions || []).map((p) => p.name);

    const permForm = useForm({ role: roleName, permissions: checked });
    permForm.post(route("admin.permissions.assign"), {
        preserveScroll: true,
    });
}
</script>
