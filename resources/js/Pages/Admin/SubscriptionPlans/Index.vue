<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline";
import ConfirmModal from "@/Components/ConfirmModal.vue";

const props = defineProps({
    plans: { type: Array, default: () => [] },
});

const page = usePage();

function formatMoney(cents) {
    if (cents === null || cents === undefined) return "-";
    return (cents / 100).toFixed(2);
}

const confirmDeleteOpen = ref(false);
const planToDelete = ref(null);
const deleting = ref(false);

function requestDeletePlan(plan) {
    planToDelete.value = plan;
    confirmDeleteOpen.value = true;
}

function closeDeletePlan() {
    confirmDeleteOpen.value = false;
    planToDelete.value = null;
    deleting.value = false;
}

function confirmDeletePlan() {
    if (!planToDelete.value || deleting.value) return;

    deleting.value = true;

    Inertia.delete(
        route("admin.subscription-plans.destroy", planToDelete.value.id),
        {
            preserveScroll: true,
            onFinish: () => {
                deleting.value = false;
                closeDeletePlan();
            },
        },
    );
}

const sortedPlans = computed(() => {
    return [...props.plans].sort((a, b) => {
        if ((a.sort_order ?? 0) !== (b.sort_order ?? 0)) {
            return (a.sort_order ?? 0) - (b.sort_order ?? 0);
        }
        return (a.id ?? 0) - (b.id ?? 0);
    });
});
</script>

<template>
    <AppLayout title="Subscription Plans">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6"
            >
                <div
                    class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Subscription Plans
                        </h1>
                        <p class="text-sm text-gray-600 mt-1 max-w-2xl">
                            Create and manage plans shown on the Billing page.
                            Prices are stored in cents.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('admin.subscription-plans.create')"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            New plan
                        </Link>
                    </div>
                </div>

                <div
                    v-if="page.props.flash?.success"
                    class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm"
                >
                    {{ page.props.flash.success }}
                </div>
                <div
                    v-if="page.props.flash?.error"
                    class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm"
                >
                    {{ page.props.flash.error }}
                </div>
            </div>

            <!-- Quick stats -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Total plans
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ sortedPlans.length }}
                    </div>
                </div>
                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5"
                >
                    <div class="text-xs font-medium text-gray-500"> {{ $t('active') }} </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ sortedPlans.filter((p) => p.is_active).length }}
                    </div>
                </div>
                <div
                    class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Inactive
                    </div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">
                        {{ sortedPlans.filter((p) => !p.is_active).length }}
                    </div>
                </div>
            </div>

            <!-- Table / Empty state -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
            >
                <div
                    class="flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4"
                >
                    <div>
                        <div class="text-sm font-semibold text-gray-900">
                            Plans
                        </div>
                        <div class="text-xs text-gray-500">
                            Sorted by sort order, then ID
                        </div>
                    </div>
                </div>

                <div v-if="sortedPlans.length === 0" class="px-6 py-12">
                    <div
                        class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-10 text-center"
                    >
                        <div class="text-sm font-semibold text-gray-900">
                            No plans yet
                        </div>
                        <div class="mt-1 text-sm text-gray-600">
                            Create your first subscription plan to start
                            selling.
                        </div>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.subscription-plans.create')"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Create plan
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="text-left font-medium px-6 py-3">
                                    Plan
                                </th>
                                <th class="text-left font-medium px-6 py-3">
                                    Slug
                                </th>
                                <th class="text-right font-medium px-6 py-3">
                                    Monthly
                                </th>
                                <th class="text-right font-medium px-6 py-3">
                                    Yearly discount
                                </th>
                                <th class="text-left font-medium px-6 py-3">
                                    Status
                                </th>
                                <th class="text-right font-medium px-6 py-3">
                                    Sort
                                </th>
                                <th class="text-right font-medium px-6 py-3">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="p in sortedPlans"
                                :key="p.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ p.name }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-gray-500">
                                        ID: {{ p.id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <span
                                        class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs font-medium text-gray-700"
                                    >
                                        {{ p.slug }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-semibold text-gray-900">
                                        {{ formatMoney(p.monthly_price_cents) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-700">
                                    {{ p.yearly_discount_percent }}%
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium border"
                                        :class="
                                            p.is_active
                                                ? 'bg-green-50 text-green-700 border-green-200'
                                                : 'bg-gray-50 text-gray-600 border-gray-200'
                                        "
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full"
                                            :class="
                                                p.is_active
                                                    ? 'bg-green-600'
                                                    : 'bg-gray-400'
                                            "
                                        ></span>
                                        {{
                                            p.is_active ? "Active" : "Inactive"
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-700">
                                    {{ p.sort_order }}
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'admin.subscription-plans.edit',
                                                    p.id,
                                                )
                                            "
                                            class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            title="Edit"
                                        >
                                            <PencilSquareIcon class="h-4 w-4" />
                                        </Link>
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white p-2 text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            title="Delete"
                                            @click="requestDeletePlan(p)"
                                        >
                                            <TrashIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <ConfirmModal
                :show="confirmDeleteOpen"
                variant="danger"
                title="Delete plan"
                :message="
                    planToDelete
                        ? `Delete plan '${planToDelete.name}'? This cannot be undone.`
                        : ''
                "
                confirmText="Delete"
                cancelText="Cancel"
                :loading="deleting"
                @close="closeDeletePlan"
                @confirm="confirmDeletePlan"
            />
        </div>
    </AppLayout>
</template>
