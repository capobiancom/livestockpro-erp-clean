@extends('install.layout')
@section('title', 'Installation Complete')

@section('content')
<div class="text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>

    <h2 class="text-3xl font-bold text-gray-800 mb-3">🎉 Installation Complete!</h2>
    <p class="text-gray-500 mb-8 max-w-lg mx-auto">
        Vacaliza ERP SaaS has been successfully installed. Your application is ready to use.
    </p>

    {{-- Credentials Summary --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-8 text-left max-w-md mx-auto">
        <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">Your Login Details</h3>
        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-gray-500">Login URL:</span>
                <a href="{{ $appUrl }}/login" class="text-blue-600 hover:underline font-mono text-xs">
                    {{ $appUrl }}/login
                </a>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-500">Name:</span>
                <span class="font-medium text-gray-800">{{ $adminName }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-500">Email:</span>
                <span class="font-medium text-gray-800 font-mono text-xs">{{ $adminEmail }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-500">Password:</span>
                <span class="text-gray-400 italic text-xs">As set during installation</span>
            </div>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8 text-sm text-amber-800 text-left max-w-md mx-auto">
        <strong>⚠ Security Notice:</strong> Delete or secure the <code class="bg-amber-100 px-1 rounded">/install</code> URL path.
        The installer has been automatically locked and cannot be re-run, but it is good practice to
        restrict access to it on your server (e.g. in .htaccess or Nginx config).
    </div>

    <a href="{{ $appUrl }}/login"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors shadow-lg text-lg">
        Go to Application →
    </a>

    <div class="mt-6 text-sm text-gray-400">
        <p>Need help? Check the <strong>documentation/</strong> folder included in your download.</p>
    </div>
</div>
@endsection
