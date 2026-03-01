<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Installation') — LivestockPro ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .step-done  { background:#16a34a; color:#fff; }
        .step-active{ background:#2563eb; color:#fff; }
        .step-todo  { background:#e5e7eb; color:#6b7280; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex flex-col items-center justify-start py-10 px-4">

    {{-- Logo / Header --}}
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-2xl mb-3 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h.5A2.5 2.5 0 0020.5 5.5V3.935M15 3v.01"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">LivestockPro ERP</h1>
        <p class="text-sm text-gray-500 mt-1">Installation Wizard</p>
    </div>

    {{-- Step Progress Bar --}}
    @php
        $steps = [
            ['label' => 'Welcome',      'route' => 'install.welcome'],
            ['label' => 'Requirements', 'route' => 'install.requirements'],
            ['label' => 'Database',     'route' => 'install.database'],
            ['label' => 'Environment',  'route' => 'install.environment'],
            ['label' => 'Migrate',      'route' => 'install.migrate'],
            ['label' => 'Admin',        'route' => 'install.admin'],
            ['label' => 'Complete',     'route' => 'install.complete'],
        ];
        $currentRoute = request()->route()->getName();
        $currentIndex = collect($steps)->search(fn($s) => $s['route'] === $currentRoute);
    @endphp
    <div class="w-full max-w-3xl mb-8">
        <div class="flex items-center justify-between">
            @foreach($steps as $i => $step)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                        {{ $i < $currentIndex ? 'step-done' : ($i === $currentIndex ? 'step-active' : 'step-todo') }}">
                        @if($i < $currentIndex)
                            ✓
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <span class="text-xs mt-1 text-center {{ $i === $currentIndex ? 'text-blue-700 font-semibold' : 'text-gray-400' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
                @if($i < count($steps) - 1)
                    <div class="flex-1 h-0.5 mb-5 {{ $i < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Main Card --}}
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl p-8">
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <p class="mt-6 text-xs text-gray-400">LivestockPro ERP &copy; {{ date('Y') }} — All rights reserved.</p>
</body>
</html>
