<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm, usePage } from "@inertiajs/inertia-vue3";

const page = usePage();

const form = useForm({
    name: "",
    key: "",
    description: "",
    is_active: true,
    sort_order: 0,
});

function submit() {
    form.post(route("admin.subscription-features.store"));
}
</script>

<template>
    <AppLayout title="Create Subscription Feature">
        <div class="max-w-3xl mx-auto space-y-6">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Create Subscription Feature
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Define a feature that can be attached to plans.
                            Example keys: <code> {{ $t('animals') }} </code>,
                            <code> {{ $t('reports') }} </code>, <code> {{ $t('accounting') }} </code>.
                        </p>
                    </div>

                    <Link
                        :href="route('admin.subscription-features.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        Back
                    </Link>
                </div>

                <div
                    v-if="page.props.flash?.error"
                    class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm"
                >
                    {{ page.props.flash.error }}
                </div>
            </div>

            <form
                class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 space-y-5"
                @submit.prevent="submit"
            >
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        > {{ $t('name') }} </label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g. Reports"
                    />
                    <div
                        v-if="form.errors.name"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        > {{ $t('key') }} </label
                    >
                    <input
                        v-model="form.key"
                        type="text"
                        class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g. reports"
                    />
                    <p class="mt-1 text-xs text-gray-500">
                        Must be unique. Will be normalized to snake_case.
                    </p>
                    <div
                        v-if="form.errors.key"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.key }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        > {{ $t('description') }} </label
                    >
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Optional description"
                    />
                    <div
                        v-if="form.errors.description"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.description }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            > {{ $t('sort_order') }} </label
                        >
                        <input
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            class="mt-1 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <div
                            v-if="form.errors.sort_order"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.sort_order }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-6">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label
                            for="is_active"
                            class="text-sm font-medium text-gray-700"
                        >
                            Active
                        </label>
                        <div
                            v-if="form.errors.is_active"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.is_active }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                    >
                        Create
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
