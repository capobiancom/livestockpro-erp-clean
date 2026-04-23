<template>
    <Layout>
        <template #title>
            <!-- Hero Section with Welcome -->
            <div
                class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-lg shadow-lg p-3 mb-4 text-white"
            >
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="bg-white/20 backdrop-blur-sm rounded-lg p-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-white"
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
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">{{ $t('dashboard') }}</h1>
                            <div
                                class="flex items-center gap-2 text-blue-100 text-xs"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <span class="font-medium">{{
                                    getCurrentDate()
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Switch dashboard shortcuts -->
                    <div class="hidden sm:flex items-center gap-3">
                        <Link
                            v-if="isSuperAdmin"
                            :href="route('admin.users.index')"
                            class="px-3 py-2 text-xs font-semibold bg-white/15 hover:bg-white/25 rounded-lg transition"
                        >
                            Switch Farm Owner
                        </Link>

                        <Link
                            v-if="isFarmOwner"
                            :href="route('dashboard', { user_id: authUserId })"
                            class="px-3 py-2 text-xs font-semibold bg-white/15 hover:bg-white/25 rounded-lg transition"
                        >
                            My Dashboard
                        </Link>

                        <div
                            class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg"
                        >
                            <div
                                class="h-2.5 w-2.5 bg-green-400 rounded-full animate-pulse"
                            ></div>
                            <span
                                class="text-xs font-semibold text-white tracking-wide"
                                >{{ $t('live') }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Farm owner: switch to another user in same farm -->
            <div
                v-if="isFarmOwner && farmUsers.length"
                class="bg-white rounded-lg shadow p-4 mb-6 border border-gray-100"
            >
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-800">
                            Switch User Dashboard
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            View dashboard as another user in your farm.
                        </div>
                        <select
                            v-model="selectedUserId"
                            class="mt-3 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                        >
                            <option :value="null">{{ $t('select_user') }}</option>
                            <option
                                v-for="u in farmUsers"
                                :key="u.id"
                                :value="u.id"
                            >
                                {{ u.name }} — {{ u.email }}
                            </option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button
                            @click="openSelectedUserDashboard"
                            :disabled="!selectedUserId"
                            class="px-4 py-2 rounded-lg font-semibold shadow transition duration-200 text-sm bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Open
                        </button>

                        <Link
                            :href="route('dashboard', { user_id: authUserId })"
                            class="px-4 py-2 rounded-lg font-semibold shadow transition duration-200 text-sm bg-gray-100 hover:bg-gray-200 text-gray-800"
                        >
                            Reset
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <!-- Quick Stats Overview (8 cards in 4x2 grid) with hover animations -->
        <div class="mb-8">
            <h3
                class="text-2xl font-bold text-gray-800 mb-5 flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7 text-indigo-600"
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
                Quick Overview
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div
                    class="group bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-blue-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-blue-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('total_animals') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-blue-700 mt-2"
                            >
                                {{ stats.total_animals }}
                            </p>
                            <p class="text-xs text-blue-500 mt-1">
                                {{ $t('all_registered') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-green-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('active_animals') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-green-700 mt-2"
                            >
                                {{ stats.active_animals }}
                            </p>
                            <p class="text-xs text-green-500 mt-1">
                                {{ $t('currently_active') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-orange-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-orange-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('total_staff') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-orange-700 mt-2"
                            >
                                {{ stats.total_staff }}
                            </p>
                            <p class="text-xs text-orange-500 mt-1">
                                {{ $t('team_members') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-purple-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-purple-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('total_farms') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-purple-700 mt-2"
                            >
                                {{ stats.total_farms }}
                            </p>
                            <p class="text-xs text-purple-500 mt-1">
                                {{ $t('locations') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
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
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-red-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-red-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('low_stock_items') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-red-700 mt-2"
                            >
                                {{ stats.low_stock_items }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">
                                {{ $t('needs_restock') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-teal-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-teal-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('feedings_today') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-teal-700 mt-2"
                            >
                                {{ stats.feedings_today }}
                            </p>
                            <p class="text-xs text-teal-500 mt-1">
                                {{ $t('completed_today') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-yellow-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-yellow-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('vaccinations_due') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-yellow-700 mt-2"
                            >
                                {{ stats.vaccinations_due }}
                            </p>
                            <p class="text-xs text-yellow-500 mt-1">
                                {{ $t('within_7_days') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-pink-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-pink-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('active_health_issues') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-pink-700 mt-2"
                            >
                                {{ stats.active_health_issues }}
                            </p>
                            <p class="text-xs text-pink-500 mt-1">
                                {{ $t('needs_attention') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reproduction Statistics Section -->
        <div class="mb-8">
            <h3
                class="text-2xl font-bold text-gray-800 mb-5 flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7 text-purple-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>
                Reproduction Overview
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Pregnancies -->
                <div
                    class="group bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-purple-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-purple-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('total_pregnancies') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-purple-700 mt-2"
                            >
                                {{ reproductionStats.total_pregnancies }}
                            </p>
                            <p class="text-xs text-purple-500 mt-1">
                                {{ $t('all_records') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ongoing Pregnancies -->
                <div
                    class="group bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-blue-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-blue-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('ongoing_pregnancies') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-blue-700 mt-2"
                            >
                                {{ reproductionStats.ongoing_pregnancies }}
                            </p>
                            <p class="text-xs text-blue-500 mt-1">
                                {{ $t('currently_active') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Aborted Pregnancies -->
                <div
                    class="group bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-red-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-red-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('aborted_pregnancies') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-red-700 mt-2"
                            >
                                {{ reproductionStats.aborted_pregnancies }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">
                                {{ $t('unsuccessful') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Pregnancies -->
                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-green-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('completed_pregnancies') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-green-700 mt-2"
                            >
                                {{ reproductionStats.completed_pregnancies }}
                            </p>
                            <p class="text-xs text-green-500 mt-1">
                                {{ $t('calved_successfully') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Pregnancy Checkups -->
                <div
                    class="group bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-yellow-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-yellow-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('total_checkups') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-yellow-700 mt-2"
                            >
                                {{ reproductionStats.total_pregnancy_checkups }}
                            </p>
                            <p class="text-xs text-yellow-500 mt-1">
                                {{ $t('all_checkups') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Normal Checkups -->
                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-green-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('normal_checkups') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-green-700 mt-2"
                            >
                                {{ reproductionStats.normal_checkups }}
                            </p>
                            <p class="text-xs text-green-500 mt-1">
                                {{ $t('healthy_results') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Risk Checkups -->
                <div
                    class="group bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-orange-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-orange-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('risk_checkups') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-orange-700 mt-2"
                            >
                                {{ reproductionStats.risk_checkups }}
                            </p>
                            <p class="text-xs text-orange-500 mt-1">
                                {{ $t('potential_issues') }}
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Critical Checkups -->
                <div
                    class="group bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-red-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-red-600 font-semibold uppercase tracking-wider"
                            >
                                {{ $t('critical_checkups') }}
                            </p>
                            <p
                                class="text-4xl font-extrabold text-red-700 mt-2"
                            >
                                {{ reproductionStats.critical_checkups }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">
                                Urgent attention
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Calving Records -->
                <div
                    class="group bg-gradient-to-br from-cyan-50 to-teal-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-cyan-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-cyan-600 font-semibold uppercase tracking-wider"
                            >
                                Total Calvings
                            </p>
                            <p
                                class="text-4xl font-extrabold text-cyan-700 mt-2"
                            >
                                {{ reproductionStats.total_calving_records }}
                            </p>
                            <p class="text-xs text-cyan-500 mt-1">
                                All calving events
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Successful Calvings -->
                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-green-600 font-semibold uppercase tracking-wider"
                            >
                                Successful Calvings
                            </p>
                            <p
                                class="text-4xl font-extrabold text-green-700 mt-2"
                            >
                                {{ reproductionStats.successful_calvings }}
                            </p>
                            <p class="text-xs text-green-500 mt-1">
                                Live births
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stillbirth Calvings -->
                <div
                    class="group bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-red-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-red-600 font-semibold uppercase tracking-wider"
                            >
                                Stillbirth Calvings
                            </p>
                            <p
                                class="text-4xl font-extrabold text-red-700 mt-2"
                            >
                                {{ reproductionStats.stillbirth_calvings }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">
                                Unsuccessful births
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Complication Calvings -->
                <div
                    class="group bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-orange-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-orange-600 font-semibold uppercase tracking-wider"
                            >
                                Complication Calvings
                            </p>
                            <p
                                class="text-4xl font-extrabold text-orange-700 mt-2"
                            >
                                {{ reproductionStats.complication_calvings }}
                            </p>
                            <p class="text-xs text-orange-500 mt-1">
                                Births with issues
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Calves Born -->
                <div
                    class="group bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-purple-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-purple-600 font-semibold uppercase tracking-wider"
                            >
                                Total Calves Born
                            </p>
                            <p
                                class="text-4xl font-extrabold text-purple-700 mt-2"
                            >
                                {{ reproductionStats.total_calves_born }}
                            </p>
                            <p class="text-xs text-purple-500 mt-1">
                                All newborn calves
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Healthy Calves -->
                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-green-600 font-semibold uppercase tracking-wider"
                            >
                                Healthy Calves
                            </p>
                            <p
                                class="text-4xl font-extrabold text-green-700 mt-2"
                            >
                                {{ reproductionStats.healthy_calves }}
                            </p>
                            <p class="text-xs text-green-500 mt-1">
                                In good health
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Weak Calves -->
                <div
                    class="group bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-yellow-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-yellow-600 font-semibold uppercase tracking-wider"
                            >
                                Weak Calves
                            </p>
                            <p
                                class="text-4xl font-extrabold text-yellow-700 mt-2"
                            >
                                {{ reproductionStats.weak_calves }}
                            </p>
                            <p class="text-xs text-yellow-500 mt-1">
                                Needs care
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Critical Calves -->
                <div
                    class="group bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md hover:shadow-2xl p-6 border border-red-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm text-red-600 font-semibold uppercase tracking-wider"
                            >
                                Critical Calves
                            </p>
                            <p
                                class="text-4xl font-extrabold text-red-700 mt-2"
                            >
                                {{ reproductionStats.critical_calves }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">
                                Urgent care needed
                            </p>
                        </div>
                        <div
                            class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview Section with enhanced cards -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3
                    class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-green-600"
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
                    Financial Overview
                </h3>
                <span
                    class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full font-medium"
                    >All Time</span
                >
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-6 border-l-4 border-emerald-500 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-emerald-100 to-green-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-emerald-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-emerald-600 text-xs font-semibold bg-emerald-50 px-3 py-1 rounded-full"
                            >REVENUE</span
                        >
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        Total Revenue
                    </p>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ money(financial.total_revenue) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">From all sales</p>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-6 border-l-4 border-red-500 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-red-100 to-rose-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-red-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-red-600 text-xs font-semibold bg-red-50 px-3 py-1 rounded-full"
                            >EXPENSES</span
                        >
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        Total Expenses
                    </p>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ money(financial.total_expenses) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">Operational costs</p>
                </div>

                <div
                    class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg hover:shadow-2xl p-6 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-white/20 backdrop-blur-sm rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-white text-xs font-semibold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full"
                            >NET PROFIT</span
                        >
                    </div>
                    <p class="text-sm text-blue-100 font-medium mb-1">
                        Net Profit
                    </p>
                    <p class="text-3xl font-extrabold text-white">
                        {{ money(financial.profit) }}
                    </p>
                    <p class="text-xs text-blue-100 mt-2">Total earnings</p>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-6 border-l-4 border-teal-500 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-teal-100 to-cyan-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-teal-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-teal-600 text-xs font-semibold bg-teal-50 px-3 py-1 rounded-full"
                            >THIS MONTH</span
                        >
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        This Month Revenue
                    </p>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ money(financial.this_month_revenue) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">Current month</p>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-6 border-l-4 border-orange-500 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-orange-100 to-amber-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-orange-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-orange-600 text-xs font-semibold bg-orange-50 px-3 py-1 rounded-full"
                            >THIS MONTH</span
                        >
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        This Month Expenses
                    </p>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ money(financial.this_month_expenses) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">Current month</p>
                </div>

                <div
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-6 border-l-4 border-purple-500 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-purple-100 to-violet-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-purple-600 text-xs font-semibold bg-purple-50 px-3 py-1 rounded-full"
                            >PURCHASES</span
                        >
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        Total Purchases
                    </p>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ money(financial.total_purchases) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">All acquisitions</p>
                </div>
            </div>
        </div>

        <!-- Production & Health Side by Side -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Production Overview Section -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <h3
                        class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-cyan-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            />
                        </svg>
                        Production
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div
                        class="group bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-cyan-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-cyan-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-cyan-600 font-semibold mb-1">
                            Total Milk
                        </p>
                        <p class="text-3xl font-extrabold text-cyan-700">
                            {{ formatNumber(production.total_milk_production)
                            }}<span class="text-lg">L</span>
                        </p>
                    </div>

                    <div
                        class="group bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-blue-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-blue-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-blue-600 font-semibold mb-1">
                            Today's Milk
                        </p>
                        <p class="text-3xl font-extrabold text-blue-700">
                            {{ formatNumber(production.today_milk_production)
                            }}<span class="text-lg">L</span>
                        </p>
                    </div>

                    <div
                        class="group bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-indigo-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-indigo-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-indigo-600 font-semibold mb-1">
                            This Month
                        </p>
                        <p class="text-3xl font-extrabold text-indigo-700">
                            {{ formatNumber(production.this_month_milk)
                            }}<span class="text-lg">L</span>
                        </p>
                    </div>

                    <div
                        class="group bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-purple-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-purple-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
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
                        </div>
                        <p class="text-sm text-purple-600 font-semibold mb-1">
                            Avg Daily
                        </p>
                        <p class="text-3xl font-extrabold text-purple-700">
                            {{ formatNumber(production.avg_daily_milk)
                            }}<span class="text-lg">L</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Health Overview Section -->
            <div>
                <div class="flex items-center justify-between mb-5">
                    <h3
                        class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-red-600"
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
                        Health Status
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div
                        class="group bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-red-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-red-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-red-600 font-semibold mb-1">
                            Total Events
                        </p>
                        <p class="text-3xl font-extrabold text-red-700">
                            {{ health.total_health_events }}
                        </p>
                    </div>

                    <div
                        class="group bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-orange-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-orange-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-orange-600 font-semibold mb-1">
                            Active Events
                        </p>
                        <p class="text-3xl font-extrabold text-orange-700">
                            {{ health.active_health_events }}
                        </p>
                    </div>

                    <div
                        class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-green-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-green-600 font-semibold mb-1">
                            Resolved
                        </p>
                        <p class="text-3xl font-extrabold text-green-700">
                            {{ health.resolved_health_events }}
                        </p>
                    </div>

                    <div
                        class="group bg-gradient-to-br from-cyan-50 to-teal-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-cyan-100 transform hover:-translate-y-1 transition-all duration-300"
                    >
                        <div class="bg-cyan-500 rounded-xl p-3 w-fit mb-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-7 w-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-cyan-600 font-semibold mb-1">
                            Recovered
                        </p>
                        <p class="text-3xl font-extrabold text-cyan-700">
                            {{ health.recovered_animals }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Milk Sales Overview Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3
                    class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-blue-600"
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
                    Milk Sales Performance
                </h3>
                <span
                    class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full font-medium"
                    >Sales Metrics</span
                >
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Sales Count -->
                <div
                    class="group bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-blue-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-blue-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-blue-600 font-semibold mb-1">
                        Total Sales
                    </p>
                    <p class="text-3xl font-extrabold text-blue-700">
                        {{ milkSales.total_sales }}
                    </p>
                    <p class="text-xs text-blue-500 mt-2">All transactions</p>
                </div>

                <!-- Total Quantity Sold -->
                <div
                    class="group bg-gradient-to-br from-cyan-50 to-teal-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-cyan-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-cyan-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-cyan-600 font-semibold mb-1">
                        Total Quantity Sold
                    </p>
                    <p class="text-3xl font-extrabold text-cyan-700">
                        {{ formatNumber(milkSales.total_quantity_sold)
                        }}<span class="text-lg">L</span>
                    </p>
                    <p class="text-xs text-cyan-500 mt-2">All time</p>
                </div>

                <!-- Total Revenue from Milk Sales -->
                <div
                    class="group bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-emerald-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-emerald-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
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
                    </div>
                    <p class="text-sm text-emerald-600 font-semibold mb-1">
                        Total Revenue
                    </p>
                    <p class="text-3xl font-extrabold text-emerald-700">
                        {{ money(milkSales.total_revenue) }}
                    </p>
                    <p class="text-xs text-emerald-500 mt-2">From milk sales</p>
                </div>

                <!-- Today's Revenue -->
                <div
                    class="group bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-yellow-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-yellow-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-yellow-600 font-semibold mb-1">
                        Today's Revenue
                    </p>
                    <p class="text-3xl font-extrabold text-yellow-700">
                        {{ money(milkSales.today_revenue) }}
                    </p>
                    <p class="text-xs text-yellow-500 mt-2">
                        {{ milkSales.today_sales }} sales today
                    </p>
                </div>

                <!-- This Month Sales -->
                <div
                    class="group bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-purple-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-purple-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-purple-600 font-semibold mb-1">
                        This Month Sales
                    </p>
                    <p class="text-3xl font-extrabold text-purple-700">
                        {{ milkSales.this_month_sales }}
                    </p>
                    <p class="text-xs text-purple-500 mt-2">Transactions</p>
                </div>

                <!-- This Month Quantity -->
                <div
                    class="group bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-pink-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-pink-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
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
                    </div>
                    <p class="text-sm text-pink-600 font-semibold mb-1">
                        This Month Quantity
                    </p>
                    <p class="text-3xl font-extrabold text-pink-700">
                        {{ formatNumber(milkSales.this_month_quantity)
                        }}<span class="text-lg">L</span>
                    </p>
                    <p class="text-xs text-pink-500 mt-2">Sold this month</p>
                </div>

                <!-- This Month Revenue -->
                <div
                    class="group bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-indigo-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-indigo-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-indigo-600 font-semibold mb-1">
                        This Month Revenue
                    </p>
                    <p class="text-3xl font-extrabold text-indigo-700">
                        {{ money(milkSales.this_month_revenue) }}
                    </p>
                    <p class="text-xs text-indigo-500 mt-2">
                        Current month earnings
                    </p>
                </div>

                <!-- Average Sale Value -->
                <div
                    class="group bg-gradient-to-br from-orange-50 to-red-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-orange-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-orange-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-orange-600 font-semibold mb-1">
                        Avg Sale Value
                    </p>
                    <p class="text-3xl font-extrabold text-orange-700">
                        {{
                            money(
                                milkSales.total_sales > 0
                                    ? milkSales.total_revenue /
                                          milkSales.total_sales
                                    : 0,
                            )
                        }}
                    </p>
                    <p class="text-xs text-orange-500 mt-2">Per transaction</p>
                </div>
            </div>
        </div>

        <!-- Operations Overview Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3
                    class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-indigo-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"
                        />
                    </svg>
                    Logistics & Operations
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div
                    class="group bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-indigo-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-indigo-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-indigo-600 font-semibold mb-1">
                        Total Logistics
                    </p>
                    <p class="text-3xl font-extrabold text-indigo-700">
                        {{ operations.total_logistics }}
                    </p>
                </div>

                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-green-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-green-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-green-600 font-semibold mb-1">
                        Completed Trips
                    </p>
                    <p class="text-3xl font-extrabold text-green-700">
                        {{ operations.completed_trips }}
                    </p>
                </div>

                <div
                    class="group bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-yellow-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-yellow-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-yellow-600 font-semibold mb-1">
                        Pending Trips
                    </p>
                    <p class="text-3xl font-extrabold text-yellow-700">
                        {{ operations.pending_trips }}
                    </p>
                </div>

                <div
                    class="group bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-md hover:shadow-xl p-6 border border-purple-100 transform hover:-translate-y-1 transition-all duration-300"
                >
                    <div class="bg-purple-500 rounded-xl p-3 w-fit mb-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-7 w-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                    </div>
                    <p class="text-sm text-purple-600 font-semibold mb-1">
                        Animals Transported
                    </p>
                    <p class="text-3xl font-extrabold text-purple-700">
                        {{ operations.animals_transported }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3
                    class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-blue-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"
                        />
                    </svg>
                    Analytics & Insights
                </h3>
                <span
                    class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full font-medium"
                    >Visual Overview</span
                >
            </div>

            <!-- Revenue Trend Chart (7 Days) -->
            <div
                class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100"
            >
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">
                            Revenue Trend
                        </h4>
                        <p class="text-sm text-gray-500">
                            Last 7 days performance
                        </p>
                    </div>
                    <span
                        class="bg-gradient-to-r from-emerald-500 to-green-500 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                    >
                        7 Days
                    </span>
                </div>
                <div class="h-80">
                    <canvas ref="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Monthly Comparison Chart (Revenue vs Expenses - 6 Months) -->
            <div
                class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100"
            >
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">
                            Revenue vs Expenses
                        </h4>
                        <p class="text-sm text-gray-500">
                            Monthly comparison over 6 months
                        </p>
                    </div>
                    <span
                        class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                    >
                        6 Months
                    </span>
                </div>
                <div class="h-80">
                    <canvas ref="monthlyComparisonChart"></canvas>
                </div>
            </div>

            <!-- Milk Production Trend Chart (7 Days) -->
            <div
                class="bg-white rounded-xl shadow-lg p-6 border border-gray-100"
            >
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">
                            Milk Production Trend
                        </h4>
                        <p class="text-sm text-gray-500">
                            Daily milk production in liters
                        </p>
                    </div>
                    <span
                        class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                    >
                        7 Days
                    </span>
                </div>
                <div class="h-80">
                    <canvas ref="milkProductionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Feedings -->
            <div
                class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
            >
                <div
                    class="bg-gradient-to-r from-teal-500 to-cyan-500 px-6 py-4"
                >
                    <h3
                        class="text-lg font-bold text-white flex items-center gap-2"
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
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Recent Feedings
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-if="recentFeedings.length === 0"
                            class="text-center py-12"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 text-gray-300 mx-auto mb-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                />
                            </svg>
                            <p class="text-gray-500 text-sm">
                                No recent feedings
                            </p>
                        </div>
                        <div
                            v-for="feeding in recentFeedings"
                            :key="feeding.id"
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-teal-50 border border-gray-200 rounded-lg hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{
                                        feeding.animal?.tag ||
                                        feeding.animal?.name
                                    }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ feeding.feed_type?.name }} •
                                    {{ feeding.quantity }} kg
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-teal-600 font-medium">
                                    {{ formatDate(feeding.fed_date) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Health Events -->
            <div
                class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
            >
                <div
                    class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4"
                >
                    <h3
                        class="text-lg font-bold text-white flex items-center gap-2"
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
                        Health Events
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-if="recentHealthEvents.length === 0"
                            class="text-center py-12"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 text-gray-300 mx-auto mb-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <p class="text-gray-500 text-sm">
                                No health events
                            </p>
                        </div>
                        <div
                            v-for="event in recentHealthEvents"
                            :key="event.id"
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-red-50 border border-gray-200 rounded-lg hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{
                                        event.animal?.tag || event.animal?.name
                                    }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ event.event_type }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-red-600 font-medium">
                                    {{ formatDate(event.occurred_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Vaccinations -->
            <div
                class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
            >
                <div
                    class="bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-4"
                >
                    <h3
                        class="text-lg font-bold text-white flex items-center gap-2"
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
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                            />
                        </svg>
                        Upcoming Vaccines
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            v-if="upcomingVaccinations.length === 0"
                            class="text-center py-12"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 text-gray-300 mx-auto mb-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <p class="text-gray-500 text-sm">
                                No upcoming vaccinations
                            </p>
                        </div>
                        <div
                            v-for="vaccination in upcomingVaccinations"
                            :key="vaccination.id"
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-purple-50 border border-gray-200 rounded-lg hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{
                                        vaccination.animal?.tag ||
                                        vaccination.animal?.name
                                    }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ vaccination.vaccine_type?.name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-purple-600 font-medium">
                                    {{ formatDate(vaccination.next_due_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Movements by Unit Section -->
        <div class="mb-8 mt-5">
            <div class="flex items-center justify-between mb-5">
                <h3
                    class="text-2xl font-bold text-gray-800 flex items-center gap-2"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-purple-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"
                        />
                    </svg>
                    Stock Movements by Unit
                </h3>
                <span
                    class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full font-medium"
                    >Unit-wise Overview</span
                >
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-if="stockMovementsByUnit.length === 0"
                    class="text-center py-12 col-span-full"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-12 w-12 text-gray-300 mx-auto mb-3"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4m0-10h4m-4 0h-4m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    <p class="text-gray-500 text-sm">
                        No stock movements to display.
                    </p>
                </div>
                <Link
                    v-for="movement in stockMovementsByUnit"
                    :key="movement.unit"
                    :href="route('stock-movements.index')"
                    :data="{ unit: movement.unit }"
                    class="group bg-white rounded-xl shadow-lg hover:shadow-2xl p-6 border-l-4 border-purple-500 transform hover:-translate-y-1 transition-all duration-300 text-left focus:outline-none focus:ring-2 focus:ring-purple-500/40"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="bg-gradient-to-br from-purple-100 to-violet-100 rounded-xl p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-purple-600 text-xs font-semibold bg-purple-50 px-3 py-1 rounded-full"
                        >
                            {{ movement.unit.toUpperCase() }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-1">
                        Total In / Out ({{ movement.unit }})
                    </p>
                    <p class="text-3xl font-extrabold text-gray-900">
                        {{ formatNumber(movement.total_in) }} /
                        {{ formatNumber(movement.total_out) }}
                    </p>
                    <p
                        class="text-xs text-gray-500 mt-2 flex items-center gap-1"
                    >
                        <span>Click to view details</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-gray-400 group-hover:text-purple-600 transition"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </p>
                </Link>
            </div>
        </div>

        <!-- Demo Seeding Popup -->
        <DemoSeedingPopup
            v-if="props.show_demo_seeding_popup"
            :farm-id="farm_id"
            @close="closeDemoSeedingPopup"
            @seed-data="closeDemoSeedingPopup"
        />
    </Layout>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/AppLayout.vue";
import { computed, ref, onMounted } from "vue";
import { useMoneyFormatter } from "@/Utils/money";
import DemoSeedingPopup from "@/Components/DemoSeedingPopup.vue"; // Import the new component
import {
    Chart,
    LineController,
    BarController,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from "chart.js";

// Register Chart.js components
Chart.register(
    LineController,
    BarController,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    Filler,
);

const props = defineProps({
    stats: Object,
    financial: Object,
    production: Object,
    health: Object,
    operations: Object,
    milkSales: Object,
    animalsByStatus: Object,
    recentFeedings: Array,
    recentHealthEvents: Array,
    upcomingVaccinations: Array,
    lowStockItems: Array,
    feedingTrend: Array,
    milkProductionTrend: Array,
    animalsByFarm: Array,
    revenueTrend: Array,
    monthlyComparison: Array,
    show_demo_seeding_popup: {
        type: Boolean,
        default: false,
    },
    farm_id: {
        type: Number,
        required: false,
    },
    reproductionStats: Object, // Add reproductionStats prop
    stockMovementsByUnit: {
        type: Array,
        default: () => [],
    }, // Add stockMovementsByUnit prop
});

const page = usePage();

const authUserId = computed(() => page.props.value?.auth?.user?.id ?? null);
const authRoles = computed(() => page.props.value?.auth?.user?.roles ?? []);
const isSuperAdmin = computed(() => authRoles.value.includes("Super Admin"));
const isFarmOwner = computed(() => authRoles.value.includes("farm owner"));

const farmUsers = computed(() => props.farmUsers ?? []);
const selectedUserId = ref(null);

const openSelectedUserDashboard = () => {
    if (!selectedUserId.value) return;
    window.location.href = `/dashboard?user_id=${selectedUserId.value}`;
};

const { money, currencySymbol } = useMoneyFormatter();

// Debugging: Log props to console
onMounted(() => {
    console.log("Dashboard Props:", props);
    console.log("show_demo_seeding_popup:", props.show_demo_seeding_popup);
    console.log("farm_id:", props.farm_id);
    console.log("currencySymbol:", currencySymbol);
});

// The popup's visibility is now directly controlled by the prop,
// so no local ref or close function is needed for its primary state.
// The @close event can still be used for other side effects if needed.
const closeDemoSeedingPopup = () => {
    // This function might be used if there are other actions to take when the popup is "closed"
    // e.g., if a user clicks "Skip for now" and we want to perform a client-side action.
    // However, for the "Seed Demo Data" button, a full page reload handles the state.
};

// Chart refs
const revenueTrendChart = ref(null);
const monthlyComparisonChart = ref(null);
const milkProductionChart = ref(null);

const formatNumber = (value) => {
    if (value === null || value === undefined) return "0.00";
    return parseFloat(value).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
    });
};

const getCurrentDate = () => {
    return new Date().toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

// Initialize charts
onMounted(() => {
    // Revenue Trend Chart (Line Chart with gradient)
    if (revenueTrendChart.value && props.revenueTrend) {
        const ctx = revenueTrendChart.value.getContext("2d");
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, "rgba(16, 185, 129, 0.3)");
        gradient.addColorStop(1, "rgba(16, 185, 129, 0.0)");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: props.revenueTrend.map((item) => item.date),
                datasets: [
                    {
                        label: "Daily Revenue",
                        data: props.revenueTrend.map((item) => item.amount),
                        borderColor: "rgb(16, 185, 129)",
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: "rgb(16, 185, 129)",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            font: {
                                size: 13,
                                weight: "600",
                            },
                            usePointStyle: true,
                            padding: 20,
                        },
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                        },
                        bodyFont: {
                            size: 13,
                        },
                        callbacks: {
                            label: function (context) {
                                return (
                                    "Revenue: " +
                                    currencySymbol +
                                    context.parsed.y.toLocaleString("en-US", {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2,
                                    })
                                );
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                            callback: function (value) {
                                return currencySymbol + value.toLocaleString();
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
                interaction: {
                    mode: "index",
                    intersect: false,
                },
            },
        });
    }

    // Monthly Comparison Chart (Bar Chart)
    if (monthlyComparisonChart.value && props.monthlyComparison) {
        const ctx = monthlyComparisonChart.value.getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: props.monthlyComparison.map((item) => item.month),
                datasets: [
                    {
                        label: "Revenue",
                        data: props.monthlyComparison.map(
                            (item) => item.revenue,
                        ),
                        backgroundColor: "rgba(59, 130, 246, 0.8)",
                        borderColor: "rgb(59, 130, 246)",
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: "Expenses",
                        data: props.monthlyComparison.map(
                            (item) => item.expenses,
                        ),
                        backgroundColor: "rgba(239, 68, 68, 0.8)",
                        borderColor: "rgb(239, 68, 68)",
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            font: {
                                size: 13,
                                weight: "600",
                            },
                            usePointStyle: true,
                            padding: 20,
                        },
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                        },
                        bodyFont: {
                            size: 13,
                        },
                        callbacks: {
                            label: function (context) {
                                return (
                                    context.dataset.label +
                                    ": " +
                                    currencySymbol +
                                    context.parsed.y.toLocaleString("en-US", {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2,
                                    })
                                );
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                            callback: function (value) {
                                return currencySymbol + value.toLocaleString();
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
                interaction: {
                    mode: "index",
                    intersect: false,
                },
            },
        });
    }

    // Milk Production Trend Chart (Area Chart with gradient)
    if (milkProductionChart.value && props.milkProductionTrend) {
        const ctx = milkProductionChart.value.getContext("2d");
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, "rgba(6, 182, 212, 0.3)");
        gradient.addColorStop(1, "rgba(6, 182, 212, 0.0)");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: props.milkProductionTrend.map((item) => item.date),
                datasets: [
                    {
                        label: "Milk Production (Liters)",
                        data: props.milkProductionTrend.map(
                            (item) => item.quantity,
                        ),
                        borderColor: "rgb(6, 182, 212)",
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: "rgb(6, 182, 212)",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            font: {
                                size: 13,
                                weight: "600",
                            },
                            usePointStyle: true,
                            padding: 20,
                        },
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: "bold",
                        },
                        bodyFont: {
                            size: 13,
                        },
                        callbacks: {
                            label: function (context) {
                                return (
                                    "Quantity: " +
                                    context.parsed.y.toFixed(2) +
                                    " L"
                                );
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                            callback: function (value) {
                                return value + " L";
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
                interaction: {
                    mode: "index",
                    intersect: false,
                },
            },
        });
    }
});
</script>
