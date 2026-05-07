<?php

namespace App\Http\Controllers;

use App\Models\StudyPlan;
use App\Models\PlanInterval;
use App\Models\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyPlanController extends Controller
{
    // عرض صفحة إنشاء الخطة
    public function create()
    {
        return view('plans.create');
    }

    // حفظ الخطة في قاعدة البيانات
    public function store(Request $request)
    {
        // 1. حفظ الخطة الأساسية
        $plan = StudyPlan::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'start_date' => now(), // تبدأ من اليوم
        ]);

        // 2. معالجة الفترات والمهام المضافة
        if ($request->has('intervals')) {
            foreach ($request->intervals as $index => $intervalData) {
                // حفظ الفترة (كل 10 أيام)
                $interval = $plan->intervals()->create([
                    'title' => "الفترة رقم " . ($index + 1),
                    'duration_days' => 10,
                    'order' => $index + 1
                ]);

                // حفظ المهام داخل هذه الفترة
                foreach ($intervalData['tasks'] as $taskName) {
                    if (!empty($taskName)) {
                        $interval->tasks()->create([
                            'subject' => 'عام', // يمكن تطويرها لاحقاً
                            'task_name' => $taskName,
                            'is_completed' => false
                        ]);
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'تم تفعيل مصفوفة الخطة بنجاح!');
    }


    public function toggleTask(\App\Models\PlanTask $task)
    {
        // عكس حالة الإنجاز
        $task->update(['is_completed' => !$task->is_completed]);

        // جلب أو إنشاء سجل نقاط المستخدم الحالي
        $achievement = \App\Models\Achievement::firstOrCreate(
            ['user_id' => auth()->id()],
            ['points' => 0]
        );

        // إضافة أو سحب النقاط
        if ($task->is_completed) {
            $achievement->increment('points', 50); // مكافأة 50 نقطة
        } else {
            $achievement->decrement('points', 50);
        }

        return response()->json([
            'status' => 'success',
            'new_xp' => $achievement->points
        ]);
    }



    // عرض صفحة التعديل مع جلب بيانات الخطة كاملة
    public function edit(\App\Models\StudyPlan $plan)
    {
        // التأكد من أن الخطة تابعة للمستخدم
        if ($plan->user_id !== auth()->id()) abort(403);
        
        $plan->load('intervals.tasks'); // جلب الفترات والمهام
        return view('plans.edit', compact('plan'));
    }

    // حفظ التعديلات وإضافة البيانات الجديدة
    public function update(Request $request, \App\Models\StudyPlan $plan)
    {
        // 1. تحديث عنوان الخطة
        $plan->update(['title' => $request->title]);

        // 2. فك تشفير البيانات القادمة من Alpine.js
        $intervalsData = json_decode($request->plan_data, true);

        foreach ($intervalsData as $index => $intervalData) {
            if (isset($intervalData['is_new']) && $intervalData['is_new']) {
                // إنشاء فترة جديدة بالكامل
                $interval = $plan->intervals()->create([
                    'title' => "الفترة رقم " . ($plan->intervals()->count() + 1),
                    'duration_days' => 10,
                    'order' => $index + 1
                ]);
            } else {
                // جلب الفترة الحالية
                $interval = \App\Models\PlanInterval::find($intervalData['id']);
            }

            // معالجة المهام داخل هذه الفترة
            foreach ($intervalData['tasks'] as $taskData) {
                if (!empty($taskData['name'])) {
                    if (isset($taskData['is_new']) && $taskData['is_new']) {
                        // إضافة مهمة جديدة
                        $interval->tasks()->create([
                            'subject' => 'عام',
                            'task_name' => $taskData['name'],
                            'is_completed' => false
                        ]);
                    } else {
                        // تحديث اسم المهمة القديمة (إذا لم تكن مكتملة)
                        if (!$taskData['is_completed']) {
                            $task = \App\Models\PlanTask::find($taskData['id']);
                            if ($task) {
                                $task->update(['task_name' => $taskData['name']]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'تم تحديث المصفوفة بنجاح!');
    }

    // 1. تدمير المصفوفة (الخطة) بالكامل
    public function destroyPlan(\App\Models\StudyPlan $plan)
    {
        // حماية أمنية: منع أي مستخدم من حذف مصفوفة غيره
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'توقف! غير مصرح لك بتدمير مصفوفات الآخرين.');
        }

        // الحذف (سيقوم الـ Cascade في قاعدة البيانات بحذف الفترات والمهام تلقائياً)
        $plan->delete();

        return redirect()->route('dashboard')->with('success', 'تم نسف المصفوفة بالكامل بنجاح!');
    }

    // 2. حذف فترة محددة (Interval) من داخل المصفوفة
    public function destroyInterval(\App\Models\PlanInterval $interval)
    {
        // جلب الخطة الأب لنتأكد من هوية المالك
        $plan = \App\Models\StudyPlan::findOrFail($interval->study_plan_id);
        
        // حماية أمنية
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحذف هذه الفترة.');
        }

        $interval->delete(); // سيتم حذف المهام التابعة لهذه الفترة أيضاً بفضل الـ Cascade (إذا كنت ضايفه لجدول المهام)

        return back()->with('success', 'تم إزالة الفترة بنجاح.');
    }
    
}
