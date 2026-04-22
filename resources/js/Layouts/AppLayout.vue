<template>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Sidebar -->
        <aside
            :class="{
                'translate-x-0': showingSidebar,
                '-translate-x-full': !showingSidebar,
            }"
            class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 shadow-2xl fixed left-0 top-0 h-screen flex flex-col z-30 transition-transform duration-300 ease-in-out lg:translate-x-0 app-sidebar"
        >
            <!-- Logo/Brand - Fixed at top -->
            <div class="p-5 border-b border-gray-700 flex-shrink-0">
                <Link
                    :href="
                        hasRole(['Super Admin', 'admin'])
                            ? route('admin.dashboard')
                            : route('dashboard')
                    "
                    class="flex items-center gap-2"
                >
                    <img
                        v-if="appSettings.logo_path"
                        :src="appSettings.logo_path"
                        alt="App Logo"
                        class="block h-9 w-auto"
                    />
                    <ApplicationLogo
                        v-else
                        class="block h-9 w-auto fill-current text-gray-200"
                    >
                    </ApplicationLogo>
                    <span class="text-xl font-semibold text-white"
                        >LivestockPro
                    </span>
                </Link>
            </div>

            <!-- Navigation - Scrollable -->
            <nav
                class="flex-1 overflow-y-auto py-4 px-3 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-800"
            >
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li v-if="hasFeatureOrSingle('dashboard')">
                        <Link
                            :href="
                                hasRole(['Super Admin', 'admin'])
                                    ? route('admin.dashboard')
                                    : route('dashboard')
                            "
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/dashboard')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                            <span>Dashboard</span>
                        </Link>
                    </li>

                    <!-- Farm Productivity Dashboard -->
                    <li
                        v-if="
                            !isSingleLicenseMode &&
                            hasFeatureOrSingle('farmproductivity')
                        "
                    >
                        <Link
                            :href="route('farm-productivity-dashboard.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/farm-productivity-dashboard')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
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
                            <span>Farm Productivity</span>
                        </Link>
                    </li>

                    <!-- Animals Menu -->
                    <li
                        v-if="hasFeatureOrSingle('animals')"
                    >
                        <button
                            @click="toggleMenu('animals')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('animals')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('animals')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                    />
                                </svg>
                                <span>Animals</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.animals && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.animals"
                            class="ml-8 mt-1 space-y-1 overflow-hidden"
                        >
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('animals.index')"
                                    :class="linkClass('/animals')"
                                    >Animals</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('breeds.index')"
                                    :class="linkClass('/breeds')"
                                    >Breeds</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('herds.index')"
                                    :class="linkClass('/herds')"
                                    >Herds</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('reproduction-records.index')"
                                    :class="linkClass('/reproduction-records')"
                                    >Reproduction</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="
                                        route('artificial-inseminations.index')
                                    "
                                    :class="
                                        linkClass('/artificial-inseminations')
                                    "
                                    >Artificial Inseminations</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('pregnancies.index')"
                                    :class="linkClass('/pregnancies')"
                                    >Pregnancy Records</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('pregnancy-checkups.index')"
                                    :class="linkClass('/pregnancy-checkups')"
                                    >Pregnancy Checkups</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('calving-records.index')"
                                    :class="linkClass('/calving-records')"
                                    >Calving Records</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('animals')">
                                <Link
                                    :href="route('calves.index')"
                                    :class="linkClass('/calves')"
                                    >Newborn Calves</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Health Menu -->
                    <li
                        v-if="hasFeatureOrSingle('healths')"
                    >
                        <button
                            @click="toggleMenu('health')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('health')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('healths')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                    />
                                </svg>
                                <span>Health</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.health && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.health"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('health-issues.index')"
                                    :class="linkClass('/health-issues')"
                                    >Health Issues</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('health-events.index')"
                                    :class="linkClass('/health-events')"
                                    >Health Events</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('diseases.index')"
                                    :class="linkClass('/diseases')"
                                    >Diseases</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('disease-treatments.index')"
                                    :class="linkClass('/disease-treatments')"
                                    >Disease Treatments</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('treatments.index')"
                                    :class="linkClass('/treatments')"
                                    >Treatments</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('vaccinations.index')"
                                    :class="linkClass('/vaccinations')"
                                    >Vaccinations</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('healths')">
                                <Link
                                    :href="route('event-types.index')"
                                    :class="linkClass('/event-types')"
                                    >Event Types</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Feeding Menu -->
                    <li
                        v-if="hasFeatureOrSingle('feedings')"
                    >
                        <button
                            @click="toggleMenu('feeding')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('feeding')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('feedings')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                <span>Feeding</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.feeding && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.feeding"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('feedings')">
                                <Link
                                    :href="route('feedings.index')"
                                    :class="linkClass('/feedings')"
                                    >Feeding Records</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Production Menu -->
                    <li
                        v-if="hasFeatureOrSingle('productions')"
                    >
                        <button
                            @click="toggleMenu('production')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('production')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('productions')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                <span>Production</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.production && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.production"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('productions')">
                                <Link
                                    :href="route('milk-records.index')"
                                    :class="linkClass('/milk-records')"
                                    >Milk Records</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('productions')">
                                <Link
                                    :href="route('milk-sales.index')"
                                    :class="linkClass('/milk-sales')"
                                    >Milk Sales</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Accounts Menu -->
                    <li
                        v-if="hasFeatureOrSingle('accounting')"
                    >
                        <button
                            @click="toggleMenu('accounts')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('accounts')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('accounting')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                <span>Accounts</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.accounts && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.accounts"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('cash-accounts.index')"
                                    :class="linkClass('/cash-accounts')"
                                    >Cash & Bank Management</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('chart-of-accounts.index')"
                                    :class="linkClass('/chart-of-accounts')"
                                    >Chart of Accounts</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('journal-entries.index')"
                                    :class="linkClass('/journal-entries')"
                                    >Journal Entries</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="
                                        route('journal-voucher-report.index')
                                    "
                                    :class="
                                        linkClass('/journal-voucher-report')
                                    "
                                    >Journal Voucher Report</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('balance-sheet.index')"
                                    :class="linkClass('/balance-sheet')"
                                    >Balance Sheet</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('profit-loss.index')"
                                    :class="linkClass('/profit-loss')"
                                    >Profit & Loss</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('cash-flow.index')"
                                    :class="linkClass('/cash-flow')"
                                    >Cash Flow</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('trial-balance.index')"
                                    :class="linkClass('/trial-balance')"
                                    >Trial Balance</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('accounting')">
                                <Link
                                    :href="route('fixed-assets.index')"
                                    :class="linkClass('/fixed-assets')"
                                    >Fixed Assets Register</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Finance Menu -->
                    <li
                        v-if="hasFeatureOrSingle('finance')"
                    >
                        <button
                            @click="toggleMenu('finance')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('finance')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('finance')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span>Finance</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.finance && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.finance"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('finance')">
                                <Link
                                    :href="route('sales.index')"
                                    :class="linkClass('/sales')"
                                    >Sales</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('finance')">
                                <Link
                                    :href="route('purchases.index')"
                                    :class="linkClass('/purchases')"
                                    >Purchases</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Customers Menu -->
                    <li
                        v-if="hasFeatureOrSingle('customers')"
                    >
                        <button
                            @click="toggleMenu('customers')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('customers')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('customers')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h2a2 2 0 002-2V7.5a2.5 2.5 0 00-2.5-2.5h-1.5m-10 0H4a2 2 0 00-2 2v11a2 2 0 002 2h2m0-11V4a2 2 0 012-2h4a2 2 0 012 2v11m-8 0v2a2 2 0 002 2h2a2 2 0 002-2v-2m-6 0H6"
                                    />
                                </svg>
                                <span>Customers</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.customers && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.customers"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('customers')">
                                <Link
                                    :href="route('customers.index')"
                                    :class="linkClass('/customers')"
                                    >Manage Customers</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('customers')">
                                <Link
                                    :href="route('customer-payments.index')"
                                    :class="linkClass('/customer-payments')"
                                    >Customer Payments</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Inventory Menu -->
                    <li
                        v-if="hasFeatureOrSingle('inventory')"
                    >
                        <button
                            @click="toggleMenu('inventory')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('inventory')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('inventory')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    />
                                </svg>
                                <span>Inventory</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.inventory && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.inventory"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('inventory.index')"
                                    :class="linkClass('/inventory')"
                                    >Inventory Items</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('medicines.index')"
                                    :class="linkClass('/medicines')"
                                    >Medicine Items</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('categories.index')"
                                    :class="linkClass('/categories')"
                                    >Categories</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('suppliers.index')"
                                    :class="linkClass('/suppliers')"
                                    >Suppliers</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('supplier-payments.index')"
                                    :class="linkClass('/supplier-payments')"
                                    >Supplier Payments</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('medicine-groups.index')"
                                    :class="linkClass('/medicine-groups')"
                                    >Medicine Groups</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('inventory')">
                                <Link
                                    :href="route('stock-movements.index')"
                                    :class="linkClass('/stock-movements')"
                                    >Stock Movements</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Operations Menu -->
                    <li
                        v-if="hasFeatureOrSingle('operation')"
                    >
                        <button
                            @click="toggleMenu('operations')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('operations')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('operation')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                <span>Operations</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.operations && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.operations"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('operation')">
                                <Link
                                    :href="route('logistics.index')"
                                    :class="linkClass('/logistics')"
                                    >Logistics</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('operation')">
                                <Link
                                    :href="route('farms.index')"
                                    :class="linkClass('/farms')"
                                    >Farms</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Human Resource Menu -->
                    <li
                        v-if="hasFeatureOrSingle('hr')"
                    >
                        <button
                            @click="toggleMenu('humanResource')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('humanResource')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('hr')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h2a2 2 0 002-2V7.5a2.5 2.5 0 00-2.5-2.5h-1.5m-10 0H4a2 2 0 00-2 2v11a2 2 0 002 2h2m0-11V4a2 2 0 012-2h4a2 2 0 012 2v11m-8 0v2a2 2 0 002 2h2a2 2 0 002-2v-2m-6 0H6"
                                    />
                                </svg>
                                <span>HR & Payroll</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.humanResource && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.humanResource"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('departments.index')"
                                    :class="linkClass('/departments')"
                                    >Departments</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')"></li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('designations.index')"
                                    :class="linkClass('/designations')"
                                    >Designations</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('employees.index')"
                                    :class="linkClass('/employees')"
                                    >Employees</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('employee-documents.index')"
                                    :class="linkClass('/employee-documents')"
                                    >Employee Documents</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('shifts.index')"
                                    :class="linkClass('/shifts')"
                                    >Shifts</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('employee-shifts.index')"
                                    :class="linkClass('/employee-shifts')"
                                    >Assign Shifts</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('attendances.index')"
                                    :class="linkClass('/attendances')"
                                    >Attendances</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('leave-types.index')"
                                    :class="linkClass('/leave-types')"
                                    >Leave Types</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('leave-requests.index')"
                                    :class="linkClass('/leave-requests')"
                                    >Leave Requests</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('salary-structures.index')"
                                    :class="linkClass('/salary-structures')"
                                    >Salary Structures</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('payroll-runs.index')"
                                    :class="linkClass('/payroll-runs')"
                                    >Payroll Runs</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('hr')">
                                <Link
                                    :href="route('payroll-items.index')"
                                    :class="linkClass('/payroll-items')"
                                    >Payroll Items</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Reports Menu -->
                    <li
                        v-if="hasFeatureOrSingle('reports')"
                    >
                        <button
                            @click="toggleMenu('reports')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('reports')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('reports')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v12a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <span>Reports</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.reports && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.reports"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="route('reports.animal-health.index')"
                                    :class="linkClass('/reports/animal-health')"
                                    >Animal Health Reports</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')"></li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.feeding-cost-analysis.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/feeding-cost-analysis',
                                        )
                                    "
                                    >Feeding Cost Analysis</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route('reports.vaccination-due.index')
                                    "
                                    :class="
                                        linkClass('/reports/vaccination-due')
                                    "
                                    >Vaccination Due Reports</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="route('reports.financial.index')"
                                    :class="linkClass('/reports/financial')"
                                    >Financial Reports</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.conception-success-rate.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/conception-success-rate',
                                        )
                                    "
                                    >Conception Success Rate</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.pregnancy-loss-analysis.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/pregnancy-loss-analysis',
                                        )
                                    "
                                    >Pregnancy Loss Analysis</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.ai-vs-natural-breeding-success.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/ai-vs-natural-breeding-success',
                                        )
                                    "
                                    >AI vs Natural Breeding Success</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route('reports.calving-interval.index')
                                    "
                                    :class="
                                        linkClass('/reports/calving-interval')
                                    "
                                    >Calving Interval Reports</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.fertility-performance-per-cow.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/fertility-performance-per-cow',
                                        )
                                    "
                                    >Fertility Performance per Cow</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('reports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.treatment-cost-per-animal.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/treatment-cost-per-animal',
                                        )
                                    "
                                    >Treatment Cost per Animal</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Inventory Reports Menu -->
                    <li
                        v-if="hasFeatureOrSingle('invreports')"
                    >
                        <button
                            @click="toggleMenu('inventoryReports')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('inventoryReports')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div
                                class="flex items-center gap-3"
                                v-if="hasFeatureOrSingle('invreports')"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4m4-8h8"
                                    />
                                </svg>
                                <span>Inventory Reports</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.inventoryReports && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.inventoryReports"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.current-stock-by-item.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/current-stock-by-item',
                                        )
                                    "
                                    >Current Stock by Item</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')"></li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.low-stock-alerts.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/low-stock-alerts',
                                        )
                                    "
                                    >Low-stock Alerts</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.expired-medicine.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/expired-medicine',
                                        )
                                    "
                                    >Expired Medicine Report</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.feed-consumed-per-animal.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/feed-consumed-per-animal',
                                        )
                                    "
                                    >Feed Consumed per Animal</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.cost-of-feed-per-cow.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/cost-of-feed-per-cow',
                                        )
                                    "
                                    >Cost of Feed per Cow</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.medicine-used-per-disease.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/medicine-used-per-disease',
                                        )
                                    "
                                    >Medicine Used per Disease</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.monthly-consumption-summary.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/monthly-consumption-summary',
                                        )
                                    "
                                    >Monthly Consumption Summary</Link
                                >
                            </li>
                            <li v-if="hasFeatureOrSingle('invreports')">
                                <Link
                                    :href="
                                        route(
                                            'reports.inventory.wastage-loss.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/reports/inventory/wastage-loss',
                                        )
                                    "
                                    >Wastage & Loss Report</Link
                                >
                            </li>
                        </ul>
                    </li>
                    <!-- Subscription Plans & Features Menu (SaaS mode only) -->
                    <li
                        v-if="isSaasMode && hasRole(['Super Admin', 'admin'])"
                    >
                        <button
                            @click="toggleMenu('subscriptions')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('subscriptions')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div class="flex items-start gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span>Plans & Features</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.subscriptions && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.subscriptions"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li>
                                <Link
                                    :href="
                                        route('admin.subscription-plans.index')
                                    "
                                    :class="
                                        linkClass('/admin/subscription-plans')
                                    "
                                    >Manage Plans</Link
                                >
                            </li>
                            <li>
                                <Link
                                    :href="
                                        route(
                                            'admin.subscription-features.index',
                                        )
                                    "
                                    :class="
                                        linkClass(
                                            '/admin/subscription-features',
                                        )
                                    "
                                    >Manage Features</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Administration Menu -->
                    <li v-if="hasRole(['Super Admin', 'admin'])">
                        <button
                            @click="toggleMenu('admin')"
                            :class="[
                                'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isMenuActive('admin')
                                    ? 'bg-gray-700 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                                    />
                                </svg>
                                <span>Administration</span>
                            </div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                :class="[
                                    'h-4 w-4 transition-transform duration-200',
                                    openMenus.admin && 'rotate-180',
                                ]"
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
                        <ul
                            v-show="openMenus.admin"
                            class="ml-8 mt-1 space-y-1"
                        >
                            <li v-if="!isSingleLicenseMode">
                                <Link
                                    :href="route('admin.dashboard')"
                                    :class="linkClass('/admin/dashboard')"
                                    >Dashboard</Link
                                >
                            </li>
                            <li v-if="!isSingleLicenseMode">
                                <Link
                                    :href="route('admin.collections')"
                                    :class="linkClass('/admin/collections')"
                                    >Collections</Link
                                >
                            </li>

                            <!-- Super Admin only -->
                            <li
                                v-if="!isSingleLicenseMode && hasRole('Super Admin')"
                            >
                                <Link
                                    :href="route('admin.farms.index')"
                                    :class="linkClass('/admin/farms')"
                                    >Farms Directory</Link
                                >
                            </li>

                            <!-- Super Admin only -->
                            <li
                                v-if="hasRole('Super Admin')"
                            >
                                <Link
                                    :href="route('admin.settings.website.edit')"
                                    :class="
                                        linkClass('/admin/settings/website')
                                    "
                                    >Website Settings</Link
                                >
                            </li>

                            <!-- Super Admin only -->
                            <li
                                v-if="hasRole('Super Admin')"
                            >
                                <Link
                                    :href="route('admin.settings.email.edit')"
                                    :class="linkClass('/admin/settings/email')"
                                    >Email Settings</Link
                                >
                            </li>

                            <!-- Super Admin only -->
                            <li
                                v-if="hasRole('Super Admin')"
                            >
                                <Link
                                    :href="route('admin.demo-requests.index')"
                                    :class="linkClass('/admin/demo-requests')"
                                    >Demo Requests</Link
                                >
                            </li>

                            <li>
                                <Link
                                    :href="route('admin.roles.index')"
                                    :class="linkClass('/admin/roles')"
                                    >Roles</Link
                                >
                            </li>
                            <li>
                                <Link
                                    :href="route('admin.permissions.index')"
                                    :class="linkClass('/admin/permissions')"
                                    >Permissions</Link
                                >
                            </li>
                            <li>
                                <Link
                                    :href="route('admin.users.index')"
                                    :class="linkClass('/admin/users')"
                                    >Users</Link
                                >
                            </li>
                            <li>
                                <Link
                                    :href="route('admin.assignRoles')"
                                    :class="linkClass('/admin/assign-roles')"
                                    >Assign Roles</Link
                                >
                            </li>
                        </ul>
                    </li>

                    <!-- Plan (Farm Owner only) - hide in single-license mode -->
                    <li
                        class="pt-2"
                        v-if="
                            !isSingleLicenseMode &&
                            user?.roles?.includes('farm owner')
                        "
                    >
                        <Link
                            :href="route('plan.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/plan')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M4 3a2 2 0 00-2 2v2a2 2 0 002 2h1v6H4a2 2 0 00-2 2v1h16v-1a2 2 0 00-2-2h-1V9h1a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 6h6v6H7V9z"
                                />
                            </svg>
                            <span>Plan</span>
                        </Link>
                    </li>

                    <!-- Billing (Farm Owner only) - hide in single-license mode -->
                    <li
                        class="pt-2"
                        v-if="
                            !isSingleLicenseMode &&
                            page.props.value.auth?.user?.roles?.includes(
                                'farm owner',
                            )
                        "
                    >
                        <Link
                            :href="route('billing.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/billing')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M4 4a2 2 0 012-2h8a2 2 0 012 2v2H4V4z"
                                />
                                <path
                                    fill-rule="evenodd"
                                    d="M4 8h12v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8zm3 2a1 1 0 000 2h6a1 1 0 100-2H7z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>Subscription & Billing</span>
                        </Link>
                    </li>

                    <!-- Profile -->
                    <li class="pt-2">
                        <Link
                            :href="route('profile.edit')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/profile')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>Profile</span>
                        </Link>
                    </li>
                    <!-- Settings -->
                    <li
                        class="pt-2"
                        v-if="
                            page.props.value.auth?.user?.roles?.includes(
                                'farm owner',
                            )
                        "
                    >
                        <Link
                            :href="route('settings.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/settings')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M11.49 3.17c-.38-1.16-1.47-1.16-1.85 0a1.724 1.724 0 01-2.573 1.066c-1.543-.94-3.31.826-2.37 2.37a1.724 1.724 0 01-1.065 2.572c-1.16.38-1.16 1.47 0 1.85a1.724 1.724 0 011.066 2.573c-.94 1.543.826 3.31 2.37 2.37a1.724 1.724 0 012.572 1.065c.38 1.16 1.47 1.16 1.85 0a1.724 1.724 0 012.573-1.066c1.543.94 3.31-.826 2.37-2.37a1.724 1.724 0 011.065-2.572c1.16-.38 1.16-1.47 0-1.85a1.724 1.724 0 01-1.066-2.573c.94-1.543-.826-3.31-2.37-2.37a1.724 1.724 0 01-2.572-1.065zM10 11a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>Settings</span>
                        </Link>
                    </li>

                    <li
                        class="pt-2"
                        v-if="!isSingleLicenseMode && hasRole(['Super Admin', 'admin'])"
                    >
                        <Link
                            :href="route('settings.payment-gateways.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200',
                                isActive('/settings/payment-gateways')
                                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                            ]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M11.49 3.17c-.38-1.16-1.47-1.16-1.85 0a1.724 1.724 0 01-2.573 1.066c-1.543-.94-3.31.826-2.37 2.37a1.724 1.724 0 01-1.065 2.572c-1.16.38-1.16 1.47 0 1.85a1.724 1.724 0 011.066 2.573c-.94 1.543.826 3.31 2.37 2.37a1.724 1.724 0 012.572 1.065c.38 1.16 1.47 1.16 1.85 0a1.724 1.724 0 012.573-1.066c1.543.94 3.31-.826 2.37-2.37a1.724 1.724 0 011.065-2.572c1.16-.38 1.16-1.47 0-1.85a1.724 1.724 0 01-1.066-2.573c.94-1.543-.826-3.31-2.37-2.37a1.724 1.724 0 01-2.572-1.065zM10 11a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>Payment Gateways</span>
                        </Link>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div
            :class="{ 'ml-64': showingSidebar }"
            class="flex-1 flex flex-col h-screen transition-all duration-300 ease-in-out lg:ml-64"
        >
            <!-- Header - Fixed at top -->
            <header
                class="bg-white border-b shadow-sm p-4 flex justify-between items-center sticky top-0 z-20 app-header"
            >
                <!-- Hamburger for mobile -->
                <button
                    @click="showingSidebar = !showingSidebar"
                    class="lg:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{
                                hidden: showingSidebar,
                                'inline-flex': !showingSidebar,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{
                                hidden: !showingSidebar,
                                'inline-flex': showingSidebar,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
                <div>
                    <slot name="title"></slot>
                </div>
                <div class="flex items-center gap-4">
                    <div v-if="user" class="flex items-center gap-3">
                        <span class="text-sm text-gray-600"
                            >Hello, {{ user.name }}</span
                        >

                        <!-- Admin Dashboard quick link (Super Admin/Admin only) -->
                        <Link
                            v-if="
                                !isSingleLicenseMode &&
                                hasRole(['Super Admin', 'admin'])
                            "
                            :href="route('admin.dashboard')"
                            class="px-3 py-2 text-sm bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-medium shadow-sm transition duration-200"
                        >
                            Admin Dashboard
                        </Link>

                        <button
                            @click="logout"
                            class="ml-1 px-4 py-2 text-sm bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition duration-200"
                        >
                            Logout
                        </button>
                    </div>
                    <div v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                            >Login</Link
                        >
                        <Link
                            :href="route('register')"
                            class="ml-3 px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                            >Register</Link
                        >
                    </div>
                    <div>
                        <slot name="actions"></slot>
                    </div>
                </div>
            </header>

            <!-- Main Content - Scrollable -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <slot />
            </main>
        </div>

        <!-- Toast Notification -->
        <ToastNotification ref="toast" />
    </div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, reactive, onMounted, ref, watch } from "vue";
