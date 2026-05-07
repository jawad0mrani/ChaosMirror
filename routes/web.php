<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyPlanController;
use App\Models\StudyPlan;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChaosController;

Route::get('/', function () {
       // إذا المستخدم مسجل دخول، خده عالمفاعل فوراً، وإذا لأ، ارميه بصفحة تسجيل الدخول
       return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', function () {
    // جلب أحدث خطة للمستخدم الحالي (ريم أو جواد) مع الفترات والمهام
    $activePlan = StudyPlan::where('user_id', auth()->id())
                           ->with('intervals.tasks')
                           ->latest()
                           ->first();

    return view('dashboard', compact('activePlan'));
    
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('plans', StudyPlanController::class)->middleware(['auth']);

Route::post('/tasks/{task}/toggle', [App\Http\Controllers\StudyPlanController::class, 'toggleTask']);

Route::post('/focus-sessions', [App\Http\Controllers\FocusSessionController::class, 'store'])->middleware('auth');

Route::get('/statistics', [App\Http\Controllers\StatisticsController::class, 'index'])->middleware('auth')->name('statistics');

Route::get('/plans/{plan}/edit', [App\Http\Controllers\StudyPlanController::class, 'edit'])->middleware('auth')->name('plans.edit');
Route::put('/plans/{plan}', [App\Http\Controllers\StudyPlanController::class, 'update'])->middleware('auth')->name('plans.update');

Route::get('/command-center/notifications', [App\Http\Controllers\AdminNotificationController::class, 'index'])->middleware('auth')->name('admin.notifications');
Route::post('/command-center/notifications', [App\Http\Controllers\AdminNotificationController::class, 'store'])->middleware('auth')->name('admin.notifications.store');

Route::post('/intervals/{interval}/evaluate', [App\Http\Controllers\DailyEvaluationController::class, 'store'])->middleware('auth')->name('evaluations.store');

Route::put('/evaluations/{evaluation}', [App\Http\Controllers\DailyEvaluationController::class, 'update'])->middleware('auth')->name('evaluations.update');

Route::get('/scare-reem', function () {
    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
        'accept' => 'application/json',
        'content-type' => 'application/json',
    ])->post('https://onesignal.com/api/v1/notifications', [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'included_segments' => ['Total Subscriptions'], // هاد بيبعت لكل الموبايلات المشتركة
        'headings' => ['en' => 'إنذار طوارئ! 🚨', 'ar' => 'إنذار طوارئ! 🚨'],
        'contents' => ['en' => 'قومي ادرسي وراكي بكالوريا يا كسلانة!', 'ar' => 'قومي ادرسي وراكي بكالوريا يا كسلانة!'],
    ]);

    return $response->json();
});

Route::get('/chaos-center', [NotificationController::class, 'index']);
Route::post('/chaos-center/strike', [NotificationController::class, 'strike'])->name('chaos.strike');

Route::middleware(['auth'])->group(function () {
    Route::get('/chaos-center', [ChaosController::class, 'index']);
    Route::post('/chaos-center/store', [ChaosController::class, 'store'])->name('chaos.store');
    Route::post('/chaos-center/quick-strike', [ChaosController::class, 'quickStrike'])->name('chaos.quick');
    Route::post('/chaos-center/fire/{id}', [ChaosController::class, 'fireNow'])->name('chaos.fire');
    Route::delete('/chaos-center/delete/{id}', [ChaosController::class, 'destroy'])->name('chaos.delete');
});

Route::get('/setup-chaos-db', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate');
    return 'تم بناء قاعدة بيانات الفوضى بنجاح! اذهب الآن إلى /chaos-center';
});

// مسارات تدمير المصفوفات والفترات
Route::delete('/plans/{plan}', [App\Http\Controllers\StudyPlanController::class, 'destroyPlan'])->name('plans.destroy');
Route::delete('/intervals/{interval}', [App\Http\Controllers\StudyPlanController::class, 'destroyInterval'])->name('intervals.destroy');

Route::post('/git-deploy-secret-link', function () {
    $signature = request()->header('X-Hub-Signature-256');
    $payload = request()->getContent();
    $secret = env('GITHUB_WEBHOOK_SECRET');

    if ($signature !== 'sha256=' . hash_hmac('sha256', $payload, $secret)) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // 1. المسار الصحيح للمشروع
    $projectPath = '/home/reemad/www/chaos-mind'; 

    // 2. الأمر المحدث (مع الأقواس) لضمان الدخول للمسار وسحب الكود بشكل سليم واصطياد أي خطأ
    $command = "(cd {$projectPath} && GIT_SSH_COMMAND='ssh -i /home/reemad/.ssh/id_ed25519 -o StrictHostKeyChecking=no' git pull origin main) 2>&1";
    
    // استخدام exec بدل shell_exec بيعطينا مصفوفة بكل الأخطاء مع كود الحالة
    exec($command, $outputArray, $statusCode);

    // 3. تنظيف الكاش تلقائياً في حال النجاح
    if ($statusCode === 0) {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
    }

    // 4. الرد التفصيلي اللي بيظهر بـ غيت هاب
    return response()->json([
        'success' => true,
        'status_code' => $statusCode, // 0 يعني نجاح، أي رقم تاني يعني إيرور
        'output' => $outputArray, 
        'executed_command' => $command
    ]);
});

require __DIR__.'/auth.php';
