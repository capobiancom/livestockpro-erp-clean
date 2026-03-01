@extends('install.layout')
@section('title', 'Create Admin Account')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Create Super Admin Account</h2>
<p class="text-gray-500 mb-6 text-sm">
    Create the first super-administrator account. This account will have full access to manage all farms, users, and settings.
</p>

<form method="POST" action="{{ route('install.admin.create') }}">
    @csrf

    <div class="space-y-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="Your full name"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="admin@yourdomain.com"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-400 @enderror">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                   placeholder="Minimum 8 characters"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-400 @enderror">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   placeholder="Repeat your password"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-sm text-blue-800">
        <strong>Note:</strong> Keep your login credentials safe. You can add more administrators and farm owners from the Admin panel after installation.
    </div>

    <div class="flex justify-between">
        <a href="{{ route('install.migrate') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            Create Account & Finish →
        </button>
    </div>
</form>
@endsection
