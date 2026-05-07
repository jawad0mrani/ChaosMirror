<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    protected $fillable = ['user_id', 'title', 'start_date'];

    public function intervals() {
        return $this->hasMany(PlanInterval::class);
    }
}
