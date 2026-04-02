<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshDemo extends Command
{
    protected $signature = 'app:refresh-demo';

    protected $description = 'Re-seed the demo family so data stays fresh. Safe to run repeatedly.';

    public function handle(): int
    {
        $this->info('Refreshing demo family data...');

        // Runs all seeders (DatabaseSeeder wipes+recreates demo family,
        // VaultCategorySeeder backfills categories). If real-family seeders
        // are added later, scope this to --class=DatabaseSeeder instead.
        $this->call('db:seed', ['--force' => true]);
        $this->call('app:seed-default-badges');

        $this->info('Demo family refreshed at '.now()->toDateTimeString());

        return Command::SUCCESS;
    }
}
