<?php

namespace App\Console\Commands;

use App\Models\Family;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class SeedDefaultBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-default-badges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default badges for families that have fewer than 20 badges. Safe to run multiple times.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $families = Family::withCount('badges')->get();
        $seeded = 0;

        foreach ($families as $family) {
            if ($family->badges_count >= 20) {
                $this->line("Skipping {$family->name} — already has {$family->badges_count} badges.");

                continue;
            }

            // Use the first parent as creator, or null if no members
            $creator = $family->members()
                ->where('family_role', 'parent')
                ->first();

            BadgeService::createDefaultBadges($family->id, $creator?->id);
            $seeded++;
            $this->info("Created default badges for {$family->name}.");
        }

        $this->newLine();
        $this->info("Done. Seeded default badges for {$seeded} ".($seeded === 1 ? 'family' : 'families').'.');

        return Command::SUCCESS;
    }
}
