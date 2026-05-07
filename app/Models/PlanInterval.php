<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanInterval extends Model
{
    protected $fillable = ['study_plan_id', 'title', 'duration_days', 'order'];

    use HasFactory;

    protected $guarded = [];

    // العلاقة مع الخطة الأم
    public function plan()
    {
        return $this->belongsTo(StudyPlan::class, 'study_plan_id');
    }

    // العلاقة مع المهام (الدروس)
    public function tasks()
    {
        return $this->hasMany(PlanTask::class);
    }

    // السطر السحري الذي كان ينقصنا: العلاقة مع التقييمات اليومية
    public function evaluations()
    {
        return $this->hasMany(DailyEvaluation::class);
    }
}
