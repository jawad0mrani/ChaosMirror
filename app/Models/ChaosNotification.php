<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChaosNotification extends Model
{
    use HasFactory;

    // الحقول المسموح تعبئتها
    protected $fillable = [
        'title',
        'message',
        'type',
        'scheduled_at',
        'fired_count',
        'status',
    ];

    // تحويل حقل التاريخ ليكون كائن Carbon ليسهل التعامل معه
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}