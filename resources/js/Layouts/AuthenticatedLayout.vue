<script setup>
import { ref, computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import InventoryDropdown from "@/Components/InventoryDropdown.vue"; // Import the new dropdown component
import ToastNotification from "@/Components/ToastNotification.vue"; // Import ToastNotification
import { Link, usePage } from "@inertiajs/inertia-vue3"; // Import usePage
import { onMounted, watch } from "vue"; // Import onMounted and watch

const showingNavigationDropdown = ref(false);
const toast = ref(null); // Ref for the ToastNotification component

const appSettings = computed(
    () => usePage().props.appSettings || { app_title: "", logo_path: null },
);

onMounted(() => {
    // This layout is not directly used by the Medicine pages, so flash message handling is moved to AppLayout.vue
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <img
                                        v-if="appSettings?.logo_path"
                                        :src="appSettings?.logo_path"
                                        alt="App Logo"
                                        class="block h-9 w-auto"
                                    />
                                    <ApplicationLogo
                                        v-else
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>
                            <div class="flex shrink-0 items-center ml-4">
                                <span
                                    class="text-xl font-semibold text-gray-800"
                                    >{{ appSettings?.app_title }}</span
                                >
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    {{ $t('dashboard') }}
                                </NavLink>
                                <NavLink
                                    :href="route('calving-records.index')"
                                    :active="
                                        route().current('calving-records.*')
                                    "
                                >
                                    {{ $t('calving_records') }}
                                </NavLink>
                                <NavLink
                                    :href="route('calves.index')"
                                    :active="route().current('calves.*')"
                                >
                                    {{ $t('newborn_calves') }}
                                </NavLink>
                                <!-- Use the new InventoryDropdown component -->
                                <InventoryDropdown />
                                <NavLink
                                    :href="route('stock-movements.index')"
                                    :active="
                                        route().current('stock-movements.*')
                                    "
                                >
                                    {{ $t('stock_movements') }}
                                </NavLink>
                                <NavLink
                                    :href="route('diseases.index')"
                                    :active="route().current('diseases.*')"
                                >
                                    {{ $t('diseases') }}
                                </NavLink>
                                <NavLink
                                    :href="route('departments.index')"
                                    :active="route().current('departments.*')"
                                >
                                    {{ $t('departments') }}
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            {{ $t('profile') }}
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('settings.index')"
                                        >
                                            {{ $t('settings') }}
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            {{ $t('logout') }}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            {{ $t('dashboard') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('calving-records.index')"
                            :active="route().current('calving-records.*')"
                        >
                            {{ $t('calving_records') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('calves.index')"
                            :active="route().current('calves.*')"
                        >
                            {{ $t('newborn_calves') }}
                        </ResponsiveNavLink>
                        <!-- Responsive Inventory Dropdown -->
                        <ResponsiveNavLink
                            :href="route('stock-movements.index')"
                            :active="route().current('stock-movements.*')"
                        >
                            {{ $t('stock_movements') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('inventory.index')"
                            :active="route().current('inventory.*')"
                        >
                            {{ $t('inventory_items') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('categories.index')"
                            :active="route().current('categories.*')"
                        >
                            {{ $t('categories') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('medicines.index')"
                            :active="route().current('medicines.*')"
                        >
                            {{ $t('medicine_items') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('medicine-groups.index')"
                            :active="route().current('medicine-groups.*')"
                        >
                            {{ $t('medicine_groups') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('diseases.index')"
                            :active="route().current('diseases.*')"
                        >
                            {{ $t('diseases') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('departments.index')"
                            :active="route().current('departments.*')"
                        >
                            {{ $t('departments') }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ $t('profile') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('settings.index')">
                                {{ $t('settings') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                {{ $t('logout') }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
        <ToastNotification ref="toast" />
    </div>
</template>
