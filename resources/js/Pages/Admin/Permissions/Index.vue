<template>
    <Head title="Permissions Management" />

    <AppLayout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Permission Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Create and manage system permissions
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

        <!-- Create New Permission -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                Create New Permission
            </h3>
            <form @submit.prevent="submit" class="flex items-end gap-4">
                <div class="flex-1">
                    <label
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Permission Name
                    </label>
                    <input
                        v-model="form.name"
                        placeholder="e.g., create animals, edit farms, etc."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
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
                    class="bg-gradient-to-r from-purple-500 to-violet-500 hover:from-purple-600 hover:to-violet-600 text-white px-6 py-2 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50"
                >
                    <span v-if="form.processing">Creating...</span>
                    <span v-else>Create Permission</span>
                </button>
            </form>
        </div>

        <!-- Existing Permissions -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold text-gray-800">
                    Existing Permissions ({{ permissions.length }})
                </h3>
            </div>

            <div v-if="permissions.length === 0" class="text-center py-12">
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
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                    />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                    No Permissions Found
                </h3>
                <p class="text-gray-500">
                    Create your first permission to control access to features.
                </p>
            </div>

            <div v-else>
                <div
                    class="p-6 border-b bg-gradient-to-r from-white to-purple-50/40"
                >
                    <div
                        class="flex flex-col md:flex-row md:items-center md:justify-between gap-3"
                    >
                        <div class="text-sm text-gray-600">
                            Search and filter permissions quickly.
                        </div>

                        <div class="w-full md:max-w-md">
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
                                    v-model="permissionSearch"
                                    type="text"
                                    placeholder="Search existing permissions..."
                                    class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-20 py-2 text-sm text-gray-700 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30"
                                />

                                <button
                                    v-if="permissionSearch"
                                    type="button"
                                    @click="permissionSearch = ''"
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
                                    <span class="font-semibold text-gray-700">{{
                                        filteredPermissions.length
                                    }}</span>
                                    of
                                    <span class="font-semibold text-gray-700">{{
                                        permissions.length
                                    }}</span>
                                </span>

                                <span
                                    v-if="
                                        permissionSearch &&
                                        filteredPermissions.length === 0
                                    "
                                >
                                    No matches
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6"
                >
                    <div
                        v-for="p in filteredPermissions"
                        :key="p.id"
                        class="flex items-center justify-between p-4 bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-200 rounded-lg hover:shadow-md transition"
                    >
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-500 rounded-full p-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-white"
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

                            <span class="font-semibold text-gray-800">
                                <template
                                    v-for="(part, idx) in highlightMatch(
                                        p.name,
                                        permissionSearch,
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
                        </div>
                        <button
                            @click="confirmDelete(p)"
                            class="text-red-600 hover:text-red-800 transition"
                            title="Delete Permission"
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
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-red-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">
                        Confirm Deletion
                    </h3>
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete
                    <span class="font-semibold">{{
                        permissionToDelete?.name
                    }}</span
                    >? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deletePermission"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                    >
                        Delete Permission
                    </button>
                </div>
            </div>
        </div>

        <!-- Assign Permissions to Role -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-3">
                Assign Permissions to Role
            </h3>
            <form @submit.prevent="assignPermissions" class="space-y-4">
                <div>
                    <label
                        class="block text-sm font-semibold text-gray-700 mb-2"
                    >
                        Select Role
                    </label>

                    <div class="w-full">
                        <select
                            v-model="assignForm.role"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">-- Select a Role --</option>
                            <option
                                v-for="role in filteredRoles"
                                :key="role.id"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-semibold text-gray-700 mb-3"
                    >
                        Select Permissions
                    </label>

                    <div class="mb-3 w-full">
                        <div class="relative w-full">
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
                                v-model="assignPermissionSearch"
                                type="text"
                                placeholder="Search permissions to assign..."
                                class="w-full rounded-lg border border-gray-200 bg-white pl-9 pr-20 py-2 text-sm text-gray-700 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30"
                            />

                            <button
                                v-if="assignPermissionSearch"
                                type="button"
                                @click="assignPermissionSearch = ''"
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
                                <span class="font-semibold text-gray-700">{{
                                    filteredAssignPermissions.length
                                }}</span>
                                of
                                <span class="font-semibold text-gray-700">{{
                                    permissions.length
                                }}</span>
                            </span>

                            <span
                                v-if="
                                    assignPermissionSearch &&
                                    filteredAssignPermissions.length === 0
                                "
                            >
                                No matches
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label
                            v-for="p in filteredAssignPermissions"
                            :key="p.id"
                            class="flex items-center gap-2 text-sm hover:bg-gray-100 p-2 rounded cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                :value="p.name"
                                v-model="assignForm.permissions"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            />
                            <span class="text-gray-700">{{ p.name }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="
                            !assignForm.role ||
                            assignForm.permissions.length === 0
                        "
                        class="bg-gradient-to-r from-purple-500 to-violet-500 hover:from-purple-600 hover:to-violet-600 text-white px-6 py-2 rounded-lg font-semibold shadow transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Assign Permissions to Role
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, reactive, ref } from "vue";

const props = defineProps({
    permissions: Array,
    roles: Array,
});

const form = useForm({ name: "" });

const permissionSearch = ref("");
const assignPermissionSearch = ref("");

const filteredPermissions = computed(() => {
    const q = permissionSearch.value.trim().toLowerCase();
    if (!q) return props.permissions || [];
    return (props.permissions || []).filter((p) =>
        String(p.name || "")
            .toLowerCase()
            .includes(q),
    );
});

const filteredRoles = computed(() => props.roles || []);

const filteredAssignPermissions = computed(() => {
    const q = assignPermissionSearch.value.trim().toLowerCase();
    if (!q) return props.permissions || [];
    return (props.permissions || []).filter((p) =>
        String(p.name || "")
            .toLowerCase()
            .includes(q),
    );
});

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
    form.post(route("admin.permissions.store"), {
        onSuccess: () => form.reset(),
    });
};

const showDeleteModal = ref(false);
const permissionToDelete = ref(null);

const confirmDelete = (permission) => {
    permissionToDelete.value = permission;
    showDeleteModal.value = true;
};

const deletePermission = () => {
    if (!permissionToDelete.value?.id) return;

    Inertia.delete(
        route("admin.permissions.destroy", permissionToDelete.value.id),
        {
            onSuccess: () => {
                showDeleteModal.value = false;
                permissionToDelete.value = null;
            },
            onFinish: () => {
                // keep modal state consistent even if request fails
            },
        },
    );
};

const assignForm = reactive({
    role: "",
    permissions: [],
});

function assignPermissions() {
    Inertia.post(route("admin.permissions.assign"), assignForm, {
        preserveScroll: true,
        onSuccess: () => {
            assignForm.role = "";
            assignForm.permissions = [];
        },
    });
}
</script>
