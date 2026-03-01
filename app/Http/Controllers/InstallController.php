<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

class InstallController extends Controller
{
    // ── Step 1: Welcome ───────────────────────────────────────────────────

    public function welcome()
    {
        return view('install.welcome');
    }

    // ── Step 2: Server Requirements ───────────────────────────────────────

    public function requirements()
    {
        $checks = $this->runRequirementChecks();
        $allPassed = collect($checks)->every(fn ($c) => $c['status']);

        return view('install.requirements', compact('checks', 'allPassed'));
    }

    // ── Step 3: Database Configuration ───────────────────────────────────

    public function database()
    {
        return view('install.database');
    }

    public function saveDatabase(Request $request)
    {
        $request->validate([
            'db_connection' => 'required|in:mysql,sqlite',
            'db_host'       => 'required_if:db_connection,mysql',
            'db_port'       => 'required_if:db_connection,mysql|numeric|nullable',
            'db_database'   => 'required',
            'db_username'   => 'required_if:db_connection,mysql',
        ]);

        // Test the connection
        try {
            if ($request->db_connection === 'sqlite') {
                $path = database_path('database.sqlite');
                if (! file_exists($path)) {
                    touch($path);
                }
                $pdo = new \PDO('sqlite:' . $path);
            } else {
                $dsn = "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}";
                $pdo = new \PDO($dsn, $request->db_username, $request->db_password ?? '');
            }
        } catch (\PDOException $e) {
            return back()->withInput()->withErrors(['db_connection' => 'Connection failed: ' . $e->getMessage()]);
        }

        // Store in session for next steps
        session([
            'install.db_connection' => $request->db_connection,
            'install.db_host'       => $request->db_host ?? '127.0.0.1',
            'install.db_port'       => $request->db_port ?? '3306',
            'install.db_database'   => $request->db_database,
            'install.db_username'   => $request->db_username ?? '',
            'install.db_password'   => $request->db_password ?? '',
        ]);

        return redirect()->route('install.environment');
    }

    // ── Step 4: Environment / App Settings ───────────────────────────────

    public function environment()
    {
        $appUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return view('install.environment', compact('appUrl'));
    }

