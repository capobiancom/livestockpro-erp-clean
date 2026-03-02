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
        $allPassed = collect($checks)->every(fn($c) => $c['status']);

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

            // Force the installer-selected connection (prevents accidental mysql/sqlite mismatch)
            $connection = session('install.db_connection', config('database.default', 'mysql'));
            config(['database.default' => $connection]);
            DB::purge($connection);
            DB::reconnect($connection);

            // If using SQLite, ensure the database file exists.
            // Do NOT delete it: users may want the sqlite file to persist in /database.
            if ($connection === 'sqlite') {
                // Disconnect first to release any locks
                DB::disconnect($connection);

                $dbPath = config('database.connections.sqlite.database');

                if (! is_dir(dirname($dbPath))) {
                    @mkdir(dirname($dbPath), 0777, true);
                }

                if (! file_exists($dbPath)) {
                    @touch($dbPath);
                }

                // Reconnect so migrations can run
                DB::reconnect($connection);
            }

            // Always start from a clean schema during installation.
            // This prevents "table already exists" errors when the installer is re-run.
            //
            // NOTE: This project ships without Laravel migration files (database/migrations),
            // so migrate:fresh would do nothing and tables like `users` would not exist.
            // In that case, we fall back to importing the shipped SQL schema.
            $migrationsPath = database_path('migrations');
            $hasLaravelMigrations = is_dir($migrationsPath) && count(glob($migrationsPath . '/*.php') ?: []) > 0;

            if ($hasLaravelMigrations) {
                Artisan::call('migrate:fresh', [
                    '--database' => $connection,
                    '--force' => true,
                    '--no-interaction' => true,
                ]);
            } else {
                // Import schema from SQL file
                $schemaFile = database_path('schema/sqlite-schema.sql');
                if (! file_exists($schemaFile)) {
                    throw new \RuntimeException("No migrations found and schema file missing at: {$schemaFile}");
                }

                $sql = file_get_contents($schemaFile);

                if ($connection === 'sqlite') {
                    // SQLite can execute the schema as-is
                    DB::connection($connection)->unprepared($sql);
                } else {
                    // MySQL: convert the SQLite-oriented schema to MySQL-friendly SQL.
                    // This is a best-effort conversion for installer usage.
                    $mysqlSql = $sql;

                    // Remove SQLite double quotes around identifiers
                    $mysqlSql = str_replace('"', '`', $mysqlSql);

                    // Remove CHECK constraints (MySQL parsing differs; keep it simple for install)
                    $mysqlSql = preg_replace('/\s+check\(`[^`]+`\s+in\([^)]+\)\)\s*/i', ' ', $mysqlSql);

                    // Type conversions
                    $mysqlSql = preg_replace('/\binteger primary key autoincrement not null\b/i', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', $mysqlSql);
                    $mysqlSql = preg_replace('/\binteger primary key autoincrement\b/i', 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', $mysqlSql);
                    $mysqlSql = preg_replace('/\binteger\b/i', 'BIGINT', $mysqlSql);
                    $mysqlSql = preg_replace('/\btinyint\(1\)\b/i', 'TINYINT(1)', $mysqlSql);
                    $mysqlSql = preg_replace('/\bdatetime\b/i', 'DATETIME', $mysqlSql);
                    $mysqlSql = preg_replace('/\bdate\b/i', 'DATE', $mysqlSql);
                    $mysqlSql = preg_replace('/\btime\b/i', 'TIME', $mysqlSql);
                    $mysqlSql = preg_replace('/\bnumeric\b/i', 'DECIMAL(18,2)', $mysqlSql);
                    $mysqlSql = preg_replace('/\bfloat\b/i', 'DOUBLE', $mysqlSql);
                    $mysqlSql = preg_replace('/\btext\b/i', 'LONGTEXT', $mysqlSql);

                    // MySQL requires a length for VARCHAR. SQLite allows bare "varchar".
                    // Use a safe default length for installer schema import.
                    $mysqlSql = preg_replace('/\bvarchar\b(?!\s*\()/i', 'VARCHAR(255)', $mysqlSql);

                    // SQLite uses default CURRENT_TIMESTAMP without parentheses; MySQL accepts it, keep.
                    // Remove INSERTs into migrations table (we are not using Laravel migrator here)
                    $mysqlSql = preg_replace('/^INSERT INTO `migrations`.*?;\s*$/mi', '', $mysqlSql);

                    // Split statements and execute one by one (safer for MySQL)
                    $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $mysqlSql)));

                    DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
                    foreach ($statements as $stmt) {
                        if ($stmt === '') {
                            continue;
                        }

                        // If the installer is re-run on the same database, some CREATE INDEX statements
                        // can fail with "Duplicate key name" (MySQL error 1061). SQLite schema uses
                        // CREATE INDEX without IF NOT EXISTS, so we ignore that specific error.
                        try {
                            DB::connection($connection)->unprepared($stmt . ';');
                        } catch (Throwable $e) {
                            $msg = $e->getMessage();

                            if (
                                str_contains($msg, 'Duplicate key name') ||
                                str_contains($msg, 'SQLSTATE[42000]: Syntax error or access violation: 1061')
                            ) {
                                continue;
                            }

                            throw $e;
                        }
                    }
                    DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');
                }
            }

            // Seeders expect tables to exist now.
            Artisan::call('db:seed', [
                '--database' => $connection,
                '--force' => true,
                '--no-interaction' => true,
            ]);

            Artisan::call('key:generate', ['--force' => true]);
            Artisan::call('storage:link', ['--force' => true]);

            // Avoid optimize:clear during install because it may try to clear the cache store.
            // When CACHE_STORE=database and the cache table isn't present yet, it can fail.
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Persist the "migrations done" flag reliably.
            // Some hosting setups / AJAX flows can lose the session write if we immediately redirect.
            session()->put('install.migrations_done', true);
            session()->save();

            // If the request was made via fetch/AJAX, return JSON so the frontend can reliably
            // redirect to the next step. Returning a redirect here often results in fetch()
            // receiving HTML, and the browser not navigating.
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('install.admin'),
                ]);
            }

            return redirect()->route('install.admin')->with('success', true);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ── Step 6: Create Admin Account ──────────────────────────────────────

    public function admin(Request $request)
    {
        // If migrations were triggered via fetch(), the browser may navigate to /install/admin
        // before the session is fully persisted on some servers. As a fallback, accept a query flag.
        /*if (! session('install.migrations_done') && ! $request->boolean('migrated')) {
            return redirect()->route('install.migrate');
        }
        */

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
        session()->forget(array_filter(array_keys(session()->all()), fn($k) => str_starts_with($k, 'install.')));

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
                'DB_DATABASE=' . session('install.db_database', 'livestock'),
                'DB_USERNAME=' . session('install.db_username', 'root'),
                'DB_PASSWORD=' . session('install.db_password', "'moin786#Ruhul786#Mahian'"),
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
APP_DEBUG=true
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

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=file
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
        $connection = trim($connection, "\"'");

        // Normalize values (strip surrounding quotes) so passwords like "pass" or 'pass'
        // don't get passed to PDO including the quote characters.
        $normalize = static function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            $value = trim($value);

            if ($value === '') {
                return '';
            }

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            return $value;
        };

        $dbHost = $normalize($env['DB_HOST'] ?? null) ?? '127.0.0.1';
        $dbPort = $normalize($env['DB_PORT'] ?? null) ?? '3306';
        $dbName = $normalize($env['DB_DATABASE'] ?? null) ?? '';
        $dbUser = $normalize($env['DB_USERNAME'] ?? null) ?? '';
        $dbPass = $normalize($env['DB_PASSWORD'] ?? null) ?? '';

        // For sqlite, ensure we point to a real file path (not quoted) and that the directory exists.
        $sqlitePath = $normalize($env['DB_DATABASE'] ?? null) ?? database_path('database.sqlite');
        if (! is_dir(dirname($sqlitePath))) {
            @mkdir(dirname($sqlitePath), 0777, true);
        }
        if (! file_exists($sqlitePath)) {
            @touch($sqlitePath);
        }

        config([
            'database.default' => $connection,
            "database.connections.{$connection}.host"     => $dbHost,
            "database.connections.{$connection}.port"     => $dbPort,
            "database.connections.{$connection}.database" => $dbName,
            "database.connections.{$connection}.username" => $dbUser,
            "database.connections.{$connection}.password" => $dbPass,

            // Keep sqlite config in sync too (installer can switch between mysql/sqlite)
            'database.connections.sqlite.database' => $sqlitePath,
        ]);

        DB::purge($connection);
        DB::reconnect($connection);
    }
}
