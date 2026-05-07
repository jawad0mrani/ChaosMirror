<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AdminNotificationController extends Controller
{
    // عرض لوحة تحكم الإشعارات
    public function index()
    {
        // حماية بسيطة: تأكد أنك أنت فقط (جواد) من يدخل (نفترض أن الـ ID الخاص بك هو 1)
        // إذا كان حسابك رقمه مختلف، قم بتغيير الرقم 1
        if (auth()->id() !== 1) abort(403, 'Unauthorized access to Command Center.');

        $users = User::all();
        $notifications = PushNotification::orderBy('created_at', 'desc')->get();
        return view('admin.notifications', compact('users', 'notifications'));
    }

    // حفظ وجدولة أو إرسال الإشعار
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $notification = PushNotification::create([
            'user_id' => $request->user_id, // إذا كان فارغاً سيذهب للجميع
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type ?? 'custom',
            'scheduled_at' => $request->scheduled_at,
            'is_sent' => $request->scheduled_at ? false : true, // إذا لم يكن مجدولاً، اعتبره مرسلاً الآن
        ]);

        // إذا لم يكن هناك وقت جدولة، أرسله فوراً عبر OneSignal
        if (!$request->scheduled_at) {
            $this->sendToOneSignal($notification);
        }

        return back()->with('success', 'تم حفظ وتفعيل بروتوكول الإشعار بنجاح!');
    }

    // الدالة الخفية التي تتصل بتطبيق الموبايل (OneSignal)
    private function sendToOneSignal($notification)
    {
        // سنضع مفاتيح OneSignal هنا لاحقاً عندما نبرمج الـ WebView
        $appId = env('ONESIGNAL_APP_ID', 'YOUR_APP_ID_HERE');
        $restApiKey = env('ONESIGNAL_REST_API_KEY', 'YOUR_REST_KEY_HERE');

        if($appId === 'YOUR_APP_ID_HERE') return; // تجاوز إذا لم يتم إعداد المفاتيح بعد

        $fields = [
            'app_id' => $appId,
            'headings' => ['en' => $notification->title, 'ar' => $notification->title],
            'contents' => ['en' => $notification->message, 'ar' => $notification->message],
        ];

        // إذا كان موجهاً لمستخدم معين (ريم مثلاً)، يجب أن نرسل إيميلها كـ External ID
        if ($notification->user_id) {
            $user = User::find($notification->user_id);
            $fields['include_external_user_ids'] = [(string)$user->id];
        } else {
            $fields['included_segments'] = ['All'];
        }

        Http::withToken($restApiKey)->post('https://onesignal.com/api/v1/notifications', $fields);
    }
}