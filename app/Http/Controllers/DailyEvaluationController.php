<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyEvaluation;
use App\Models\PlanInterval;
use Illuminate\Support\Facades\Storage;

class DailyEvaluationController extends Controller
{
    // إضافة تقرير جديد (من الداشبورد)
    public function store(Request $request, PlanInterval $interval)
    {
        // 🚨 حماية أمنية: منع إضافة تقييمين في نفس اليوم لنفس الفترة
        $alreadyEvaluated = $interval->evaluations()
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($alreadyEvaluated) {
            return redirect()->route('dashboard')->with('error', 'لقد قمت بإضافة تقرير هذا اليوم مسبقاً! يمكنك تعديله من مركز التحليل.');
        }

        // 1. خوارزمية الوسط الحسابي العادل
        $scores = [
            $request->focus_score,
            $request->exercises_score
        ]; 

        // إضافة المصادر فقط إذا تم استخدامها
        if ($request->has('used_youtube') && $request->youtube_score) $scores[] = $request->youtube_score;
        if ($request->has('used_notebook') && $request->notebook_score) $scores[] = $request->notebook_score;
        if ($request->has('used_book') && $request->book_score) $scores[] = $request->book_score;

        // حساب المحصلة (مجموع التقييمات تقسيم عددها)
        $finalScore = array_sum($scores) / count($scores);

        // 2. معالجة الصورة المضغوطة من الموبايل
        $imagePath = null;
        if ($request->compressed_image) {
            $imageParts = explode(";base64,", $request->compressed_image);
            $imageBase64 = base64_decode($imageParts[1]);
            
            $fileName = 'evaluations/' . uniqid() . '.webp'; 
            Storage::disk('public')->put($fileName, $imageBase64);
            $imagePath = $fileName;
        }

        // 3. الحفظ في قاعدة البيانات
        DailyEvaluation::create([
            'plan_interval_id' => $interval->id,
            'user_id' => auth()->id(),
            'day_number' => $interval->evaluations()->count() + 1,
            'focus_score' => $request->focus_score,
            'exercises_score' => $request->exercises_score,
            'mental_fatigue' => $request->mental_fatigue,
            'youtube_score' => $request->has('used_youtube') ? $request->youtube_score : null,
            'notebook_score' => $request->has('used_notebook') ? $request->notebook_score : null,
            'book_score' => $request->has('used_book') ? $request->book_score : null,
            'final_score' => $finalScore,
            'notes' => $request->notes,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('dashboard')->with('success', "تم إقفال وتشفير تقرير اليوم بنجاح! المحصلة: " . round($finalScore, 1) . "/10");
    }

    // تعديل تقرير موجود (من مركز التحليل)
    public function update(Request $request, DailyEvaluation $evaluation)
    {
        // 🚨 حماية أمنية: منع تعديل تقارير الأيام السابقة (يُسمح بتعديل تقرير "اليوم" فقط)
        if (!$evaluation->created_at->isToday()) {
            abort(403, 'لا يمكن تعديل التقارير العصبية الخاصة بالأيام السابقة، لقد تم إقفالها.');
        }

        // إعادة حساب المحصلة
        $scores = [
            $request->focus_score,
            $request->exercises_score
        ]; 

        if ($request->has('used_youtube') && $request->youtube_score) $scores[] = $request->youtube_score;
        if ($request->has('used_notebook') && $request->notebook_score) $scores[] = $request->notebook_score;
        if ($request->has('used_book') && $request->book_score) $scores[] = $request->book_score;

        $finalScore = array_sum($scores) / count($scores);

        // التعامل مع الصورة
        $imagePath = $evaluation->image_path; // الاحتفاظ بالصورة القديمة افتراضياً
        if ($request->compressed_image) {
            $imageParts = explode(";base64,", $request->compressed_image);
            $imageBase64 = base64_decode($imageParts[1]);
            
            $fileName = 'evaluations/' . uniqid() . '.webp'; 
            Storage::disk('public')->put($fileName, $imageBase64);
            
            // حذف الصورة القديمة لتوفير المساحة
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $fileName;
        }

        // تحديث البيانات
        $evaluation->update([
            'focus_score' => $request->focus_score,
            'exercises_score' => $request->exercises_score,
            'mental_fatigue' => $request->mental_fatigue,
            'youtube_score' => $request->has('used_youtube') ? $request->youtube_score : null,
            'notebook_score' => $request->has('used_notebook') ? $request->notebook_score : null,
            'book_score' => $request->has('used_book') ? $request->book_score : null,
            'final_score' => $finalScore,
            'notes' => $request->notes,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('statistics')->with('success', 'تم تعديل وتحديث تقرير اليوم بنجاح!');
    }
}