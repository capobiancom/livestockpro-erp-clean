<template>
    <AppLayout>
        <template #title>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Financial Reports
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Quick access to core financial statements. Choose a
                        report, set a date range, and print or export as needed.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                        @click="resetToThisMonth"
                    >
                        This month
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800"
                        @click="applyToAll"
                    >
                        Apply dates
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Date range -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-900">
                            Date range
                        </h2>
                        <div class="text-xs text-gray-500">
                            Used when opening Profit & Loss, Cash Flow, and
                            Trial Balance.
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-12">
                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >From</label
                        >
                        <input
                            v-model="form.from"
                            type="date"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-medium text-gray-700"
                            >To</label
                        >
                        <input
                            v-model="form.to"
                            type="date"
                            class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="md:col-span-6 flex items-end justify-end">
                        <div
                            class="rounded-lg bg-gray-50 px-4 py-2 text-xs text-gray-600 ring-1 ring-gray-200"
                        >
                            Selected:
                            <span class="font-semibold">{{
                                form.from || "—"
                            }}</span>
                            →
                            <span class="font-semibold">{{
                                form.to || "—"
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <button
                    type="button"
                    class="group rounded-xl bg-white p-5 text-left shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-0.5 hover:shadow-md"
                    @click="openBalanceSheet"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Statement
                            </div>
                            <div
                                class="mt-2 text-lg font-semibold text-gray-900"
                            >
                                Balance Sheet
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                Assets, liabilities, and equity snapshot.
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-blue-50 p-2 text-blue-700 ring-1 ring-blue-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7.414A2 2 0 0017.414 6L14 2.586A2 2 0 0012.586 2H4z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-medium text-blue-700">
                        Open report →
                    </div>
                </button>

                <button
                    type="button"
                    class="group rounded-xl bg-white p-5 text-left shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-0.5 hover:shadow-md"
                    @click="openProfitLoss"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Statement
                            </div>
                            <div
                                class="mt-2 text-lg font-semibold text-gray-900"
                            >
                                Profit & Loss
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                Income statement for the selected period.
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-emerald-50 p-2 text-emerald-700 ring-1 ring-emerald-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3a1 1 0 00.293.707l1 1a1 1 0 001.414-1.414L11 9.586V7z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-medium text-emerald-700">
                        Open report →
                    </div>
                </button>

                <button
                    type="button"
                    class="group rounded-xl bg-white p-5 text-left shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-0.5 hover:shadow-md"
                    @click="openCashFlow"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Statement
                            </div>
                            <div
                                class="mt-2 text-lg font-semibold text-gray-900"
                            >
                                Cash Flow
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                Cash movement across activities.
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-violet-50 p-2 text-violet-700 ring-1 ring-violet-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1v-6zM7 7a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1H8a1 1 0 01-1-1V7zM12 4a1 1 0 011-1h2a1 1 0 011 1v13a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-medium text-violet-700">
                        Open report →
                    </div>
                </button>

                <button
                    type="button"
                    class="group rounded-xl bg-white p-5 text-left shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-0.5 hover:shadow-md"
                    @click="openTrialBalance"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="text-xs font-semibold uppercase tracking-wide text-gray-500"
                            >
                                Report
                            </div>
                            <div
                                class="mt-2 text-lg font-semibold text-gray-900"
                            >
                                Trial Balance
                            </div>
                            <p class="mt-1 text-sm text-gray-600">
                                Debit/credit totals by account.
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-amber-50 p-2 text-amber-700 ring-1 ring-amber-100"
                            aria-hidden="true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M9 2a1 1 0 00-1 1v1H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2V3a1 1 0 00-1-1H9z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-medium text-amber-700">
                        Open report →
                    </div>
                </button>
            </div>

            <div class="text-xs text-gray-500">
                Notes: Balance Sheet is typically “as of today” and may ignore
                the date range. Other reports use the selected date range.
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";

const todayIso = () => new Date().toISOString().slice(0, 10);

const firstDayOfThisMonthIso = () => {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), 1)
        .toISOString()
        .slice(0, 10);
};

const form = reactive({
    from: firstDayOfThisMonthIso(),
    to: todayIso(),
});

function resetToThisMonth() {
    form.from = firstDayOfThisMonthIso();
    form.to = todayIso();
}

function applyToAll() {
    // No-op: dates are applied when opening a report.
    // Kept for UX consistency with other report pages.
}

function openBalanceSheet() {
    Inertia.get("/balance-sheet", {}, { preserveScroll: true });
}

function openProfitLoss() {
    Inertia.get(
        "/profit-loss",
        { from: form.from, to: form.to },
        { preserveScroll: true },
    );
}

function openCashFlow() {
    Inertia.get(
        "/cash-flow",
        { from: form.from, to: form.to },
        { preserveScroll: true },
    );
}

function openTrialBalance() {
    Inertia.get(
        "/trial-balance",
        { from: form.from, to: form.to },
        { preserveScroll: true },
    );
}
</script>
