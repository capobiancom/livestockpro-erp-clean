@extends('install.layout')
@section('title', 'Welcome')

@section('content')
<div class="text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
        </svg>
    </div>
    <h2 class="text-3xl font-bold text-gray-800 mb-3">Welcome to LivestockPro ERP SaaS</h2>
    <p class="text-gray-500 mb-8 max-w-xl mx-auto">
        This wizard will guide you through setting up your LivestockPro ERP SaaS installation in just a few minutes.
        Please ensure you have your database credentials ready before proceeding.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 text-left">
        <div class="p-4 bg-blue-50 rounded-xl">
            <div class="w-8 h-8 bg-blue-200 rounded-lg flex items-center justify-center mb-2">
                <span class="text-blue-700 font-bold text-sm">1</span>
            </div>
            <h3 class="font-semibold text-gray-700 text-sm">Server Check</h3>
            <p class="text-xs text-gray-500 mt-1">Verify PHP version, extensions, and folder permissions.</p>
        </div>
        <div class="p-4 bg-green-50 rounded-xl">
            <div class="w-8 h-8 bg-green-200 rounded-lg flex items-center justify-center mb-2">
                <span class="text-green-700 font-bold text-sm">2</span>
            </div>
            <h3 class="font-semibold text-gray-700 text-sm">Database & App Setup</h3>
            <p class="text-xs text-gray-500 mt-1">Configure your MySQL/SQLite database and application settings.</p>
        </div>
        <div class="p-4 bg-purple-50 rounded-xl">
            <div class="w-8 h-8 bg-purple-200 rounded-lg flex items-center justify-center mb-2">
                <span class="text-purple-700 font-bold text-sm">3</span>
            </div>
            <h3 class="font-semibold text-gray-700 text-sm">Admin Account</h3>
            <p class="text-xs text-gray-500 mt-1">Create your super-administrator account to log in.</p>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8 text-left text-sm text-amber-800">
        <strong>Before you begin, ensure you have:</strong>
        <ul class="list-disc list-inside mt-2 space-y-1">
            <li>MySQL database created (or SQLite file path ready)</li>
            <li>Database username and password with full privileges</li>
            <li>Your server's domain name or IP address</li>
            <li>PHP 8.2 or higher installed</li>
        </ul>
    </div>

    <a href="{{ route('install.requirements') }}"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors shadow-lg">
        Begin Installation
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
        </svg>
    </a>
</div>
@endsection
