<script setup>
import AppLayout from "@/Pages/Layout/AppLayout.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import NotifyModal from "@/Components/NotifyModal.vue";
import ToastNotification from "@/Components/ToastNotification.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, ref } from "vue";

const props = defineProps({
    kpis: { type: Object, required: true },
    farms: { type: Array, required: true },
});

const page = usePage();

const q = ref("");
const busyFarmId = ref(null);

const toastRef = ref(null);

const confirmOpen = ref(false);
const confirmTitle = ref("");
const confirmMessage = ref("");
const confirmVariant = ref("primary");
const confirmLoading = ref(false);
let confirmAction = null;

const notifyOpen = ref(false);
const notifyFarm = ref(null);
const notifyLoading = ref(false);

const filteredFarms = computed(() => {
    const query = q.value.trim().toLowerCase();
    if (!query) return props.farms;
    return props.farms.filter((f) =>
        (f.name || "").toLowerCase().includes(query),
    );
});

const totals = computed(() => ({
    farms: props.farms.length,
    activeSubscriptions: props.farms.filter((f) => f.subscription?.is_active)
        .length,
    inactiveSubscriptions: props.farms.filter(
        (f) => f.subscription && !f.subscription.is_active,
    ).length,
}));

function formatMoneyFromCents(cents) {
    const amount = (Number(cents || 0) / 100).toFixed(2);
    return amount;
}

function openConfirm({ title, message, variant = "primary", onConfirm }) {
    confirmTitle.value = title || "Confirm action";
    confirmMessage.value = message || "";
    confirmVariant.value = variant;
    confirmAction = onConfirm;
    confirmOpen.value = true;
}

function closeConfirm() {
    if (confirmLoading.value) return;
    confirmOpen.value = false;
    confirmAction = null;
}

function confirmNow() {
    if (!confirmAction || confirmLoading.value) return;
    confirmLoading.value = true;

    Promise.resolve()
        .then(() => confirmAction?.())
        .catch((e) => {
            // Inertia router.post doesn't throw on validation/redirect; this is for unexpected JS errors.
            console.error(e);
            toastRef.value?.showToast(
                "Unable to complete the action. Please try again.",
                "error",
            );
        })
        .finally(() => {
            confirmLoading.value = false;
            confirmOpen.value = false;
            confirmAction = null;
        });
}

function toggleSubscription(farm) {
    if (!farm?.subscription?.id) return;

    const nextStateLabel = farm.subscription.is_active
        ? "Inactivate"
        : "Activate";
    const nextStateVerb = farm.subscription.is_active
        ? "inactivate"
        : "activate";

    openConfirm({
        title: `${nextStateLabel} subscription`,
        message: `Are you sure you want to ${nextStateVerb} subscription for "${farm.name}"?`,
        variant: farm.subscription.is_active ? "danger" : "primary",
        onConfirm: () => {
            busyFarmId.value = farm.id;

            Inertia.post(
                route("admin.farms.subscriptions.toggle", {
                    farm: farm.id,
                }),
                {},
                {
                    preserveScroll: true,
                    // Ensure the table reflects the new state immediately after toggle.
                    // (Backend now toggles the same "current" subscription record.)
                    onSuccess: () => {
                        Inertia.reload({ only: ["farms", "kpis"] });
                        toastRef.value?.showToast(
                            `Subscription ${nextStateVerb}d successfully.`,
                            "success",
                        );
                    },
                    onError: () => {
                        toastRef.value?.showToast(
                            "Failed to update subscription.",
                            "error",
                        );
                    },
                    onFinish: () => {
                        busyFarmId.value = null;
                    },
                },
            );
        },
    });
}

function openNotify(farm) {
    notifyFarm.value = farm;
    notifyOpen.value = true;
}

function closeNotify() {
    if (notifyLoading.value) return;
    notifyOpen.value = false;
    notifyFarm.value = null;
}

