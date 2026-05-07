<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\PushNotification;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    $now = now()->format('Y-m-d H:i:00');
    
    // جلب الإشعارات المجدولة التي حان وقتها ولم تُرسل بعد
    $notifications = PushNotification::where('is_sent', false)
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', $now)
        ->get();

    foreach ($notifications as $note) {
        // كود OneSignal هنا (نفس الموجود في الـ Controller)
        $appId = env('ONESIGNAL_APP_ID');
        $restApiKey = env('ONESIGNAL_REST_API_KEY');
        
        if($appId) {
            $fields = [
                'app_id' => $appId,
                'headings' => ['en' => $note->title, 'ar' => $note->title],
                'contents' => ['en' => $note->message, 'ar' => $note->message],
            ];
            if ($note->user_id) {
                $fields['include_external_user_ids'] = [(string)$note->user_id];
            } else {
                $fields['included_segments'] = ['All'];
            }
            Http::withToken($restApiKey)->post('https://onesignal.com/api/v1/notifications', $fields);
        }
        
        // تعليم الإشعار كمرسل
        $note->update(['is_sent' => true]);
    }
})->everyMinute();