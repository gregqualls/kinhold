<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipeTag extends Pivot
{
    use HasUuids;

    protected $table = 'recipe_tag';
}
