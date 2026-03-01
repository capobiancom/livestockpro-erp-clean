<script setup>
import { ref, computed, watch } from "vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { PlusIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { debounce } from "lodash";

const props = defineProps({
    journalEntry: {
        type: Object,
        default: () => ({}),
    },
    chartOfAccounts: {
        type: Array,
        required: true,
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    entry_date: props.journalEntry.entry_date
        ? new Date(props.journalEntry.entry_date).toISOString().split("T")[0]
        : "",
    reference_type: props.journalEntry.reference_type || "",
    reference_id: props.journalEntry.reference_id || "",
    description: props.journalEntry.description || "",
    status: props.journalEntry.status || "draft",
    lines:
        props.journalEntry.lines && props.journalEntry.lines.length > 0
            ? props.journalEntry.lines.map((line) => ({
                  id: line.id,
                  account_id: line.account_id,
                  debit_amount: line.debit_amount,
                  credit_amount: line.credit_amount,
                  narration: line.narration,
              }))
            : [
                  {
                      account_id: "",
                      debit_amount: 0,
                      credit_amount: 0,
                      narration: "",
                  },
              ],
});

const addLine = () => {
    form.lines.push({
        account_id: "",
        debit_amount: 0,
        credit_amount: 0,
        narration: "",
    });
};

const removeLine = (index) => {
    form.lines.splice(index, 1);
};

const totalDebit = computed(() => {
    return form.lines
        .reduce(
            (sum, line) => parseFloat(sum) + parseFloat(line.debit_amount || 0),
            0,
        )
        .toFixed(2);
});

const totalCredit = computed(() => {
    return form.lines
        .reduce(
            (sum, line) =>
                parseFloat(sum) + parseFloat(line.credit_amount || 0),
            0,
        )
        .toFixed(2);
});

const isBalanced = computed(() => {
    return parseFloat(totalDebit.value) === parseFloat(totalCredit.value);
});

const submit = () => {
    if (props.isEdit) {
        form.put(route("journal-entries.update", props.journalEntry.id), {
            onSuccess: () => form.reset(),
        });
    } else {
        form.post(route("journal-entries.store"), {
            onSuccess: () => form.reset(),
        });
    }
};

// Debounce input for debit/credit to prevent rapid updates
const debouncedUpdate = debounce(() => {
    // This function will be called after a delay,
    // ensuring that totalDebit and totalCredit are updated less frequently.
}, 300);

watch(() => form.lines, debouncedUpdate, { deep: true });
</script>

<template>
    <form
        @submit.prevent="submit"
        class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"
    >
        <div class="space-y-6">
            <!-- General Information Section -->
            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    General Information
                </h2>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div>
                        <InputLabel for="entry_date" value="Entry Date" />
                        <TextInput
                            id="entry_date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.entry_date"
                            required
                        />
                        <InputError
                            :message="form.errors.entry_date"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="reference_type"
                            value="Reference Type"
                        />
                        <select
                            id="reference_type"
                            v-model="form.reference_type"
                            class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        >
                            <option value="">Select Reference Type</option>
                            <option value="sale">Sale</option>
                            <option value="purchase">Purchase</option>
                            <option value="payroll">Payroll</option>
                            <option value="treatment">Treatment</option>
                            <!-- Backward compatibility: auto journals store FQCN in reference_type -->
                            <option value="App\Models\Sale">Sale (Auto)</option>
                            <option value="App\Models\Purchase">
                                Purchase (Auto)
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.reference_type"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel for="reference_id" value="Reference ID" />
                        <TextInput
                            id="reference_id"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.reference_id"
                        />
                        <InputError
                            :message="form.errors.reference_id"
                            class="mt-2"
                        />
                    </div>

                    <div class="lg:col-span-3">
                        <InputLabel for="description" value="Description" />
                        <textarea
                            id="description"
                            class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                            v-model="form.description"
                        ></textarea>
                        <InputError
                            :message="form.errors.description"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel for="status" value="Status" />
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        >
                            <option value="draft">Draft</option>
                            <option value="posted">Posted</option>
                            <option value="reversed">Reversed</option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-2"
                        />
                    </div>
                </div>
            </div>

            <!-- Journal Entry Lines Section -->
            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    Journal Entry Lines
                </h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Account
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Debit
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Credit
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Narration
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="(line, index) in form.lines"
                                :key="index"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select
                                        :id="`account_id-${index}`"
                                        v-model="line.account_id"
                                        class="block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm text-sm"
                                    >
                                        <option value="">Select Account</option>
                                        <option
                                            v-for="account in chartOfAccounts"
                                            :key="account.id"
                                            :value="account.id"
                                        >
                                            {{ account.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="
                                            form.errors[
                                                `lines.${index}.account_id`
                                            ]
                                        "
                                        class="mt-2"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap w-32">
                                    <TextInput
                                        :id="`debit_amount-${index}`"
                                        type="number"
                                        step="0.01"
                                        class="block w-full text-sm"
                                        v-model="line.debit_amount"
                                        @input="debouncedUpdate"
                                    />
                                    <InputError
                                        :message="
                                            form.errors[
                                                `lines.${index}.debit_amount`
                                            ]
                                        "
                                        class="mt-2"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap w-32">
                                    <TextInput
                                        :id="`credit_amount-${index}`"
                                        type="number"
                                        step="0.01"
                                        class="block w-full text-sm"
                                        v-model="line.credit_amount"
                                        @input="debouncedUpdate"
                                    />
                                    <InputError
                                        :message="
                                            form.errors[
                                                `lines.${index}.credit_amount`
                                            ]
                                        "
                                        class="mt-2"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <TextInput
                                        :id="`narration-${index}`"
                                        type="text"
                                        class="block w-full text-sm"
                                        v-model="line.narration"
                                    />
                                    <InputError
                                        :message="
                                            form.errors[
                                                `lines.${index}.narration`
                                            ]
                                        "
                                        class="mt-2"
                                    />
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right"
                                >
                                    <button
                                        type="button"
                                        @click="removeLine(index)"
                                        class="text-red-600 hover:text-red-900"
                                        v-if="form.lines.length > 1"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <button
                        type="button"
                        @click="addLine"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        <PlusIcon class="h-5 w-5 mr-2" /> Add Line
                    </button>
                    <div class="text-lg font-medium text-gray-800">
                        Total Debit:
                        <span
                            :class="{
                                'text-red-600': !isBalanced,
                                'text-green-600': isBalanced,
                            }"
                            >{{ totalDebit }}</span
                        >
                        | Total Credit:
                        <span
                            :class="{
                                'text-red-600': !isBalanced,
                                'text-green-600': isBalanced,
                            }"
                            >{{ totalCredit }}</span
                        >
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing || !isBalanced }"
                    :disabled="form.processing || !isBalanced"
                >
                    {{
                        isEdit ? "Update Journal Entry" : "Create Journal Entry"
                    }}
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>
