<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RestaurantTag extends Pivot
{
    use HasUuids;

    protected $table = 'restaurant_tag';
}
