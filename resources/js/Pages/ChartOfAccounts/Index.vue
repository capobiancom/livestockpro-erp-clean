<template>
    <Layout title="Chart of Accounts">
        <template #title>
            <div class="flex items-center justify-between">
                <h2
                    class="flex items-center text-xl font-semibold leading-tight text-gray-800"
                >
                    Chart of Accounts
                </h2>
                <PrimaryButton @click="openCreateModal" class="ml-5">
                    Add Account
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div
                            v-if="chartOfAccounts.length === 0"
                            class="text-center text-gray-500"
                        >
                            No chart of accounts found.
                        </div>
                        <div v-else>
                            <ChartOfAccountTree
                                :accounts="chartOfAccounts"
                                @edit="openEditModal"
                                @delete="confirmDelete"
                                @add-child="openCreateChildModal"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditMode ? "Edit Account" : "Create Account" }}
                </h2>

                <form @submit.prevent="saveAccount">
                    <div class="mb-4">
                        <InputLabel for="parent_id" value="Parent Account" />
                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                            <option :value="null">No Parent</option>
                            <option
                                v-for="account in flatAccounts"
                                :key="account.id"
                                :value="account.id"
                            >
                                {{ account.name }} ({{ account.code }})
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.parent_id"
                            class="mt-2"
                        />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="code" value="Code" />
                        <TextInput
                            id="code"
                            v-model="form.code"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                        />
                        <InputError :message="form.errors.code" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="type" value="Type" />
                        <select
                            id="type"
                            v-model="form.type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required
                        >
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="form.is_system"
                                name="is_system"
                            />
                            <span class="ml-2 text-sm text-gray-600"
                                >Is System Account</span
                            >
                        </label>
                        <InputError
                            :message="form.errors.is_system"
                            class="mt-2"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="form.is_active"
                                name="is_active"
                            />
                            <span class="ml-2 text-sm text-gray-600"
                                >Is Active</span
                            >
                        </label>
                        <InputError
                            :message="form.errors.is_active"
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeModal">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            class="ml-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ isEditMode ? "Update" : "Create" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <ConfirmDeleteModal
            :show="showConfirmDeleteModal"
            @close="showConfirmDeleteModal = false"
            @confirm="deleteAccount"
        />
    </Layout>
</template>

<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import ChartOfAccountTree from "./Partials/ChartOfAccountTree.vue";
import { ref, reactive, computed } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    chartOfAccounts: Array,
    flatAccounts: Array, // For parent selection in form
});

const showModal = ref(false);
const isEditMode = ref(false);
const editingAccount = ref(null);
const showConfirmDeleteModal = ref(false);
const accountToDelete = ref(null);

const form = useForm({
    parent_id: null,
    code: "",
    name: "",
    type: "asset",
    is_system: false,
    is_active: true,
});

const openCreateModal = () => {
    isEditMode.value = false;
    editingAccount.value = null;
    form.reset();
    showModal.value = true;
};

const openCreateChildModal = (parentAccount) => {
    isEditMode.value = false;
    editingAccount.value = null;
    form.reset();
    form.parent_id = parentAccount.id;
    showModal.value = true;
};

const openEditModal = (account) => {
    isEditMode.value = true;
    editingAccount.value = account;
    form.parent_id = account.parent_id;
    form.code = account.code;
    form.name = account.name;
    form.type = account.type;
    form.is_system = account.is_system;
    form.is_active = account.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.clearErrors();
};

const saveAccount = () => {
    if (isEditMode.value) {
        form.put(route("chart-of-accounts.update", editingAccount.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("chart-of-accounts.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (account) => {
    accountToDelete.value = account;
    showConfirmDeleteModal.value = true;
};

const deleteAccount = () => {
    if (accountToDelete.value) {
        Inertia.delete(
            route("chart-of-accounts.destroy", accountToDelete.value.id),
            {
                onSuccess: () => {
                    showConfirmDeleteModal.value = false;
                    accountToDelete.value = null;
                },
            },
        );
    }
};
</script>

<style scoped>
/* Add any specific styles for this page here */
</style>
