<?php

namespace App\Http\Controllers;

use App\Models\DailyEvaluation;
use Illuminate\Http\Request;
use App\Models\FocusSession;
use App\Models\Achievement;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // 1. حساب دقائق التركيز لليوم الحالي
        $todayMinutes = FocusSession::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('duration_minutes');

        // 2. الهدف اليومي (الافتراضي 6 ساعات = 360 دقيقة)
        $dailyGoal = 360; 
        $progressPercentage = min(($todayMinutes / $dailyGoal) * 100, 100);

        // 3. جلب الـ XP الكلي
        $totalXp = Achievement::where('user_id', $userId)->value('points') ?? 0;

        // 4. تجهيز بيانات الرسم البياني للأيام السبعة الماضية
        $days = [];
        $minutesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            // اسم اليوم بالعربي أو الانجليزي (استخدمنا الإنجليزي القصير للشكل المستقبلي)
            $days[] = $date->format('D'); 
            
            $mins = FocusSession::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->sum('duration_minutes');
                
            $minutesData[] = $mins;
        }

        $evaluations = DailyEvaluation::with('interval')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('statistics', compact('evaluations','todayMinutes', 'dailyGoal', 'progressPercentage', 'totalXp', 'days', 'minutesData'));
    }
}