import ToastNotification from "@/Components/ToastNotification.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue"; // Import ApplicationLogo
import FarmSwitcher from "@/Components/FarmSwitcher.vue";

const page = usePage();
const user = computed(() => page.props.value.auth?.user ?? null);
const roles = computed(() => {
    const rawRoles = page.props.value.auth?.user?.roles ?? [];
    return rawRoles.map((r) => (typeof r === "string" ? r : r.name));
});
const appSettings = computed(() => usePage().props.value?.appSettings || {}); // Safely access appSettings with optional chaining
const subscription = computed(() => usePage().props.value?.subscription || {});
const enabledFeatures = computed(() => subscription.value?.features || []);
const hasFeature = (key) => enabledFeatures.value.includes(key);

// In single-license mode we bypass subscription feature gating entirely.
const hasFeatureOrSingle = (key) =>
    isSingleLicenseMode.value || hasRole(["Super Admin", "admin"])
        ? true
        : hasFeature(key);

const hasRole = (roleName) => {
    if (Array.isArray(roleName)) {
        return roleName.some((r) => roles.value.includes(r));
    }
    return roles.value.includes(roleName);
};

// App mode flags (provided by HandleInertiaRequests)
const isSaasMode = computed(() => !!page.props.value?.app_mode?.saas_mode);
const isSingleLicenseMode = computed(
    () => !!page.props.value?.app_mode?.single_license_mode,
);

