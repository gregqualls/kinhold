<?php

namespace Database\Factories;

use App\Enums\FamilyRole;
use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake('en_US')->name(),
            'email' => fake('en_US')->unique()->safeEmail(),
            'password' => Hash::make('Password1'),
            'family_id' => Family::factory(),
            'family_role' => FamilyRole::Parent,
        ];
    }

    public function child(): static
    {
        return $this->state(fn () => ['family_role' => FamilyRole::Child]);
    }

    public function parent(): static
    {
        return $this->state(fn () => ['family_role' => FamilyRole::Parent]);
    }
}
