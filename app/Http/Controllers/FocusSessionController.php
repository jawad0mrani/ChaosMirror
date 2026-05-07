<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Achievement;
// افترضنا أنك تملك موديل FocusSession، إذا لم تنشئه نفذ: php artisan make:model FocusSession
use App\Models\FocusSession;

class FocusSessionController extends Controller
{
    public function store(Request $request)
    {
        $duration = $request->input('duration_minutes', 25);
        // استقبال إشارة "الاكتمال التام" من الواجهة
        $isFullyCompleted = $request->input('is_fully_completed', false);

        // استنتاج وقت البداية
        $startTime = now()->subMinutes($duration);

        // حفظ الجلسة 
        \App\Models\FocusSession::create([
            'user_id' => auth()->id(),
            'start_time' => $startTime,
            'duration_minutes' => $duration,
            'completed_at' => now(),
        ]);

        // --- نظام حساب النقاط والمكافآت ---
        $gainedXp = $duration; // الأساس: 1 XP لكل دقيقة
        $bonusMessage = "";

        // إضافة البونص إذا صمدت للنهاية
        if ($isFullyCompleted) {
            $gainedXp += 10; // مكافأة الصمود
            $bonusMessage = " | +10 XP مكافأة صمود! 🌟";
        }

        // إضافة النقاط للمستخدم
        $achievement = \App\Models\Achievement::firstOrCreate(
            ['user_id' => auth()->id()],
            ['points' => 0]
        );
        $achievement->increment('points', $gainedXp);

        return response()->json([
            'status' => 'success',
            'new_xp' => $achievement->points,
            'message' => "تم تسجيل ({$duration}) دقيقة: +{$duration} XP" . $bonusMessage
        ]);
    }
}