const farmContext = computed(() => page.props.value?.farm_context ?? {});

const showingSidebar = ref(window.innerWidth >= 1024); // Show sidebar by default on larger screens

const openMenus = reactive({
    animals: false,
    health: false,
    feeding: false,
    production: false,
    finance: false,
    inventory: false,
    operations: false,
    reports: false,
    inventoryReports: false,
    subscriptions: false,
    admin: true,
    humanResource: false,
    payroll: false, // Add payroll menu state
    accounts: false, // Add accounts menu state
    customers: false, // Add customers menu state
});

const currentPath = computed(() => page.url.value || "");

const isActive = (path) => {
    return currentPath.value === path;
};

const isMenuActive = (menuKey) => {
    const menuRoutes = {
        dashboard: ["/dashboard", "/farm-productivity-dashboard"],
        adminDashboard: ["/admin/dashboard"],
        animals: [
            "/animals",
            "/breeds",
            "/herds",
            "/reproduction-records",
            "/artificial-inseminations",
            "/pregnancies",
            "/pregnancy-checkups",
            "/calving-records",
            "/calves",
        ],
        finance: ["/expenses", "/sales", "/purchases"],
        customers: ["/customers", "/customer-payments"], // Add customers routes
        health: [
            "/health-issues",
            "/health-events",
            "/disease-treatments",
            "/treatments",
            "/vaccinations",
            "/vaccine-types",
            "/event-types",
            "/diseases",
        ],
        feeding: ["/feedings", "/feed-types"],
        production: ["/milk-records", "/milk-sales"],
        inventory: [
            "/inventory",
            "/medicines",
            "/categories",
            "/suppliers",
            "/supplier-payments",
            "/medicine-groups",
            "/stock-movements",
        ],
        operations: ["/logistics", "/farms", "/staff"],
        subscriptions: [
            "/admin/subscription-plans",
            "/admin/subscription-features",
        ],
        admin: [
            "/admin/dashboard",
            "/admin/collections",
            "/admin/farms",
            "/admin/roles",
            "/admin/permissions",
            "/admin/users",
            "/admin/assign-roles",
        ],
        humanResource: [
            "/departments",
            "/designations",
            "/employees",
            "/employee-documents",
            "/shifts",
            "/employee-shifts",
            "/attendances",
            "/leave-types",
            "/leave-requests",
            "/salary-structures",
            "/payroll-runs", // Add payroll runs route
            "/payroll-items",
        ],
        accounts: [
            "/chart-of-accounts",
            "/journal-entries",
            "/journal-voucher-report",
            "/balance-sheet",
            "/profit-loss",
            "/cash-flow",
            "/trial-balance",
            "/cash-accounts",
            "/fixed-assets",
        ],
        reports: [
            "/reports/animal-health",
            "/reports/feeding-cost-analysis",
            "/reports/vaccination-due",
            "/reports/financial",
            "/reports/conception-success-rate",
            "/reports/pregnancy-loss-analysis",
            "/reports/ai-vs-natural-breeding-success",
            "/reports/calving-interval",
            "/reports/fertility-performance-per-cow",
            "/reports/treatment-cost-per-animal",
        ],
        inventoryReports: [
            "/reports/inventory/current-stock-by-item",
            "/reports/inventory/low-stock-alerts",
            "/reports/inventory/expired-medicine",
            "/reports/inventory/feed-consumed-per-animal",
            "/reports/inventory/cost-of-feed-per-cow",
            "/reports/inventory/medicine-used-per-disease",
            "/reports/inventory/monthly-consumption-summary",
            "/reports/inventory/wastage-loss",
        ],
        settings: ["/settings"], // Add settings route
        plan: ["/plan"],
        billing: ["/billing"], // Add billing route
    };

    return (
        menuRoutes[menuKey]?.some((route) =>
            currentPath.value.startsWith(route),
        ) || false
    );
};

