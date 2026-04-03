<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\Tag;
use App\Models\User;

/**
 * Shared context for demo seeders. Provides the demo family, users, and tags
 * so each sub-seeder can look them up without passing parameters.
 */
trait DemoFamilyContext
{
    private ?Family $demoFamily = null;

    private ?User $mike = null;

    private ?User $sarah = null;

    private ?User $emma = null;

    private ?User $jake = null;

    private ?User $lily = null;

    /** @var array<string, Tag> */
    private array $tags = [];

    protected function loadDemoContext(): void
    {
        $this->demoFamily = Family::where('slug', 'q32-demo-family')->firstOrFail();

        $users = User::where('family_id', $this->demoFamily->id)->get()->keyBy('name');
        $this->mike = $users['Mike'];
        $this->sarah = $users['Sarah'];
        $this->emma = $users['Emma'];
        $this->jake = $users['Jake'];
        $this->lily = $users['Lily'];

        $this->tags = Tag::where('family_id', $this->demoFamily->id)
            ->get()
            ->keyBy('name')
            ->toArray();

        // Re-key as Tag models
        $this->tags = Tag::where('family_id', $this->demoFamily->id)
            ->get()
            ->keyBy('name')
            ->all();
    }

    protected function family(): Family
    {
        return $this->demoFamily;
    }

    protected function familyId(): string
    {
        return $this->demoFamily->id;
    }

    /** @return User[] */
    protected function kids(): array
    {
        return [$this->emma, $this->jake, $this->lily];
    }

    /** @return User[] */
    protected function parents(): array
    {
        return [$this->mike, $this->sarah];
    }

    /** @return User[] */
    protected function everyone(): array
    {
        return [$this->mike, $this->sarah, $this->emma, $this->jake, $this->lily];
    }
}
