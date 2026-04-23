<script setup>
const props = defineProps({
    form: Object,
    farms: Array,
    users: Array,
    customerTypes: Array,
});
</script>

<template>
    <!-- Customer Information Section -->
    <div class="mb-8">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >1</span
            >
            Customer Information
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Basic customer details and contact information
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.name }"
                    autocomplete="name"
                    placeholder="Customer's full name"
                />
                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Customer Type <span class="text-red-500">*</span>
                </label>
                <select
                    id="type"
                    v-model="form.type"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="[{ 'border-red-500': form.errors.type }, 'cursor-pointer hover:bg-gray-50 transition-colors duration-200']"
                >
                    <option value=""> {{ $t('select_customer_type') }} </option>
                    <option
                        v-for="typeOption in customerTypes"
                        :key="typeOption"
                        :value="typeOption"
                    >
                        {{ typeOption }}
                    </option>
                </select>
                <p v-if="form.errors.type" class="text-red-500 text-sm mt-1">
                    {{ form.errors.type }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Email <span class="text-gray-400 text-xs"> {{ $t('optional') }} </span>
                </label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.email }"
                    autocomplete="email"
                    placeholder="customer@example.com"
                />
                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Phone <span class="text-gray-400 text-xs"> {{ $t('optional') }} </span>
                </label>
                <input
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.phone }"
                    autocomplete="phone"
                    placeholder="+1 (555) 123-4567"
                />
                <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">
                    {{ form.errors.phone }}
                </p>
            </div>
        </div>
    </div>

    <!-- Address and Other Details Section -->
    <div class="mb-8 pt-8 border-t border-gray-200">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >2</span
            >
            Address & Other Details
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Physical address, contact person, and assigned farm/user
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Address
                    <span class="text-gray-400 text-xs"> {{ $t('optional') }} </span>
                </label>
                <input
                    id="address"
                    v-model="form.address"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.address }"
                    autocomplete="address"
                    placeholder="123 Main St, Anytown, USA"
                />
                <p v-if="form.errors.address" class="text-red-500 text-sm mt-1">
                    {{ form.errors.address }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Contact Person
                    <span class="text-gray-400 text-xs"> {{ $t('optional') }} </span>
                </label>
                <input
                    id="contact_person"
                    v-model="form.contact_person"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    :class="{ 'border-red-500': form.errors.contact_person }"
                    placeholder="Name of primary contact"
                />
                <p
                    v-if="form.errors.contact_person"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ form.errors.contact_person }}
                </p>
            </div>
        </div>
    </div>

    <!-- Additional Notes Section -->
    <div class="pt-8 border-t border-gray-200">
        <h3
            class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2"
        >
            <span
                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm"
                >3</span
            >
            Additional Notes
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Add any additional information about this customer
        </p>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Notes <span class="text-gray-400 text-xs"> {{ $t('optional') }} </span>
            </label>
            <textarea
                id="notes"
                v-model="form.notes"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                :class="{ 'border-red-500': form.errors.notes }"
                placeholder="Additional notes about this customer..."
            ></textarea>
            <p v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                {{ form.errors.notes }}
            </p>
        </div>
    </div>
</template>
