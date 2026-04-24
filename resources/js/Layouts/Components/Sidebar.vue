<template>
    <aside
        id="primary-sidebar"
        :aria-label="$t('primary_navigation')"
        :class="{
            'translate-x-0': showing,
            '-translate-x-full': !showing,
        }"
        class="w-64 bg-white border-r border-gray-200 shadow-sm fixed left-0 top-0 h-screen flex flex-col z-30 transition-transform duration-300 ease-in-out lg:translate-x-0 app-sidebar"
    >
        <!-- Logo/Brand - Fixed at top -->
        <div class="p-5 border-b border-gray-100 flex-shrink-0">
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
                    alt=""
                    class="block h-9 w-auto"
                />
                <ApplicationLogo
                    v-else
                    class="block h-9 w-auto fill-current text-green-600"
                >
                </ApplicationLogo>
                <span class="text-xl font-semibold text-slate-900"
                    >Vacaliza
                </span>
            </Link>
        </div>

        <!-- Navigation - Scrollable -->
        <nav
            :aria-label="$t('primary_navigation')"
            class="flex-1 overflow-y-auto py-4 px-3 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent"
        >
            <ul class="space-y-1">
                <li v-if="sectionHas.operations" class="px-3 pt-1 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('operations')"
                        :aria-expanded="openSections.operations"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_operations') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.operations }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.operations && openSections.operations">
                <!-- Dashboard -->
                <li v-if="hasFeatureOrSingle('dashboard')">
                    <Link
                        :href="
                            hasRole(['Super Admin', 'admin'])
                                ? route('admin.dashboard')
                                : route('dashboard')
                        "
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/dashboard')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/dashboard') ? 'page' : undefined"
                    >
                        <Home aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span>{{ $t('dashboard') }}</span>
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
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/farm-productivity-dashboard')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/farm-productivity-dashboard') ? 'page' : undefined"
                    >
                        <BarChart3 aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span>{{ $t('farm_productivity') }}</span>
                    </Link>
                </li>

                </template>
                <li v-if="sectionHas.livestock" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('livestock')"
                        :aria-expanded="openSections.livestock"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_livestock') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.livestock }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.livestock && openSections.livestock">
                <!-- Animals Menu -->
                <li
                    v-if="hasFeatureOrSingle('animals')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('animals')"
                        :aria-expanded="openMenus.animals"
                        aria-controls="submenu-animals"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('animals')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('animals')"
                        >
                            <PawPrint aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('animals') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.animals && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-animals"
                        v-show="openMenus.animals"
                        class="ml-8 mt-1 space-y-1 overflow-hidden"
                    >
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('animals.index')"
                                v-bind="linkAttrs('/animals')"
                                >{{ $t('animals') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('breeds.index')"
                                v-bind="linkAttrs('/breeds')"
                                >{{ $t('breeds') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('herds.index')"
                                v-bind="linkAttrs('/herds')"
                                >{{ $t('herds') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('reproduction-records.index')"
                                v-bind="linkAttrs('/reproduction-records')"
                                >{{ $t('reproduction') }}</Link
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
                                >{{ $t('artificial_inseminations') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('pregnancies.index')"
                                v-bind="linkAttrs('/pregnancies')"
                                >{{ $t('pregnancy_records') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('pregnancy-checkups.index')"
                                v-bind="linkAttrs('/pregnancy-checkups')"
                                >{{ $t('pregnancy_checkups') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('calving-records.index')"
                                v-bind="linkAttrs('/calving-records')"
                                >{{ $t('calving_records') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('animals')">
                            <Link
                                :href="route('calves.index')"
                                v-bind="linkAttrs('/calves')"
                                >{{ $t('newborn_calves') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Health Menu -->
                <li
                    v-if="hasFeatureOrSingle('healths')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('health')"
                        :aria-expanded="openMenus.health"
                        aria-controls="submenu-health"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('health')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('healths')"
                        >
                            <HeartPulse aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('health') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.health && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-health"
                        v-show="openMenus.health"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('health-issues.index')"
                                v-bind="linkAttrs('/health-issues')"
                                >{{ $t('health_issues') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('health-events.index')"
                                v-bind="linkAttrs('/health-events')"
                                >{{ $t('health_events') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('diseases.index')"
                                v-bind="linkAttrs('/diseases')"
                                >{{ $t('diseases') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('disease-treatments.index')"
                                v-bind="linkAttrs('/disease-treatments')"
                                >{{ $t('disease_treatments') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('treatments.index')"
                                v-bind="linkAttrs('/treatments')"
                                >{{ $t('treatments') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('vaccinations.index')"
                                v-bind="linkAttrs('/vaccinations')"
                                >{{ $t('vaccinations') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('healths')">
                            <Link
                                :href="route('event-types.index')"
                                v-bind="linkAttrs('/event-types')"
                                >{{ $t('event_types') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Feeding Menu -->
                <li
                    v-if="hasFeatureOrSingle('feedings')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('feeding')"
                        :aria-expanded="openMenus.feeding"
                        aria-controls="submenu-feeding"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('feeding')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('feedings')"
                        >
                            <Wheat aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('feeding') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.feeding && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-feeding"
                        v-show="openMenus.feeding"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('feedings')">
                            <Link
                                :href="route('feedings.index')"
                                v-bind="linkAttrs('/feedings')"
                                >{{ $t('feeding_records') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Production Menu -->
                <li
                    v-if="hasFeatureOrSingle('productions')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('production')"
                        :aria-expanded="openMenus.production"
                        aria-controls="submenu-production"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('production')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('productions')"
                        >
                            <Droplet aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('production') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.production && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-production"
                        v-show="openMenus.production"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('productions')">
                            <Link
                                :href="route('milk-records.index')"
                                v-bind="linkAttrs('/milk-records')"
                                >{{ $t('milk_records') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('productions')">
                            <Link
                                :href="route('milk-sales.index')"
                                v-bind="linkAttrs('/milk-sales')"
                                >{{ $t('milk_sales') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                </template>
                <li v-if="sectionHas.finance" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('finance')"
                        :aria-expanded="openSections.finance"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_finance') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.finance }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.finance && openSections.finance">
                <!-- Accounts Menu -->
                <li
                    v-if="hasFeatureOrSingle('accounting')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('accounts')"
                        :aria-expanded="openMenus.accounts"
                        aria-controls="submenu-accounts"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('accounts')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('accounting')"
                        >
                            <Landmark aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('accounts') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.accounts && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-accounts"
                        v-show="openMenus.accounts"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('cash-accounts.index')"
                                v-bind="linkAttrs('/cash-accounts')"
                                >{{ $t('cash_bank_management') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('chart-of-accounts.index')"
                                v-bind="linkAttrs('/chart-of-accounts')"
                                >{{ $t('chart_of_accounts') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('journal-entries.index')"
                                v-bind="linkAttrs('/journal-entries')"
                                >{{ $t('journal_entries') }}</Link
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
                                >{{ $t('journal_voucher_report') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('balance-sheet.index')"
                                v-bind="linkAttrs('/balance-sheet')"
                                >{{ $t('balance_sheet') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('profit-loss.index')"
                                v-bind="linkAttrs('/profit-loss')"
                                >{{ $t('profit_loss') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('cash-flow.index')"
                                v-bind="linkAttrs('/cash-flow')"
                                >{{ $t('cash_flow') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('trial-balance.index')"
                                v-bind="linkAttrs('/trial-balance')"
                                >{{ $t('trial_balance') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('accounting')">
                            <Link
                                :href="route('fixed-assets.index')"
                                v-bind="linkAttrs('/fixed-assets')"
                                >{{ $t('fixed_assets_register') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Finance Menu -->
                <li
                    v-if="hasFeatureOrSingle('finance')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('finance')"
                        :aria-expanded="openMenus.finance"
                        aria-controls="submenu-finance"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('finance')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('finance')"
                        >
                            <CircleDollarSign aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('finance') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.finance && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-finance"
                        v-show="openMenus.finance"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('finance')">
                            <Link
                                :href="route('sales.index')"
                                v-bind="linkAttrs('/sales')"
                                >{{ $t('sales') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('finance')">
                            <Link
                                :href="route('purchases.index')"
                                v-bind="linkAttrs('/purchases')"
                                >{{ $t('purchases') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Customers Menu -->
                <li
                    v-if="hasFeatureOrSingle('customers')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('customers')"
                        :aria-expanded="openMenus.customers"
                        aria-controls="submenu-customers"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('customers')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('customers')"
                        >
                            <Users aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('customers') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.customers && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-customers"
                        v-show="openMenus.customers"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('customers')">
                            <Link
                                :href="route('customers.index')"
                                v-bind="linkAttrs('/customers')"
                                >{{ $t('manage_customers') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('customers')">
                            <Link
                                :href="route('customer-payments.index')"
                                v-bind="linkAttrs('/customer-payments')"
                                >{{ $t('customer_payments') }}</Link
                            >
                        </li>
                    </ul>
                </li>

                </template>
                <li v-if="sectionHas.inventory" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('inventory')"
                        :aria-expanded="openSections.inventory"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_inventory') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.inventory }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.inventory && openSections.inventory">
                <!-- Inventory Menu -->
                <li
                    v-if="hasFeatureOrSingle('inventory')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('inventory')"
                        :aria-expanded="openMenus.inventory"
                        aria-controls="submenu-inventory"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('inventory')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('inventory')"
                        >
                            <Package aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span>{{ $t('inventory') }}</span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.inventory && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-inventory"
                        v-show="openMenus.inventory"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('inventory.index')"
                                v-bind="linkAttrs('/inventory')"
                                >{{ $t('inventory_items') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('medicines.index')"
                                v-bind="linkAttrs('/medicines')"
                                >{{ $t('medicine_items') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('categories.index')"
                                v-bind="linkAttrs('/categories')"
                                >{{ $t('categories') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('suppliers.index')"
                                v-bind="linkAttrs('/suppliers')"
                                >{{ $t('suppliers') }}</Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('supplier-payments.index')"
                                v-bind="linkAttrs('/supplier-payments')"
                                > {{ $t('supplier_payments') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('medicine-groups.index')"
                                v-bind="linkAttrs('/medicine-groups')"
                                > {{ $t('medicine_groups') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('inventory')">
                            <Link
                                :href="route('stock-movements.index')"
                                v-bind="linkAttrs('/stock-movements')"
                                > {{ $t('stock_movements') }} </Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Operations Menu -->
                <li
                    v-if="hasFeatureOrSingle('operation')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('operations')"
                        :aria-expanded="openMenus.operations"
                        aria-controls="submenu-operations"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('operations')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('operation')"
                        >
                            <Truck aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span> {{ $t('operations') }} </span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.operations && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-operations"
                        v-show="openMenus.operations"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('operation')">
                            <Link
                                :href="route('logistics.index')"
                                v-bind="linkAttrs('/logistics')"
                                > {{ $t('logistics') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('operation')">
                            <Link
                                :href="route('farms.index')"
                                v-bind="linkAttrs('/farms')"
                                > {{ $t('farms') }} </Link
                            >
                        </li>
                    </ul>
                </li>

                </template>
                <li v-if="sectionHas.hr" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('hr')"
                        :aria-expanded="openSections.hr"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_hr') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.hr }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.hr && openSections.hr">
                <!-- Human Resource Menu -->
                <li
                    v-if="hasFeatureOrSingle('hr')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('humanResource')"
                        :aria-expanded="openMenus.humanResource"
                        aria-controls="submenu-humanResource"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('humanResource')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('hr')"
                        >
                            <BriefcaseBusiness aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span> {{ $t('hr_payroll') }} </span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.humanResource && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-humanResource"
                        v-show="openMenus.humanResource"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('departments.index')"
                                v-bind="linkAttrs('/departments')"
                                > {{ $t('departments') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')"></li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('designations.index')"
                                v-bind="linkAttrs('/designations')"
                                > {{ $t('designations') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('employees.index')"
                                v-bind="linkAttrs('/employees')"
                                > {{ $t('employees') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('employee-documents.index')"
                                v-bind="linkAttrs('/employee-documents')"
                                > {{ $t('employee_documents') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('shifts.index')"
                                v-bind="linkAttrs('/shifts')"
                                > {{ $t('shifts') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('employee-shifts.index')"
                                v-bind="linkAttrs('/employee-shifts')"
                                > {{ $t('assign_shifts') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('attendances.index')"
                                v-bind="linkAttrs('/attendances')"
                                > {{ $t('attendances') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('leave-types.index')"
                                v-bind="linkAttrs('/leave-types')"
                                > {{ $t('leave_types') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('leave-requests.index')"
                                v-bind="linkAttrs('/leave-requests')"
                                > {{ $t('leave_requests') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('salary-structures.index')"
                                v-bind="linkAttrs('/salary-structures')"
                                > {{ $t('salary_structures') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('payroll-runs.index')"
                                v-bind="linkAttrs('/payroll-runs')"
                                > {{ $t('payroll_runs') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('hr')">
                            <Link
                                :href="route('payroll-items.index')"
                                v-bind="linkAttrs('/payroll-items')"
                                > {{ $t('payroll_items') }} </Link
                            >
                        </li>
                    </ul>
                </li>

                </template>
                <li v-if="sectionHas.reports" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('reports')"
                        :aria-expanded="openSections.reports"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_reports') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.reports }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.reports && openSections.reports">
                <!-- Reports Menu -->
                <li
                    v-if="hasFeatureOrSingle('reports')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('reports')"
                        :aria-expanded="openMenus.reports"
                        aria-controls="submenu-reports"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('reports')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('reports')"
                        >
                            <ClipboardList aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span> {{ $t('reports') }} </span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.reports && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-reports"
                        v-show="openMenus.reports"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="hasFeatureOrSingle('reports')">
                            <Link
                                :href="route('reports.animal-health.index')"
                                v-bind="linkAttrs('/reports/animal-health')"
                                > {{ $t('animal_health_reports') }} </Link
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
                                > {{ $t('feeding_cost_analysis') }} </Link
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
                                > {{ $t('vaccination_due_reports') }} </Link
                            >
                        </li>
                        <li v-if="hasFeatureOrSingle('reports')">
                            <Link
                                :href="route('reports.financial.index')"
                                v-bind="linkAttrs('/reports/financial')"
                                > {{ $t('financial_reports') }} </Link
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
                                > {{ $t('conception_success_rate') }} </Link
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
                                > {{ $t('pregnancy_loss_analysis') }} </Link
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
                                > {{ $t('ai_vs_natural_breeding_success') }} </Link
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
                                > {{ $t('calving_interval_reports') }} </Link
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
                                > {{ $t('fertility_performance_per_cow') }} </Link
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
                                > {{ $t('treatment_cost_per_animal') }} </Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Inventory Reports Menu -->
                <li
                    v-if="hasFeatureOrSingle('invreports')"
                >
                    <button
                        type="button"
                        @click="toggleMenu('inventoryReports')"
                        :aria-expanded="openMenus.inventoryReports"
                        aria-controls="submenu-inventoryReports"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('inventoryReports')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div
                            class="flex items-center gap-3"
                            v-if="hasFeatureOrSingle('invreports')"
                        >
                            <LineChart aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span> {{ $t('inventory_reports') }} </span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.inventoryReports && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-inventoryReports"
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
                                > {{ $t('current_stock_by_item') }} </Link
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
                                > {{ $t('low_stock_alerts') }} </Link
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
                                > {{ $t('expired_medicine_report') }} </Link
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
                                > {{ $t('feed_consumed_per_animal') }} </Link
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
                                > {{ $t('cost_of_feed_per_cow') }} </Link
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
                                > {{ $t('medicine_used_per_disease') }} </Link
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
                                > {{ $t('monthly_consumption_summary') }} </Link
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
                                > {{ $t('wastage_loss_report') }} </Link
                            >
                        </li>
                    </ul>
                </li>
                </template>
                <li v-if="sectionHas.administration" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('administration')"
                        :aria-expanded="openSections.administration"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_administration') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.administration }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.administration && openSections.administration">
                <!-- Subscription Plans & Features Menu (SaaS mode only) -->
                <li
                    v-if="isSaasMode && hasRole(['Super Admin', 'admin'])"
                >
                    <button
                        type="button"
                        @click="toggleMenu('subscriptions')"
                        :aria-expanded="openMenus.subscriptions"
                        aria-controls="submenu-subscriptions"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('subscriptions')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div class="flex items-start gap-3">
                            <CreditCard aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span> {{ $t('plans_features') }} </span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.subscriptions && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-subscriptions"
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
                                > {{ $t('manage_plans') }} </Link
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
                                > {{ $t('manage_features') }} </Link
                            >
                        </li>
                    </ul>
                </li>

                <!-- Administration Menu -->
                <li v-if="hasRole(['Super Admin', 'admin'])">
                    <button
                        type="button"
                        @click="toggleMenu('admin')"
                        :aria-expanded="openMenus.admin"
                        aria-controls="submenu-admin"
                        :class="[
                            'w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isMenuActive('admin')
                                ? 'text-green-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <ShieldCheck aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                            <span> {{ $t('administration') }} </span>
                        </div>
                        <ChevronDown aria-hidden="true" :class="['h-4 w-4 transition-transform duration-200 text-slate-400', openMenus.admin && 'rotate-180']" :stroke-width="2" />
                    </button>
                    <ul
                        id="submenu-admin"
                        v-show="openMenus.admin"
                        class="ml-8 mt-1 space-y-1"
                    >
                        <li v-if="!isSingleLicenseMode">
                            <Link
                                :href="route('admin.dashboard')"
                                v-bind="linkAttrs('/admin/dashboard')"
                                >{{ $t('dashboard') }}</Link
                            >
                        </li>
                        <li v-if="!isSingleLicenseMode">
                            <Link
                                :href="route('admin.collections')"
                                v-bind="linkAttrs('/admin/collections')"
                                > {{ $t('collections') }} </Link
                            >
                        </li>

                        <!-- Super Admin only -->
                        <li
                            v-if="!isSingleLicenseMode && hasRole('Super Admin')"
                        >
                            <Link
                                :href="route('admin.farms.index')"
                                v-bind="linkAttrs('/admin/farms')"
                                > {{ $t('farms_directory') }} </Link
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
                                > {{ $t('website_settings') }} </Link
                            >
                        </li>

                        <!-- Super Admin only -->
                        <li
                            v-if="hasRole('Super Admin')"
                        >
                            <Link
                                :href="route('admin.settings.email.edit')"
                                v-bind="linkAttrs('/admin/settings/email')"
                                > {{ $t('email_settings') }} </Link
                            >
                        </li>

                        <!-- Super Admin only -->
                        <li
                            v-if="hasRole('Super Admin')"
                        >
                            <Link
                                :href="route('admin.demo-requests.index')"
                                v-bind="linkAttrs('/admin/demo-requests')"
                                > {{ $t('demo_requests') }} </Link
                            >
                        </li>

                        <li>
                            <Link
                                :href="route('admin.roles.index')"
                                v-bind="linkAttrs('/admin/roles')"
                                > {{ $t('roles') }} </Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="route('admin.permissions.index')"
                                v-bind="linkAttrs('/admin/permissions')"
                                > {{ $t('permissions') }} </Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="route('admin.users.index')"
                                v-bind="linkAttrs('/admin/users')"
                                > {{ $t('users') }} </Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="route('admin.assignRoles')"
                                v-bind="linkAttrs('/admin/assign-roles')"
                                > {{ $t('assign_roles') }} </Link
                            >
                        </li>
                    </ul>
                </li>

                </template>
                <li v-if="sectionHas.account" class="px-3 pt-5 pb-1">
                    <button
                        type="button"
                        @click="toggleSection('account')"
                        :aria-expanded="openSections.account"
                        class="w-full flex items-center justify-between py-1 hover:text-slate-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 rounded"
                    >
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $t('section_account') }}
                        </h3>
                        <ChevronDown aria-hidden="true" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="{ 'rotate-180': openSections.account }" :stroke-width="2" />
                    </button>
                </li>
                <template v-if="sectionHas.account && openSections.account">
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
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/plan')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/plan') ? 'page' : undefined"
                    >
                        <CalendarDays aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span> {{ $t('plan') }} </span>
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
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/billing')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/billing') ? 'page' : undefined"
                    >
                        <Receipt aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span> {{ $t('subscription_billing') }} </span>
                    </Link>
                </li>

                <!-- Profile -->
                <li class="pt-2">
                    <Link
                        :href="route('profile.edit')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/profile')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/profile') ? 'page' : undefined"
                    >
                        <UserCircle aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span> {{ $t('profile') }} </span>
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
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/settings')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/settings') ? 'page' : undefined"
                    >
                        <SettingsIcon aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span> {{ $t('settings') }} </span>
                    </Link>
                </li>

                <li
                    class="pt-2"
                    v-if="!isSingleLicenseMode && hasRole(['Super Admin', 'admin'])"
                >
                    <Link
                        :href="route('settings.payment-gateways.index')"
                        :class="[
                            'flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors duration-150',
                            isActive('/settings/payment-gateways')
                                ? 'bg-green-50 text-green-700 font-semibold'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                        ]"
                        :aria-current="isActive('/settings/payment-gateways') ? 'page' : undefined"
                    >
                        <Wallet aria-hidden="true" class="h-5 w-5" :stroke-width="2" />
                        <span> {{ $t('payment_gateways') }} </span>
                    </Link>
                </li>
                </template>
            </ul>
        </nav>
    </aside>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { computed, reactive, onMounted } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {
    Home,
    BarChart3,
    PawPrint,
    HeartPulse,
    Wheat,
    Droplet,
    Landmark,
    CircleDollarSign,
    Users,
    Package,
    Truck,
    BriefcaseBusiness,
    LineChart,
    ClipboardList,
    CreditCard,
    ShieldCheck,
    CalendarDays,
    Receipt,
    UserCircle,
    Settings as SettingsIcon,
    Wallet,
    ChevronDown,
} from "lucide-vue-next";

const props = defineProps({
    showing: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const user = computed(() => page.props.value.auth?.user ?? null);
const roles = computed(() => {
    const rawRoles = page.props.value.auth?.user?.roles ?? [];
    return rawRoles.map((r) => (typeof r === "string" ? r : r.name));
});
const appSettings = computed(() => usePage().props.value?.appSettings || {});
const subscription = computed(() => usePage().props.value?.subscription || {});
const enabledFeatures = computed(() => subscription.value?.features || []);
const hasFeature = (key) => enabledFeatures.value.includes(key);

// In single-license mode we bypass subscription feature gating entirely.
const hasFeatureOrSingle = (key) =>
    isSingleLicenseMode.value ? true : hasFeature(key);

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
    payroll: false,
    accounts: false,
    customers: false,
});

const openSections = reactive({
    operations: false,
    livestock: false,
    finance: false,
    inventory: false,
    hr: false,
    reports: false,
    administration: false,
    account: false,
});

const toggleSection = (key) => {
    openSections[key] = !openSections[key];
};

const sectionHas = computed(() => ({
    operations:
        hasFeatureOrSingle("dashboard") ||
        (!isSingleLicenseMode.value && hasFeatureOrSingle("farmproductivity")),
    livestock:
        hasFeatureOrSingle("animals") ||
        hasFeatureOrSingle("healths") ||
        hasFeatureOrSingle("feedings") ||
        hasFeatureOrSingle("productions"),
    finance:
        hasFeatureOrSingle("accounting") ||
        hasFeatureOrSingle("finance") ||
        hasFeatureOrSingle("customers"),
    inventory:
        hasFeatureOrSingle("inventory") || hasFeatureOrSingle("operation"),
    hr: hasFeatureOrSingle("hr"),
    reports:
        hasFeatureOrSingle("reports") || hasFeatureOrSingle("invreports"),
    administration: hasRole(["Super Admin", "admin"]),
    account: true,
}));

const sectionActivePrefixes = {
    operations: ["/dashboard", "/farm-productivity-dashboard"],
    livestock: [
        "/animals",
        "/breeds",
        "/herds",
        "/reproduction-records",
        "/artificial-inseminations",
        "/pregnancies",
        "/pregnancy-checkups",
        "/calving-records",
        "/calves",
        "/health-issues",
        "/health-events",
        "/disease-treatments",
        "/treatments",
        "/vaccinations",
        "/vaccine-types",
        "/event-types",
        "/diseases",
        "/feedings",
        "/feed-types",
        "/milk-records",
        "/milk-sales",
    ],
    finance: [
        "/chart-of-accounts",
        "/journal-entries",
        "/journal-voucher-report",
        "/balance-sheet",
        "/profit-loss",
        "/cash-flow",
        "/trial-balance",
        "/cash-accounts",
        "/fixed-assets",
        "/expenses",
        "/sales",
        "/purchases",
        "/customers",
        "/customer-payments",
    ],
    inventory: [
        "/inventory",
        "/medicines",
        "/categories",
        "/suppliers",
        "/supplier-payments",
        "/medicine-groups",
        "/stock-movements",
        "/logistics",
        "/farms",
        "/staff",
    ],
    hr: [
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
        "/payroll-runs",
        "/payroll-items",
    ],
    reports: ["/reports"],
    administration: ["/admin"],
    account: ["/plan", "/billing", "/profile", "/settings"],
};

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
        customers: ["/customers", "/customer-payments"],
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
            "/payroll-runs",
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
        settings: ["/settings"],
        plan: ["/plan"],
        billing: ["/billing"],
    };

    return (
        menuRoutes[menuKey]?.some((route) =>
            currentPath.value.startsWith(route),
        ) || false
    );
};

const linkAttrs = (path) => {
    const active = currentPath.value.startsWith(path);
    return {
        class: [
            "block px-3 py-2 rounded-lg text-sm transition-colors duration-150",
            active
                ? "bg-green-50 text-green-700 font-semibold"
                : "text-slate-600 hover:bg-slate-50 hover:text-slate-900",
        ],
        "aria-current": active ? "page" : undefined,
    };
};

const linkClass = (path) => linkAttrs(path).class;

const toggleMenu = (menuKey) => {
    openMenus[menuKey] = !openMenus[menuKey];
};

onMounted(() => {
    Object.keys(openMenus).forEach((menuKey) => {
        if (isMenuActive(menuKey)) {
            openMenus[menuKey] = true;
        }
    });
    Object.keys(openSections).forEach((key) => {
        if (
            sectionActivePrefixes[key]?.some((p) =>
                currentPath.value.startsWith(p),
            )
        ) {
            openSections[key] = true;
        }
    });
    if (!Object.values(openSections).some(Boolean) && sectionHas.value.operations) {
        openSections.operations = true;
    }
});
</script>

<style scoped>
/* Scrollbar hidden by default, revealed on hover of the sidebar */
.scrollbar-thin {
    scrollbar-width: none;
}
.scrollbar-thin::-webkit-scrollbar {
    width: 0;
    background: transparent;
}
.app-sidebar:hover .scrollbar-thin,
.scrollbar-thin:focus-within {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.app-sidebar:hover .scrollbar-thin::-webkit-scrollbar,
.scrollbar-thin:focus-within::-webkit-scrollbar {
    width: 6px;
}
.app-sidebar:hover .scrollbar-thin::-webkit-scrollbar-track,
.scrollbar-thin:focus-within::-webkit-scrollbar-track {
    background: transparent;
}
.app-sidebar:hover .scrollbar-thin::-webkit-scrollbar-thumb,
.scrollbar-thin:focus-within::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.app-sidebar:hover .scrollbar-thin::-webkit-scrollbar-thumb:hover,
.scrollbar-thin:focus-within::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
