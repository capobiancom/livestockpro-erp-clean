@extends('install.layout')
@section('title', 'Database Setup')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Running Migrations & Seeding</h2>
<p class="text-gray-500 mb-6 text-sm">
    LivestockPro ERP SaaS will now create all required database tables and seed the initial configuration data.
    This may take a few moments.
</p>

<div id="status-box" class="bg-gray-900 text-green-400 rounded-xl p-5 font-mono text-sm min-h-40 mb-6 overflow-y-auto">
    <p id="status-log">Waiting to start…</p>
</div>

<div id="progress-bar" class="hidden">
    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
        <div id="progress-fill" class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
    </div>
</div>

<div id="error-box" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 mb-4 text-red-700 text-sm"></div>

<div id="action-buttons" class="flex justify-between">
    <a href="{{ route('install.environment') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
    <button id="run-btn" onclick="runMigrations()"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
        Run Migrations →
    </button>
</div>

<div id="next-button" class="hidden flex justify-end mt-4">
    <a href="{{ route('install.admin') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
        Continue to Create Admin →
    </a>
</div>

<script>
let migrationInProgress = false;

async function runMigrations() {
    // Prevent multiple concurrent runs
    if (migrationInProgress) {
        return;
    }
    migrationInProgress = true;

    const btn = document.getElementById('run-btn');
    const log = document.getElementById('status-log');
    const progressBar = document.getElementById('progress-bar');
    const progressFill = document.getElementById('progress-fill');
    const errorBox = document.getElementById('error-box');
    const nextBtn = document.getElementById('next-button');

    btn.disabled = true;
    btn.style.pointerEvents = 'none';
    btn.textContent = 'Running…';
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    progressBar.classList.remove('hidden');
    errorBox.classList.add('hidden');
    log.innerHTML = 'Waiting to start…';

    const messages = [
        '$ php artisan migrate --force',
        '→ Creating tables…',
        '$ php artisan db:seed --force',
        '→ Seeding roles & permissions…',
        '→ Seeding subscription catalog…',
        '$ php artisan key:generate',
        '$ php artisan storage:link',
        '$ php artisan optimize:clear',
    ];

    let step = 0;
    const interval = setInterval(() => {
        if (step < messages.length) {
            log.innerHTML += '\n' + messages[step];
            progressFill.style.width = (((step + 1) / messages.length) * 90) + '%';
            step++;
        }
    }, 400);

    try {
        const url = '{{ route('install.migrate.run') }}';
        console.log('Fetching:', url);
        
        const resp = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        });

        clearInterval(interval);
        console.log('Response status:', resp.status);

        // The backend currently returns a redirect (HTML) on success.
        // If we blindly call resp.json(), we'll get:
        //   Unexpected token '<', "<!DOCTYPE "... is not valid JSON
        //
        // So:
        // 1) If response is JSON, parse it and use {success:true/false}
        // 2) Otherwise, treat any 2xx as success and navigate to the next step.
        const contentType = resp.headers.get('content-type') || '';
        console.log('Response content-type:', contentType);

        let data = null;

        if (contentType.includes('application/json')) {
            data = await resp.json();
            console.log('Response data (json):', data);

            if (!data.success) {
                throw new Error(data.error || 'Unknown error');
            }

            // If backend provides a redirect URL, follow it immediately.
            if (data.redirect) {
                console.log('Following JSON redirect to:', data.redirect);
                // Ensure admin step doesn't bounce back if session write is delayed
                window.location.href = data.redirect + (data.redirect.includes('?') ? '&' : '?') + 'migrated=1';
                return;
            }
        } else {
            // Non-JSON (likely HTML redirect page). If not OK, show the body as error.
            if (!resp.ok) {
                const text = await resp.text();
                console.log('Response data (text):', text);
                throw new Error('Server returned ' + resp.status + '. ' + (text?.slice(0, 300) || ''));
            }
        }

        // Success UI
        progressFill.style.width = '100%';
        log.innerHTML += '\n\n✅ All migrations and seeding completed successfully!';
        progressFill.classList.remove('bg-green-500');
        progressFill.classList.add('bg-green-600');

        // Hide action buttons
        document.getElementById('action-buttons').style.display = 'none';

        // Show next button
        nextBtn.classList.remove('hidden');

        // If the backend responded with a redirect, follow it explicitly.
        // (fetch() follows redirects internally, but it does NOT change window.location)
        if (resp.redirected && resp.url) {
            console.log('Following server redirect to:', resp.url);
            // Ensure admin step doesn't bounce back if session write is delayed
            window.location.href = resp.url + (resp.url.includes('?') ? '&' : '?') + 'migrated=1';
            return;
        }

        console.log('Redirecting to admin in 1 second...');

        // Auto-redirect after 1 second
        setTimeout(() => {
            console.log('Redirecting now...');
            window.location.href = '{{ route('install.admin', ['migrated' => 1]) }}';
        }, 1000);
    } catch (err) {
        clearInterval(interval);
        console.error('Migration error:', err);
        progressFill.style.width = '0%';
        errorBox.textContent = '❌ Migration failed: ' + err.message;
        errorBox.classList.remove('hidden');
        btn.disabled = false;
        btn.style.pointerEvents = 'auto';
        btn.textContent = 'Retry';
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        migrationInProgress = false;
    }
}
</script>
@endsection
