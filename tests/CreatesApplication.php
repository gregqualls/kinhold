<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Ensure framework storage directories exist (missing in CI, worktrees, fresh clones)
        foreach (['cache', 'sessions', 'views'] as $dir) {
            $path = storage_path("framework/{$dir}");
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        return $app;
    }
}
