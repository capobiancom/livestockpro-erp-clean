@extends('install.layout')
@section('title', 'Server Requirements')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Server Requirements</h2>
<p class="text-gray-500 mb-6 text-sm">Checking that your server meets all requirements to run LivestockPro ERP SaaS.</p>

<div class="space-y-2 mb-8">
    @foreach($checks as $check)
        <div class="flex items-center justify-between p-3 rounded-lg border {{ $check['status'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
            <div class="flex items-center gap-3">
                @if($check['status'])
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                @endif
                <span class="text-sm font-medium {{ $check['status'] ? 'text-green-800' : 'text-red-800' }}">
                    {{ $check['label'] }}
                </span>
            </div>
            <span class="text-xs px-2 py-1 rounded-full {{ $check['status'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $check['value'] }}
            </span>
        </div>
    @endforeach
</div>

@if(! $allPassed)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-800">
        <strong>⚠ Some requirements are not met.</strong>
        <p class="mt-1">Please fix the issues above and then refresh this page. Contact your hosting provider if you need help enabling PHP extensions or setting folder permissions.</p>
    </div>
@endif

<div class="flex justify-between">
    <a href="{{ route('install.welcome') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
        ← Back
    </a>
    @if($allPassed)
        <a href="{{ route('install.database') }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            Continue to Database →
        </a>
    @else
        <button onclick="location.reload()"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            ↺ Re-check
        </button>
    @endif
</div>
@endsection
