<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مركز عمليات الفوضى ☢️</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap');
        body { 
            font-family: 'Tajawal', sans-serif; 
            background-color: #050505; 
            color: #e2e8f0; 
            background-image: 
                linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)),
                repeating-linear-gradient(0deg, transparent, transparent 2px, #2a004d 2px, #2a004d 4px);
        }
        .neon-border { border: 1px solid #ff0055; box-shadow: 0 0 10px #ff0055, inset 0 0 10px #ff0055; }
        .cyber-table th { background: rgba(255, 0, 85, 0.1); border-bottom: 2px solid #ff0055; }
        .cyber-table td { border-bottom: 1px solid #333; }
        .glitch-text { text-shadow: 2px 0 0 red, -2px 0 0 cyan; }
    </style>
</head>
<body class="min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- الهيدر -->
        <div class="text-center bg-black/50 p-6 rounded-xl neon-border">
            <h1 class="text-5xl font-black text-red-500 mb-2 glitch-text">☢️ SYSTEM ONLINE: CHAOS MIRROR ☢️</h1>
            <p class="text-red-300 font-bold tracking-widest text-lg">لوحة التحكم الدكتاتورية - الهدف: طالبة البكالوريا (ريم)</p>
            <a href="{{ url('/') }}" class="inline-block mt-4 text-blue-400 hover:text-blue-300 underline text-sm">العودة للموقع الرئيسي</a>
        </div>

        @if(session('success'))
            <div class="bg-green-900 border-l-4 border-green-500 p-4 text-green-300 font-bold shadow-lg shadow-green-900/50">
                ✔️ {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- العمود الأيمن: ترسانة الأسلحة السريعة & فورم الإضافة -->
            <div class="space-y-8 lg:col-span-1">
                
                <!-- أزرار الطوارئ (Quick Strikes) -->
                <div class="bg-gray-900 rounded-xl p-6 neon-border">
                    <h2 class="text-2xl font-black text-yellow-500 mb-4 border-b border-gray-700 pb-2">⚡ ترسانة الطوارئ</h2>
                    <p class="text-sm text-gray-400 mb-4">ضربات قاضية بضغطة زر (بدون جدولة).</p>
                    
                    <div class="space-y-3">
                        <form action="{{ route('chaos.quick') }}" method="POST">
                            @csrf
                            <input type="hidden" name="title" value="🚨 انخفاض مستوى التركيز!">
                            <input type="hidden" name="message" value="تم رصدك.. اتركي الموبايل وافتحي كتاب الفيزياء فوراً.">
                            <button type="submit" class="w-full bg-red-700 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition border border-red-500 shadow-[0_0_10px_red]">
                                📱 قصف محاولة استخدام الموبايل
                            </button>
                        </form>

                        <form action="{{ route('chaos.quick') }}" method="POST">
                            @csrf
                            <input type="hidden" name="title" value="⏰ الوقت يمر!">
                            <input type="hidden" name="message" value="البكالوريا لا ترحم المتخاذلين.. استيقظي وادرسي.">
                            <button type="submit" class="w-full bg-orange-700 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded transition border border-orange-500 shadow-[0_0_10px_orange]">
                                ⏳ قصف تضييع الوقت
                            </button>
                        </form>
                    </div>
                </div>

                <!-- برمجة هجوم جديد -->
                <div class="bg-gray-900 rounded-xl p-6 border border-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.3)]">
                    <h2 class="text-2xl font-black text-blue-400 mb-4 border-b border-gray-700 pb-2">⚙️ برمجة كمين جديد</h2>
                    
                    <form action="{{ route('chaos.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-gray-300 font-bold mb-1">عنوان الكمين:</label>
                            <input type="text" name="title" required class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-white focus:border-blue-500 outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 font-bold mb-1">رسالة الرعب:</label>
                            <textarea name="message" required rows="3" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-white focus:border-blue-500 outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-gray-300 font-bold mb-1">نوع الهجوم:</label>
                            <select name="type" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-white focus:border-blue-500 outline-none">
                                <option value="immediate">🚀 قصف فوري (الآن)</option>
                                <option value="once">🕒 كمين مؤقت (لمرة واحدة)</option>
                                <option value="daily">🔄 تعذيب يومي (نفس الوقت كل يوم)</option>
                                <option value="weekly">📅 غارة أسبوعية</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 font-bold mb-1">توقيت التنفيذ (للمجدول):</label>
                            <input type="datetime-local" name="schedule_time" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-white [color-scheme:dark] outline-none">
                        </div>

                        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-600 text-white font-black py-3 rounded uppercase tracking-widest transition">
                            برمجة الهجوم 🎯
                        </button>
                    </form>
                </div>
            </div>

            <!-- العمود الأيسر: رادار العمليات (جدول الإشعارات) -->
            <div class="bg-gray-900 rounded-xl p-6 border border-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.3)] lg:col-span-2 overflow-x-auto">
                <h2 class="text-2xl font-black text-purple-400 mb-4 border-b border-gray-700 pb-2">📡 رادار العمليات النفسية (الأرشيف والجدولة)</h2>
                
                <table class="w-full text-right cyber-table border-collapse">
                    <thead>
                        <tr>
                            <th class="p-3 text-red-400 font-bold">العنوان</th>
                            <th class="p-3 text-red-400 font-bold">النوع</th>
                            <th class="p-3 text-red-400 font-bold">التوقيت/الحالة</th>
                            <th class="p-3 text-red-400 font-bold text-center">الضربات</th>
                            <th class="p-3 text-red-400 font-bold text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notif)
                            <tr class="hover:bg-gray-800 transition">
                                <td class="p-3">
                                    <div class="font-bold text-white">{{ $notif->title }}</div>
                                    <div class="text-xs text-gray-400 mt-1 truncate max-w-xs">{{ $notif->message }}</div>
                                </td>
                                <td class="p-3">
                                    @if($notif->type == 'immediate') <span class="bg-red-900/50 text-red-400 px-2 py-1 rounded text-xs">فوري</span>
                                    @elseif($notif->type == 'once') <span class="bg-blue-900/50 text-blue-400 px-2 py-1 rounded text-xs">مرة واحدة</span>
                                    @elseif($notif->type == 'daily') <span class="bg-yellow-900/50 text-yellow-400 px-2 py-1 rounded text-xs">يومي</span>
                                    @else <span class="bg-purple-900/50 text-purple-400 px-2 py-1 rounded text-xs">أسبوعي</span>
                                    @endif
                                </td>
                                <td class="p-3 text-sm">
                                    @if($notif->status == 'completed')
                                        <span class="text-green-500 font-bold">تم الانتهاء ✔️</span>
                                    @else
                                        <div class="text-blue-300" dir="ltr">{{ $notif->scheduled_at ? $notif->scheduled_at->format('Y-m-d H:i') : 'N/A' }}</div>
                                        <span class="text-yellow-500 text-xs animate-pulse">قيد الانتظار ⏳</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center text-2xl font-black text-red-500">{{ $notif->fired_count }}</td>
                                <td class="p-3 text-center space-x-2 rtl:space-x-reverse">
                                    <!-- زر الإطلاق اليدوي -->
                                    <form action="{{ route('chaos.fire', $notif->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-700 hover:bg-green-600 text-white p-2 rounded shadow transition" title="إطلاق الآن">
                                            🚀
                                        </button>
                                    </form>
                                    <!-- زر الحذف -->
                                    <form action="{{ route('chaos.delete', $notif->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من تفكيك هذه العبوة؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-800 hover:bg-red-700 text-white p-2 rounded shadow transition" title="حذف">
                                            🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500 font-bold">لا توجد أي عمليات في الرادار حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>