    public function saveEnvironment(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
            'app_url'  => 'required|url',
            'saas_mode' => 'required|in:true,false',
            'timezone'  => 'required|string',
            'currency'  => 'required|string|max:10',
        ]);

        session([
            'install.app_name'  => $request->app_name,
            'install.app_url'   => rtrim($request->app_url, '/'),
            'install.saas_mode' => $request->saas_mode,
            'install.timezone'  => $request->timezone,
            'install.currency'  => $request->currency,
            'install.mail_mailer'   => $request->mail_mailer ?? 'log',
            'install.mail_host'     => $request->mail_host ?? '',
            'install.mail_port'     => $request->mail_port ?? 587,
            'install.mail_username' => $request->mail_username ?? '',
            'install.mail_password' => $request->mail_password ?? '',
            'install.mail_from'     => $request->mail_from ?? 'hello@example.com',
        ]);

        // Write .env file
        $this->writeEnvFile();

        return redirect()->route('install.migrate');
    }

    // ── Step 5: Run Migrations ────────────────────────────────────────────

    public function migrate()
    {
        return view('install.migrate');
    }

    public function runMigrations(Request $request)
    {
        try {
            // Re-boot the database connection with the new .env values
            $this->reloadDatabaseConfig();

            // If using SQLite, delete the database file to ensure a fresh start
            $connection = config('database.default', 'mysql');
            if ($connection === 'sqlite') {
                // Disconnect first to release any locks
                DB::disconnect($connection);
                
                $dbPath = config('database.connections.sqlite.database');
                if (file_exists($dbPath)) {
                    @unlink($dbPath);
                }
                
                // Reconnect so migrations can create a fresh database
                DB::reconnect($connection);
            }

            Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
            Artisan::call('db:seed', ['--force' => true, '--no-interaction' => true]);
            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('storage:link', ['--force' => true]);
            Artisan::call('optimize:clear');

            session(['install.migrations_done' => true]);
            
            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ── Step 6: Create Admin Account ──────────────────────────────────────

    public function admin()
    {
        if (! session('install.migrations_done')) {
            return redirect()->route('install.migrate');
        }

        return view('install.admin');
    }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        try {
            $this->reloadDatabaseConfig();

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign Super Admin role (created by RoleSeeder)
            $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
            $user->assignRole($role);

            session([
                'install.admin_email' => $request->email,
                'install.admin_name'  => $request->name,
                'install.complete'    => true,
            ]);
        } catch (Throwable $e) {
            return back()->withInput()->withErrors(['email' => 'Failed to create admin: ' . $e->getMessage()]);
        }

        return redirect()->route('install.complete');
    }

    // ── Step 7: Installation Complete ─────────────────────────────────────

    public function complete()
    {
        if (! session('install.complete')) {
            return redirect()->route('install.welcome');
        }

        // Write the lock file so the installer cannot be re-run
        file_put_contents(storage_path('installed'), now()->toDateTimeString());

        $appUrl   = session('install.app_url', config('app.url'));
        $adminEmail = session('install.admin_email', '');
        $adminName  = session('install.admin_name', '');

        // Flush install session data
        session()->forget(array_filter(array_keys(session()->all()), fn ($k) => str_starts_with($k, 'install.')));

        return view('install.complete', compact('appUrl', 'adminEmail', 'adminName'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function runRequirementChecks(): array
    {
        $extensions = ['pdo', 'mbstring', 'tokenizer', 'openssl', 'curl', 'fileinfo', 'xml', 'ctype', 'bcmath', 'json'];
        $checks = [];

        // PHP version
        $checks[] = [
            'label'   => 'PHP >= 8.2',
            'status'  => version_compare(PHP_VERSION, '8.2.0', '>='),
            'value'   => PHP_VERSION,
        ];

        // Extensions
        foreach ($extensions as $ext) {
            $checks[] = [
                'label'  => "Extension: {$ext}",
                'status' => extension_loaded($ext),
                'value'  => extension_loaded($ext) ? 'Installed' : 'Missing',
            ];
        }

        // Folder permissions
        $writablePaths = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($writablePaths as $path) {
            $label = str_replace(base_path() . '/', '', $path);
            $checks[] = [
                'label'  => "Writable: {$label}",
                'status' => is_writable($path),
                'value'  => is_writable($path) ? 'Writable' : 'Not Writable',
            ];
        }

        return $checks;
    }

    private function writeEnvFile(): void
    {
        $connection = session('install.db_connection', 'mysql');
        $appKey     = 'base64:' . base64_encode(random_bytes(32));

        $dbBlock = $connection === 'sqlite'
            ? "DB_CONNECTION=sqlite\nDB_DATABASE=" . database_path('database.sqlite')
            : implode("\n", [
                'DB_CONNECTION=mysql',
                'DB_HOST=' . session('install.db_host', '127.0.0.1'),
                'DB_PORT=' . session('install.db_port', '3306'),
                'DB_DATABASE=' . session('install.db_database', 'livestockpro'),
                'DB_USERNAME=' . session('install.db_username', 'root'),
                'DB_PASSWORD=' . session('install.db_password', ''),
            ]);

        $saasMode = session('install.saas_mode', 'true');
        $appName  = addslashes(session('install.app_name', 'LivestockPro ERP'));
        $appUrl   = session('install.app_url', 'http://localhost');
        $timezone = session('install.timezone', 'UTC');

        $mailBlock = implode("\n", [
            'MAIL_MAILER=' . session('install.mail_mailer', 'log'),
            'MAIL_HOST=' . session('install.mail_host', '127.0.0.1'),
            'MAIL_PORT=' . session('install.mail_port', 587),
            'MAIL_USERNAME=' . session('install.mail_username', 'null'),
            'MAIL_PASSWORD=' . session('install.mail_password', 'null'),
            'MAIL_FROM_ADDRESS=' . session('install.mail_from', 'hello@example.com'),
            'MAIL_FROM_NAME="${APP_NAME}"',
        ]);

        $content = <<<ENV
APP_NAME="{$appName}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_TIMEZONE={$timezone}
APP_URL={$appUrl}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

SAAS_MODE={$saasMode}

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

{$dbBlock}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

{$mailBlock}

VITE_APP_NAME="\${APP_NAME}"
ENV;

        file_put_contents(base_path('.env'), $content);
    }

    private function reloadDatabaseConfig(): void
    {
        // Re-read the .env file and update the running config.
        // We manually parse .env instead of using parse_ini_file
        // because the file contains special characters like base64 strings
        // that would trigger PHP parse errors.
        if (! file_exists(base_path('.env'))) {
            return;
        }

        $env = [];
        $lines = file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }

        // Update config with database settings
        $connection = $env['DB_CONNECTION'] ?? 'mysql';

        config([
            'database.default' => $connection,
            "database.connections.{$connection}.host"     => $env['DB_HOST'] ?? '127.0.0.1',
            "database.connections.{$connection}.port"     => $env['DB_PORT'] ?? '3306',
            "database.connections.{$connection}.database" => $env['DB_DATABASE'] ?? '',
            "database.connections.{$connection}.username" => $env['DB_USERNAME'] ?? '',
            "database.connections.{$connection}.password" => $env['DB_PASSWORD'] ?? '',
        ]);

        DB::purge($connection);
        DB::reconnect($connection);
    }
}
