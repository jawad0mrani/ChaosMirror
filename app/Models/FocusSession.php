<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FocusSession extends Model
{
    // السماح بإدخال هذه البيانات
    protected $fillable = [
        'user_id', 
        'start_time',
        'duration_minutes', 
        'completed_at'
    ];

    // علاقة الجلسة مع المستخدم (ريم)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
