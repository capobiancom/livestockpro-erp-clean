@extends('install.layout')
@section('title', 'Database Configuration')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Database Configuration</h2>
<p class="text-gray-500 mb-6 text-sm">Enter your database connection details. The installer will test the connection before continuing.</p>

<form method="POST" action="{{ route('install.database.save') }}">
    @csrf

    {{-- Connection Type --}}
    <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-2">Database Type</label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer
                {{ old('db_connection', 'mysql') === 'mysql' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                <input type="radio" name="db_connection" value="mysql"
                       {{ old('db_connection', 'mysql') === 'mysql' ? 'checked' : '' }}
                       onchange="toggleDbFields(this.value)" class="sr-only">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">MySQL / MariaDB</p>
                    <p class="text-xs text-gray-500">Recommended for production</p>
                </div>
            </label>
            <label class="relative flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer
                {{ old('db_connection') === 'sqlite' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                <input type="radio" name="db_connection" value="sqlite"
                       {{ old('db_connection') === 'sqlite' ? 'checked' : '' }}
                       onchange="toggleDbFields(this.value)" class="sr-only">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">SQLite</p>
                    <p class="text-xs text-gray-500">Simple file-based database</p>
                </div>
            </label>
        </div>
    </div>

    {{-- MySQL Fields --}}
    <div id="mysql-fields">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('db_host') border-red-400 @enderror">
                @error('db_host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="number" name="db_port" value="{{ old('db_port', '3306') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
            <input type="text" name="db_database" value="{{ old('db_database', 'livestockpro') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('db_database') border-red-400 @enderror">
            @error('db_database') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="db_username" value="{{ old('db_username', 'root') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('db_username') border-red-400 @enderror">
                @error('db_username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="db_password" value="{{ old('db_password') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
    </div>

    @if($errors->has('db_connection'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ $errors->first('db_connection') }}
        </div>
    @endif

    <div class="flex justify-between mt-6">
        <a href="{{ route('install.requirements') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Back
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            Test & Continue →
        </button>
    </div>
</form>

<script>
function toggleDbFields(value) {
    document.getElementById('mysql-fields').style.display = value === 'mysql' ? 'block' : 'none';
}
// Set initial state
document.addEventListener('DOMContentLoaded', () => {
    const selected = document.querySelector('input[name="db_connection"]:checked')?.value || 'mysql';
    toggleDbFields(selected);
    document.querySelectorAll('input[name="db_connection"]').forEach(el => {
        el.addEventListener('change', e => {
            document.querySelectorAll('input[name="db_connection"]').forEach(r => {
                r.closest('label').classList.toggle('border-blue-500', r.checked);
                r.closest('label').classList.toggle('bg-blue-50', r.checked);
                r.closest('label').classList.toggle('border-gray-200', !r.checked);
            });
        });
    });
});
</script>
@endsection
