<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanTask extends Model
{
    protected $fillable = ['plan_interval_id', 'subject', 'task_name', 'is_completed'];
}