const linkClass = (path) => {
    return [
        "block px-3 py-2 rounded-lg text-sm transition-all duration-200",
        currentPath.value.startsWith(path)
            ? "bg-blue-600 text-white shadow-md"
            : "text-gray-400 hover:bg-gray-700 hover:text-white",
    ];
};

const toggleMenu = (menuKey) => {
    openMenus[menuKey] = !openMenus[menuKey];
};

onMounted(() => {
    Object.keys(openMenus).forEach((menuKey) => {
        if (isMenuActive(menuKey)) {
            openMenus[menuKey] = true;
        }
    });

    // Close sidebar on smaller screens when navigating
    if (window.innerWidth < 1024) {
        showingSidebar.value = false;
    }
});

// Watch for route changes to close sidebar on mobile
watch(currentPath, () => {
    if (window.innerWidth < 1024) {
        showingSidebar.value = false;
    }
});

// Update showingSidebar on window resize
onMounted(() => {
    window.addEventListener("resize", () => {
        showingSidebar.value = window.innerWidth >= 1024;
    });
});

const toast = ref(null);

watch(
    () => page.props.value.flash ?? {},
    (flash) => {
        console.log("Inertia Flash:", flash); // Add console log for debugging flash messages
        if (toast.value) {
            if (flash.success) {
                toast.value.showToast(flash.success, "success");
            } else if (flash.error) {
                toast.value.showToast(flash.error, "error");
            } else if (flash.info) {
                toast.value.showToast(flash.info, "info");
            }
        }
    },
    { deep: true },
);

// Watch for Inertia errors
watch(
    () => page.props.value.errors ?? {},
    (errors) => {
        console.log("Inertia Errors:", errors); // Add console log for debugging
        if (toast.value && Object.keys(errors).length > 0) {
            // Display the first error message found
            const firstError = Object.values(errors)[0];
            toast.value.showToast(firstError, "error");
        }
    },
    { deep: true },
);

function logout() {
    Inertia.post(route("logout"));
}
</script>

<style scoped>
/* Custom scrollbar styling for sidebar */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #1f2937;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

@media print {
    aside,
    header {
        display: none !important;
    }
    main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
}
</style>