function submitNotify(message) {
    if (!notifyFarm.value?.id) return;

    notifyLoading.value = true;
    busyFarmId.value = notifyFarm.value.id;

    Inertia.post(
        route("admin.farms.notifications.send", { farm: notifyFarm.value.id }),
        { message },
        {
            preserveScroll: true,
            onSuccess: () => {
                toastRef.value?.showToast("Notification sent.", "success");
            },
            onError: () => {
                toastRef.value?.showToast(
                    "Failed to send notification.",
                    "error",
                );
            },
            onFinish: () => {
                notifyLoading.value = false;
                busyFarmId.value = null;
                notifyOpen.value = false;
                notifyFarm.value = null;
            },
        },
    );
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout>
        <template #title>
            <div>
                <div class="text-xs text-gray-500">Administration</div>
                <div class="text-lg font-semibold text-gray-900">
                    Admin Dashboard
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- KPI row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="text-xs font-medium text-gray-500">
                        This month receivable
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{
                            formatMoneyFromCents(
                                kpis.this_month_receivable_cents,
                            )
                        }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">
                        Invoices (unpaid/pending)
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="text-xs font-medium text-gray-500">
                        This month collection
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{
                            formatMoneyFromCents(
                                kpis.this_month_collection_cents,
                            )
                        }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">
                        Payments (succeeded)
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Total farms
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ totals.farms }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">In system</div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Active subscriptions
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ totals.activeSubscriptions }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400">
                        Inactive: {{ totals.inactiveSubscriptions }}
                    </div>
                </div>
            </div>

            <!-- Farms table -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-4 border-b"
                >
                    <div>
                        <div class="text-sm font-semibold text-gray-900">
                            Farms
                        </div>
                        <div class="text-xs text-gray-500">
                            Manage subscriptions and send notifications
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input
                                v-model="q"
                                type="text"
                                class="border border-gray-200 rounded-lg pl-3 pr-3 py-2 text-sm w-full md:w-80 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search farm by name..."
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr class="text-left border-b">
                                <th class="py-3 px-4 font-medium">Farm</th>
                                <th class="py-3 px-4 font-medium">Users</th>
                                <th class="py-3 px-4 font-medium">Plan</th>
                                <th class="py-3 px-4 font-medium">
                                    Subscription
                                </th>
                                <th class="py-3 px-4 font-medium">Ends</th>
                                <th class="py-3 px-4 font-medium text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="farm in filteredFarms"
                                :key="farm.id"
                                class="border-b hover:bg-gray-50/60"
                            >
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900">
                                        {{ farm.name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ID: {{ farm.id }}
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-gray-700">
                                    {{ farm.users_count }}
                                </td>

                                <td class="py-3 px-4 text-gray-700">
                                    {{ farm.subscription?.plan?.name ?? "-" }}
                                </td>

                                <td class="py-3 px-4">
                                    <span
                                        v-if="farm.subscription"
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium border"
                                        :class="
                                            farm.subscription.is_active
                                                ? 'bg-green-50 text-green-700 border-green-200'
                                                : 'bg-red-50 text-red-700 border-red-200'
                                        "
                                    >
                                        {{
                                            farm.subscription.is_active
                                                ? "Active"
                                                : "Inactive"
                                        }}
                                    </span>
                                    <span v-else class="text-gray-400"
                                        >No subscription</span
                                    >
                                </td>

                                <td class="py-3 px-4 text-gray-700">
                                    {{ farm.subscription?.ends_on ?? "-" }}
                                </td>

                                <td class="py-3 px-4">
                                    <div
                                        class="flex flex-wrap justify-end gap-2"
                                    >
                                        <button
                                            class="relative inline-flex h-9 w-20 items-center rounded-full transition-colors disabled:opacity-50"
                                            :class="
                                                farm.subscription?.is_active
                                                    ? 'bg-green-600'
                                                    : 'bg-gray-300'
                                            "
                                            :disabled="
                                                !farm.subscription ||
                                                busyFarmId === farm.id
                                            "
                                            @click="toggleSubscription(farm)"
                                            type="button"
                                            aria-label="Toggle subscription active status"
                                        >
                                            <span
                                                class="inline-flex items-center justify-center h-7 w-7 transform rounded-full bg-white shadow transition"
                                                :class="
                                                    farm.subscription?.is_active
                                                        ? 'translate-x-11'
                                                        : 'translate-x-2'
                                                "
                                            >
                                            </span>
                                            <span
                                                class="absolute left-2 text-[10px] font-semibold text-white"
                                                v-if="
                                                    farm.subscription?.is_active
                                                "
                                                >ON</span
                                            >
                                            <span
                                                class="absolute right-2 text-[10px] font-semibold text-gray-700"
                                                v-else
                                                >OFF</span
                                            >
                                        </button>

                                        <button
                                            class="px-3 py-2 rounded-lg border border-gray-200 text-xs font-medium hover:bg-gray-50 disabled:opacity-50"
                                            :disabled="busyFarmId === farm.id"
                                            @click="openNotify(farm)"
                                        >
                                            Notify
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredFarms.length === 0">
                                <td
                                    colspan="6"
                                    class="py-10 text-center text-gray-500"
                                >
                                    No farms found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 text-xs text-gray-500">
                    Actions show toast messages via flash (success/error). If
                    you don’t see them, check `ToastNotification` component.
                </div>
            </div>
        </div>
        <ConfirmModal
            :show="confirmOpen"
            :title="confirmTitle"
            :message="confirmMessage"
            :variant="confirmVariant"
            :loading="confirmLoading"
            confirm-text="Confirm"
            cancel-text="Cancel"
            @close="closeConfirm"
            @confirm="confirmNow"
        />

        <NotifyModal
            :show="notifyOpen"
            :farm-name="notifyFarm?.name"
            :loading="notifyLoading"
            @close="closeNotify"
            @submit="submitNotify"
        />
    </AppLayout>
</template>
