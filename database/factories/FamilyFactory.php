<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FamilyFactory extends Factory
{
    protected $model = Family::class;

    public function definition(): array
    {
        $name = fake('en_US')->lastName().' Family';

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'invite_code' => Str::random(16),
            'settings' => [],
        ];
    }
}
