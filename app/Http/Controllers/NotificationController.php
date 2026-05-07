<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index() {
        return view('chaos-center');
    }

    public function strike(Request $request) {
        $payload = [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'included_segments' => ['All'], 
            'headings' => ['en' => $request->title ?? 'تنبيه من مرآة الفوضى'],
            'contents' => ['en' => $request->message ?? 'ارجعي للدراسة فوراً!'],
        ];

        if ($request->filled('schedule_time')) {
            $time = Carbon::parse($request->schedule_time)->timezone('Asia/Baghdad')->format('Y-m-d H:i:s \G\M\TO');
            $payload['send_after'] = $time;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', $payload);

        if ($response->successful()) {
            return back()->with('success', 'تم تفخيخ الإشعار بنجاح!');
        }

        return back()->with('error', 'فشلت الضربة: ' . $response->body());
    }
}