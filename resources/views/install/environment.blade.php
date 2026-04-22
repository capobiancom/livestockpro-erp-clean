@extends('install.layout')
@section('title', 'Application Settings')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Application Settings</h2>
<p class="text-gray-500 mb-6 text-sm">Configure your application name, URL, and operational mode.</p>

<form method="POST" action="{{ route('install.environment.save') }}">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
            <input type="text" name="app_name" value="{{ old('app_name', 'Vacaliza ERP SaaS') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('app_name') border-red-400 @enderror">
            @error('app_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Application URL</label>
            <input type="url" name="app_url" value="{{ old('app_url', $appUrl) }}"
                   placeholder="https://yourdomain.com"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('app_url') border-red-400 @enderror">
            @error('app_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
            <select name="timezone" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @foreach(timezone_identifiers_list() as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', 'UTC') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Default Currency Code</label>
            <input type="text" name="currency" value="{{ old('currency', 'USD') }}" maxlength="10"
                   placeholder="USD, EUR, BDT …"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    {{-- SaaS Mode --}}
    <div class="mb-6 p-5 bg-blue-50 border border-blue-200 rounded-xl">
        <h3 class="font-semibold text-blue-800 mb-3 text-sm">Operational Mode</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="flex items-start gap-3 p-3 bg-white border-2 rounded-xl cursor-pointer
                {{ old('saas_mode', 'false') === 'false' ? 'border-blue-500' : 'border-gray-200' }}">
                <input type="radio" name="saas_mode" value="false"
                       {{ old('saas_mode', 'false') === 'false' ? 'checked' : '' }} class="mt-0.5">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Single License Mode</p>
                    <p class="text-xs text-gray-500 mt-0.5">All features unlocked. No subscription billing required. Best for single-farm installations.</p>
                </div>
            </label>
            <label class="flex items-start gap-3 p-3 bg-white border-2 rounded-xl cursor-pointer
                {{ old('saas_mode', 'false') === 'true' ? 'border-blue-500' : 'border-gray-200' }}">
                <input type="radio" name="saas_mode" value="true"
                       {{ old('saas_mode', 'false') === 'true' ? 'checked' : '' }} class="mt-0.5">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">SaaS / Multi-Tenant Mode</p>
                    <p class="text-xs text-gray-500 mt-0.5">Subscription billing enforced. Supports multiple farms with tiered plans. Best for SaaS deployments.</p>
                </div>
            </label>
        </div>
    </div>

    {{-- Mail Settings (collapsed by default) --}}
    <div class="mb-6">
        <button type="button" onclick="toggleMail()"
                class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
            <span id="mail-arrow" class="transition-transform">▶</span>
            Email / SMTP Settings (optional — can be configured later in Admin panel)
        </button>
        <div id="mail-settings" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mailer</label>
                    <select name="mail_mailer" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                        <option value="log">Log (no email sent)</option>
                        <option value="smtp">SMTP</option>
                        <option value="sendmail">Sendmail</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host') }}" placeholder="smtp.example.com"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">SMTP Port</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', 587) }}"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">From Address</label>
                    <input type="email" name="mail_from" value="{{ old('mail_from') }}" placeholder="hello@yourdomain.com"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username') }}"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Password</label>
                    <input type="password" name="mail_password"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('install.database') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
            Save & Continue →
        </button>
    </div>
</form>

<script>
function toggleMail() {
    const el = document.getElementById('mail-settings');
    const arrow = document.getElementById('mail-arrow');
    el.classList.toggle('hidden');
    arrow.style.transform = el.classList.contains('hidden') ? '' : 'rotate(90deg)';
}
</script>
@endsection
