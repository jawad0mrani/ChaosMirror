<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ChaosNotification;
use Carbon\Carbon;

class ChaosController extends Controller
{
    // دالة حماية أمنية (تفادي مشكلة middleware في لاراڤيل 11)
    private function checkAccess() {
        if (!auth()->check() || auth()->id() != 1) {
            abort(403, 'غير مصرح لك بدخول مركز العمليات السري.');
        }
    }

    public function index()
    {
        $this->checkAccess();
        $notifications = ChaosNotification::orderBy('created_at', 'desc')->get();
        return view('chaos-center', compact('notifications'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:immediate,once,daily,weekly',
        ]);

        $status = 'pending';
        $scheduledAt = $request->schedule_time ? Carbon::parse($request->schedule_time)->timezone('Asia/Baghdad') : now();

        if ($request->type == 'immediate') {
            $status = 'completed';
        } elseif (in_array($request->type, ['daily', 'weekly'])) {
            $status = 'active';
        }

        $notification = ChaosNotification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'scheduled_at' => $scheduledAt,
            'status' => $status,
        ]);

        if ($request->type == 'immediate') {
            $this->sendToOneSignal($notification->title, $notification->message);
            $notification->increment('fired_count');
        }

        return back()->with('success', 'تم تسجيل العملية بنجاح في قاعدة البيانات!');
    }

    public function quickStrike(Request $request)
    {
        $this->checkAccess();
        $title = $request->input('title', 'إنذار طوارئ 🚨');
        $message = $request->input('message', 'العودة للدراسة فوراً!');
        
        $this->sendToOneSignal($title, $message);

        ChaosNotification::create([
            'title' => '[ضربة سريعة] ' . $title,
            'message' => $message,
            'type' => 'immediate',
            'scheduled_at' => now(),
            'status' => 'completed',
            'fired_count' => 1,
        ]);

        return back()->with('success', 'تم قصف الهدف بنجاح بالضربة السريعة!');
    }

    public function destroy($id)
    {
        $this->checkAccess();
        ChaosNotification::findOrFail($id)->delete();
        return back()->with('success', 'تم تفكيك العبوة المجدولة.');
    }

    public function fireNow($id)
    {
        $this->checkAccess();
        $notification = ChaosNotification::findOrFail($id);
        $this->sendToOneSignal($notification->title, $notification->message);
        
        $notification->increment('fired_count');
        if($notification->type == 'once') {
            $notification->update(['status' => 'completed']);
        }
        
        return back()->with('success', 'تم إطلاق الإشعار يدوياً بنجاح!');
    }

    private function sendToOneSignal($title, $message)
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'included_segments' => ['Total Subscriptions'],
            'headings' => ['ar' => $title, 'en' => $title],
            'contents' => ['ar' => $message, 'en' => $message],
        ]);
    }
}