<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyEvaluation extends Model
{
    protected $guarded = []; // السماح بإدخال كل الحقول
    
    public function interval() {
        return $this->belongsTo(PlanInterval::class, 'plan_interval_id');
    }
}