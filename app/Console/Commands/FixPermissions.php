<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-permissions {--user=www-data : The web server user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix file and directory permissions for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = base_path();
        $webUser = $this->option('user');

        $this->info('Fixing application permissions...');
        $this->line('');

        // Permissions mapping
        $permissions = [
            // Main read-only directories: 755
            $basePath                          => 0755,
            $basePath . '/app'                 => 0755,
            $basePath . '/bootstrap'           => 0755,
            $basePath . '/config'              => 0755,
            $basePath . '/database'            => 0755,
            $basePath . '/public'              => 0755,
            $basePath . '/resources'           => 0755,
            $basePath . '/routes'              => 0755,
            $basePath . '/tests'               => 0755,

            // Writable directories: 777
            $basePath . '/storage'             => 0777,
            $basePath . '/storage/logs'        => 0777,
            $basePath . '/bootstrap/cache'     => 0777,
        ];

        // Apply chmod
        foreach ($permissions as $path => $mode) {
            if (!is_dir($path)) {
                $this->warn("  ✗ {$path} (not a directory)");
                continue;
            }

            if (@chmod($path, $mode)) {
                $this->info("  ✓ chmod {$path} to " . decoct($mode));
            } else {
                $this->error("  ✗ Failed to chmod {$path}");
            }
        }

        $this->line('');
        $this->info('Applying ownership to web server user: ' . $webUser);
        $this->line('');

        // Apply chown if running as root
        if (function_exists('posix_getuid') && posix_getuid() === 0) {
            $userInfo = posix_getpwnam($webUser);

            if ($userInfo === false) {
                $this->error("User '{$webUser}' does not exist!");
                return 1;
            }

            $uid = $userInfo['uid'];

            // Critical directories to change ownership
            $chownPaths = [
                $basePath,
                $basePath . '/storage',
                $basePath . '/storage/logs',
                $basePath . '/bootstrap/cache',
            ];

            foreach ($chownPaths as $path) {
                if (!is_dir($path) && !is_file($path)) {
                    continue;
                }

                if (@chown($path, $uid)) {
                    $this->info("  ✓ chown {$path} to {$webUser}");
                } else {
                    $this->error("  ✗ Failed to chown {$path}");
                }
            }

            // Recursively change ownership for storage and bootstrap/cache
            $this->line('');
            $this->info('Recursively changing ownership (this may take a moment)...');
            $this->recursiveChown($basePath . '/storage', $uid);
            $this->recursiveChown($basePath . '/bootstrap/cache', $uid);
        } else {
            $this->warn('Not running as root - skipping ownership changes');
            $this->info('To apply ownership, run: sudo chown -R ' . $webUser . ':' . $webUser . ' ' . $basePath);
        }

        $this->line('');
        $this->info('✓ Permissions fixed successfully!');

        return 0;
    }

    /**
     * Recursively change ownership of files and directories
     */
    private function recursiveChown(string $path, int $uid): void
    {
        try {
            if (!is_dir($path)) {
                return;
            }

            $files = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new \RecursiveIteratorIterator($files, \RecursiveIteratorIterator::SELF_FIRST);

            // Limit depth to prevent excessive I/O
            $iterator->setMaxDepth(3);

            $count = 0;
            foreach ($iterator as $file) {
                @chown($file->getPathname(), $uid);
                if (++$count % 100 === 0) {
                    $this->comment("  ... processed {$count} items");
                }
            }

            $this->info("  ✓ Processed {$count} items in {$path}");
        } catch (\Throwable $e) {
            $this->error("  ✗ Error processing {$path}: " . $e->getMessage());
        }
    }
}
