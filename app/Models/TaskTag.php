<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskTag extends Pivot
{
    use HasUuids;

    protected $table = 'task_tag';
}
