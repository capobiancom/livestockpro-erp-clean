<template>
    <Head title="Create Employee" />

    <Layout>
        <template #title>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Employees Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Add a new employee to your organization
                    </p>
                </div>
                <Link
                    :href="route('employees.index')"
                    class="bg-gradient-to-r ml-5 from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Back to Employees
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mt-6">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Employee Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >1</span
                        >
                        Personal Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the employee's personal details.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                First Name
                                <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., John"
                                required
                            />
                            <p
                                v-if="form.errors.first_name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.first_name }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Last Name
                                <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., Doe"
                                required
                            />
                            <p
                                v-if="form.errors.last_name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.last_name }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Gender
                                <span class="text-indigo-500">*</span>
                            </label>
                            <select
                                v-model="form.gender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                required
                            >
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <p
                                v-if="form.errors.gender"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.gender }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Date of Birth
                            </label>
                            <input
                                v-model="form.date_of_birth"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <p
                                v-if="form.errors.date_of_birth"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.date_of_birth }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Phone
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., +1234567890"
                            />
                            <p
                                v-if="form.errors.phone"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.phone }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Email
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., john.doe@example.com"
                            />
                            <p
                                v-if="form.errors.email"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Address
                            </label>
                            <textarea
                                v-model="form.address"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 123 Main St, Anytown, USA"
                            ></textarea>
                            <p
                                v-if="form.errors.address"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.address }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Employment Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >2</span
                        >
                        Employment Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Specify the employee's role and employment terms.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Join Date
                                <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.join_date"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                required
                            />
                            <p
                                v-if="form.errors.join_date"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.join_date }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Department
                                <span class="text-indigo-500">*</span>
                            </label>
                            <select
                                v-model="form.department_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                required
                            >
                                <option value="">Select Department</option>
                                <option
                                    v-for="department in departments"
                                    :key="department.id"
                                    :value="department.id"
                                >
                                    {{ department.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.department_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.department_id }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Designation
                                <span class="text-indigo-500">*</span>
                            </label>
                            <select
                                v-model="form.designation_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                required
                            >
                                <option value="">Select Designation</option>
                                <option
                                    v-for="designation in designations"
                                    :key="designation.id"
                                    :value="designation.id"
                                >
                                    {{ designation.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.designation_id"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.designation_id }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Employment Type
                                <span class="text-indigo-500">*</span>
                            </label>
                            <select
                                v-model="form.employment_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                required
                            >
                                <option value="">Select Employment Type</option>
                                <option value="permanent">Permanent</option>
                                <option value="contract">Contract</option>
                                <option value="daily">Daily</option>
                            </select>
                            <p
                                v-if="form.errors.employment_type"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.employment_type }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Salary Type
                                <span class="text-indigo-500">*</span>
                            </label>
                            <select
                                v-model="form.salary_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                required
                            >
                                <option value="">Select Salary Type</option>
                                <option value="monthly">Monthly</option>
                                <option value="daily">Daily</option>
                                <option value="hourly">Hourly</option>
                            </select>
                            <p
                                v-if="form.errors.salary_type"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.salary_type }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Status
                                <span class="text-indigo-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                required
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="terminated">Terminated</option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payroll Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >3</span
                        >
                        Payroll Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Enter the employee's payroll related information.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Bonus
                            </label>
                            <input
                                v-model="form.bonus"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 500.00"
                            />
                            <p
                                v-if="form.errors.bonus"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.bonus }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Festival Bonus
                            </label>
                            <input
                                v-model="form.festival_bonus"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 200.00"
                            />
                            <p
                                v-if="form.errors.festival_bonus"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.festival_bonus }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Performance Incentive
                            </label>
                            <input
                                v-model="form.performance_incentive"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 300.00"
                            />
                            <p
                                v-if="form.errors.performance_incentive"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.performance_incentive }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Tax Amount
                            </label>
                            <input
                                v-model="form.tax_amount"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 50.00"
                            />
                            <p
                                v-if="form.errors.tax_amount"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.tax_amount }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Loan Deduction
                            </label>
                            <input
                                v-model="form.loan_deduction"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 100.00"
                            />
                            <p
                                v-if="form.errors.loan_deduction"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.loan_deduction }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Other Deductions
                            </label>
                            <input
                                v-model="form.other_deductions"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., 20.00"
                            />
                            <p
                                v-if="form.errors.other_deductions"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.other_deductions }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Account Details Section -->
                <div class="mb-8">
                    <h3
                        class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
                    >
                        <span
                            class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                            >4</span
                        >
                        Account Details
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Set up the employee's login credentials.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                User Email
                                <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.user_email"
                                type="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="e.g., employee@example.com"
                                required
                            />
                            <p
                                v-if="form.errors.user_email"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.user_email }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Password
                                <span class="text-indigo-500">*</span>
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                required
                            />
                            <p
                                v-if="form.errors.password"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200"
                >
                    <Link
                        :href="route('employees.index')"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        {{
                            form.processing ? "Creating..." : "Create Employee"
                        }}
                    </button>
                </div>
            </div>
        </form>
    </Layout>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    departments: Array,
    designations: Array,
    farm_id: Number,
    user_id: Number,
});

const form = useForm({
    employee_code: "",
    farm_id: props.farm_id,
    user_id: props.user_id,
    department_id: "",
    designation_id: "",
    first_name: "",
    last_name: "",
    gender: "",
    date_of_birth: "",
    phone: "",
    email: "",
    address: "",
    join_date: "",
    employment_type: "",
    salary_type: "",
    status: "active",
    bonus: 0,
    festival_bonus: 0,
    performance_incentive: 0,
    tax_amount: 0,
    loan_deduction: 0,
    other_deductions: 0,
    user_email: "",
    password: "",
});

const submit = () => {
    form.post(route("employees.store"));
};
</script>
