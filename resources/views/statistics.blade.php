<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Tajawal:wght@400;700;900&display=swap');
        
        .glass-panel {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .font-tajawal { font-family: 'Tajawal', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
        
        /* إخفاء شريط التمرير للعارض الداخلي */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- تضمين مكتبة الرسم البياني -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="min-h-screen bg-slate-950 text-white py-12 relative overflow-hidden font-tajawal" dir="rtl">
        <!-- إضاءات الخلفية -->
        <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-purple-900/30 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-cyan-900/20 rounded-full mix-blend-screen filter blur-[100px]" style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 space-y-8">
            
            <!-- الهيدر وأزرار التنقل -->
            <div class="flex justify-between items-center mb-8 px-4 sm:px-0">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-l from-purple-400 to-cyan-500">
                        مركز التحليل العصبي
                    </h2>
                    <p class="text-slate-400 mt-1 font-mono-code text-xs sm:text-sm" dir="ltr">> Analyzing performance data...</p>
                </div>
                <a href="{{ route('dashboard') }}" class="px-4 sm:px-6 py-2 border border-slate-700 rounded-full text-xs sm:text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition-all flex items-center gap-2">
                    <span class="hidden sm:inline">العودة للمفاعل</span>
                    <span class="sm:hidden">رجوع</span>
                    <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- بطاقات الـ KPI العلوية -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 sm:px-0">
                <!-- إنجاز اليوم -->
                <div class="glass-panel rounded-2xl p-6 border-b-4 border-cyan-500">
                    <h3 class="text-slate-400 text-sm mb-2">دقائق التركيز (اليوم)</h3>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black text-cyan-400 font-mono-code">{{ $todayMinutes }}</span>
                        <span class="text-slate-500 text-sm mb-1">دقيقة</span>
                    </div>
                    <!-- شريط الهدف اليومي -->
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-slate-500 mb-1 font-mono-code">
                            <span>Daily Goal: 6h</span>
                            <span>{{ number_format($progressPercentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-cyan-500 h-full shadow-[0_0_10px_rgba(56,189,248,0.8)]" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- إجمالي نقاط الخبرة -->
                <div class="glass-panel rounded-2xl p-6 border-b-4 border-purple-500">
                    <h3 class="text-slate-400 text-sm mb-2">إجمالي الطاقة (XP)</h3>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black text-purple-400 font-mono-code">{{ $totalXp }}</span>
                        <span class="text-slate-500 text-sm mb-1">XP</span>
                    </div>
                    <p class="text-xs text-purple-500/70 mt-4 font-mono-code">> Level calculation active</p>
                </div>

                <!-- معدل الأسبوع -->
                <div class="glass-panel rounded-2xl p-6 border-b-4 border-indigo-500">
                    <h3 class="text-slate-400 text-sm mb-2">إجمالي الأسبوع</h3>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black text-indigo-400 font-mono-code">{{ array_sum($minutesData) }}</span>
                        <span class="text-slate-500 text-sm mb-1">دقيقة</span>
                    </div>
                    <p class="text-xs text-indigo-500/70 mt-4 font-mono-code">> 7-Day Performance</p>
                </div>
            </div>

            <!-- الرسم البياني (The Chart) -->
            <div class="glass-panel rounded-3xl p-4 sm:p-6 mx-4 sm:mx-0 relative">
                <h3 class="text-lg font-bold text-slate-300 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    موجات التركيز (آخر 7 أيام)
                </h3>
                
                <div class="w-full h-[250px] sm:h-[300px]">
                    <canvas id="focusChart"></canvas>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- قسم التقارير اليومية (المجلدات الديناميكية) -->
            <!-- ============================================== -->
            <div class="mt-12 px-4 sm:px-0" dir="rtl" x-data="{ activeFolder: null, imgViewerOpen: false, imgSrc: '' }">
                
                <h3 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-l from-cyan-400 to-blue-500 mb-6 flex items-center gap-3">
                    <svg class="w-7 h-7 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    أرشيف الفترات الدراسية
                </h3>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-900/30 border border-green-500/50 rounded-xl text-green-400 text-sm font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                @php 
                    // تجميع التقارير حسب الـ ID الخاص بالفترة
                    $groupedEvaluations = $evaluations->groupBy('plan_interval_id'); 
                @endphp

                <div class="space-y-4">
                    @forelse($groupedEvaluations as $intervalId => $evals)
                        @php 
                            $interval = $evals->first()->interval; 
                            $safeIntervalId = $intervalId ?: 'unassigned';
                        @endphp
                        
                        <!-- المجلد (Folder Wrapper) مع تأثير التقلص -->
                        <div class="transition-all duration-500 ease-out"
                             :class="(activeFolder && activeFolder !== '{{ $safeIntervalId }}') ? 'opacity-50 scale-[0.98] grayscale-[30%]' : 'scale-100'">
                            
                            <!-- رأس المجلد (Clickable) -->
                            <div @click="activeFolder = activeFolder === '{{ $safeIntervalId }}' ? null : '{{ $safeIntervalId }}'"
                                 class="glass-panel rounded-2xl p-5 cursor-pointer border-r-4 transition-all duration-300 hover:bg-slate-800/80 flex justify-between items-center"
                                 :class="activeFolder === '{{ $safeIntervalId }}' ? 'border-cyan-500 shadow-[0_0_20px_rgba(56,189,248,0.2)] bg-slate-800/50' : 'border-slate-700'">
                                
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-colors"
                                         :class="activeFolder === '{{ $safeIntervalId }}' ? 'bg-cyan-500/20 text-cyan-400' : 'bg-slate-800 text-slate-400'">
                                        <!-- أيقونة المجلد المفتوح والمغلق ديناميكياً -->
                                        <svg x-show="activeFolder !== '{{ $safeIntervalId }}'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        <svg x-show="activeFolder === '{{ $safeIntervalId }}'" style="display:none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold" :class="activeFolder === '{{ $safeIntervalId }}' ? 'text-cyan-400' : 'text-slate-200'">
                                            {{ $interval->title ?? 'فترة سابقة (بدون عنوان)' }}
                                        </h4>
                                        <p class="text-xs text-slate-500 font-mono-code mt-1">{{ $evals->count() }} أيام منجزة</p>
                                    </div>
                                </div>
                                
                                <div class="text-slate-500 transition-transform duration-500" :class="activeFolder === '{{ $safeIntervalId }}' ? 'rotate-180 text-cyan-400' : ''">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- محتوى المجلد (ينسدل لأسفل) -->
                            <div x-show="activeFolder === '{{ $safeIntervalId }}'" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                                 class="pt-4 pb-2" style="display: none;">
                                 
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($evals as $eval)
                                        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 relative group hover:border-slate-600 transition-colors">
                                            
                                            <!-- الرأس: رقم اليوم والتاريخ -->
                                            <div class="flex justify-between items-start mb-4 border-b border-slate-800 pb-3">
                                                <div>
                                                    <h5 class="font-bold text-white flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full {{ $eval->created_at->isToday() ? 'bg-cyan-500 shadow-[0_0_8px_#06b6d4]' : 'bg-slate-500' }}"></span>
                                                        تقرير اليوم رقم {{ $eval->day_number }}
                                                    </h5>
                                                    <p class="text-[10px] text-slate-500 font-mono-code mt-1">{{ $eval->created_at->format('Y-m-d') }}</p>
                                                </div>
                                                
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-base {{ $eval->final_score >= 7 ? 'bg-green-900/40 text-green-400 border border-green-500' : ($eval->final_score >= 5 ? 'bg-orange-900/40 text-orange-400 border border-orange-500' : 'bg-red-900/40 text-red-400 border border-red-500') }}">
                                                    {{ round($eval->final_score, 1) }}
                                                </div>
                                            </div>

                                            <!-- التقييمات المصغرة -->
                                            <div class="grid grid-cols-3 gap-2 mb-4 text-[10px] font-mono-code text-center">
                                                <div class="bg-slate-950 p-2 rounded-lg border border-slate-800/50">تركيز<br><span class="text-cyan-400 text-sm">{{ $eval->focus_score }}</span></div>
                                                <div class="bg-slate-950 p-2 rounded-lg border border-slate-800/50">تمارين<br><span class="text-purple-400 text-sm">{{ $eval->exercises_score }}</span></div>
                                                <div class="bg-slate-950 p-2 rounded-lg border border-slate-800/50">تشتت<br><span class="text-red-400 text-sm">{{ $eval->mental_fatigue }}</span></div>
                                            </div>

                                            <!-- الملاحظات -->
                                            @if($eval->notes)
                                                <div class="mb-4 bg-slate-950/50 p-3 rounded-lg border-r-2 border-slate-500">
                                                    <p class="text-xs text-slate-300 leading-relaxed">{{ $eval->notes }}</p>
                                                </div>
                                            @endif

                                            <!-- زر عرض الصورة المدمج (يفتح الـ Modal الداخلي) -->
                                            <div class="flex justify-between items-center mt-4 pt-3 border-t border-slate-800/50">
                                                @if($eval->image_path)
                                                    <button type="button" @click.prevent="imgSrc = '{{ asset('storage/' . $eval->image_path) }}'; imgViewerOpen = true" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1 transition-colors px-2 py-1 bg-cyan-900/20 rounded-md">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        عرض المرفق
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-slate-600 font-mono-code">No Attachment</span>
                                                @endif

                                                <!-- زر تعديل التقرير (يوم حالي فقط) -->
                                                @if($eval->created_at->isToday())
                                                    <button onclick="document.getElementById('edit-eval-modal-{{ $eval->id }}').classList.remove('hidden')" class="text-[10px] px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded font-bold transition-colors">
                                                        تعديل التقرير
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-slate-600 font-mono-code bg-slate-900 px-2 py-1 rounded">LOCKED</span>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-slate-900/30 rounded-3xl border border-slate-800 border-dashed">
                            <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            <p class="text-slate-500 text-sm">أرشيف الفترات فارغ حالياً.</p>
                        </div>
                    @endforelse
                </div>

                <!-- ============================================== -->
                <!-- عارض الصور الداخلي الشفاف (Image Viewer Modal) -->
                <!-- ============================================== -->
                <div x-show="imgViewerOpen" style="display: none;"
                     class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/95 backdrop-blur-md p-4">
                     
                    <!-- خلفية قابلة للنقر للإغلاق -->
                    <div class="absolute inset-0 cursor-pointer" @click="imgViewerOpen = false"></div>
                    
                    <!-- زر الإغلاق -->
                    <button @click="imgViewerOpen = false" class="absolute top-6 right-6 z-10 p-2 bg-slate-800 text-slate-300 hover:text-white rounded-full transition-colors shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <!-- الصورة -->
                    <img :src="imgSrc" class="relative z-10 max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl border border-slate-800" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-90"
                         x-transition:enter-end="opacity-100 transform scale-100">
                </div>

                <!-- ============================================== -->
                <!-- نوافذ التعديل (مفصولة خارج الأقسام لتجنب تشوه CSS) -->
                <!-- ============================================== -->
                @foreach($evaluations as $eval)
                    @if($eval->created_at->isToday())
                        <div id="edit-eval-modal-{{ $eval->id }}" class="hidden fixed inset-0 z-[150] bg-slate-950/80 backdrop-blur-sm overflow-y-auto no-scrollbar text-right" dir="rtl">
                            <div class="flex min-h-full items-start justify-center p-2 sm:p-4">
                                <div class="bg-slate-900 border border-cyan-500/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 w-full max-w-lg shadow-[0_0_50px_rgba(56,189,248,0.1)] my-4 sm:my-12 relative">
                                    <div class="flex justify-between items-center mb-5 sm:mb-6 border-b border-slate-800 pb-3 sm:pb-4 sticky top-0 bg-slate-900 z-10">
                                        <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            تعديل تقرير اليوم
                                        </h3>
                                        <button onclick="document.getElementById('edit-eval-modal-{{ $eval->id }}').classList.add('hidden')" type="button" class="text-slate-400 hover:text-red-400 bg-slate-800 p-1.5 rounded-full">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>

                                    <form action="{{ route('evaluations.update', $eval->id) }}" method="POST" id="edit-eval-form-{{ $eval->id }}" class="space-y-5 pb-2">
                                        @csrf
                                        @method('PUT')
                                        
                                        <input type="hidden" name="compressed_image" id="edit-compressed-image-{{ $eval->id }}">

                                        <div class="space-y-5" x-data="{ 
                                                focus: {{ $eval->focus_score }}, 
                                                exercises: {{ $eval->exercises_score }}, 
                                                fatigue: {{ $eval->mental_fatigue }} 
                                            }">
                                            <div>
                                                <div class="flex justify-between items-center text-xs sm:text-sm text-slate-300 mb-2">
                                                    <span>مستوى التركيز العام</span> 
                                                    <span class="font-mono-code text-cyan-400 font-bold bg-cyan-900/30 px-2 py-1 rounded" x-text="focus + '/10'"></span>
                                                </div>
                                                <input type="range" name="focus_score" x-model="focus" min="1" max="10" class="w-full accent-cyan-500">
                                            </div>
                                            <div>
                                                <div class="flex justify-between items-center text-xs sm:text-sm text-slate-300 mb-2">
                                                    <span>جودة حل التمارين</span> 
                                                    <span class="font-mono-code text-purple-400 font-bold bg-purple-900/30 px-2 py-1 rounded" x-text="exercises + '/10'"></span>
                                                </div>
                                                <input type="range" name="exercises_score" x-model="exercises" min="1" max="10" class="w-full accent-purple-500">
                                            </div>
                                            <div>
                                                <div class="flex justify-between items-center text-xs sm:text-sm text-slate-300 mb-2">
                                                    <span>الإرهاق الذهني (Burnout)</span> 
                                                    <span class="font-mono-code text-red-400 font-bold bg-red-900/30 px-2 py-1 rounded" x-text="fatigue + '/10'"></span>
                                                </div>
                                                <input type="range" name="mental_fatigue" x-model="fatigue" min="1" max="10" class="w-full accent-red-500">
                                            </div>
                                        </div>

                                        <div class="p-3 sm:p-4 bg-slate-950/50 border border-slate-800 rounded-2xl space-y-4">
                                            <h4 class="text-xs text-slate-400 font-bold mb-3 border-b border-slate-800 pb-2">المصادر المستخدمة اليوم:</h4>
                                            
                                            <div x-data="{ used: {{ $eval->youtube_score !== null ? 'true' : 'false' }}, score: {{ $eval->youtube_score ?? 5 }} }">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" name="used_youtube" x-model="used" class="rounded border-slate-700 bg-slate-900 text-red-500 focus:ring-red-500 w-4 h-4">
                                                    <label class="text-xs sm:text-sm text-slate-300">يوتيوب (فيديوهات شرح)</label>
                                                </div>
                                                <div x-show="used" class="mt-3 pl-2 sm:pl-7" style="display: none;">
                                                    <div class="flex justify-between text-[10px] sm:text-xs text-slate-400 mb-1"><span>التقييم</span><span x-text="score+'/10'"></span></div>
                                                    <input type="range" name="youtube_score" x-model="score" min="1" max="10" class="w-full accent-red-500 h-1.5">
                                                </div>
                                            </div>

                                            <div x-data="{ used: {{ $eval->notebook_score !== null ? 'true' : 'false' }}, score: {{ $eval->notebook_score ?? 5 }} }">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" name="used_notebook" x-model="used" class="rounded border-slate-700 bg-slate-900 text-green-500 focus:ring-green-500 w-4 h-4">
                                                    <label class="text-xs sm:text-sm text-slate-300">الدفتر الخاص</label>
                                                </div>
                                                <div x-show="used" class="mt-3 pl-2 sm:pl-7" style="display: none;">
                                                    <div class="flex justify-between text-[10px] sm:text-xs text-slate-400 mb-1"><span>التقييم</span><span x-text="score+'/10'"></span></div>
                                                    <input type="range" name="notebook_score" x-model="score" min="1" max="10" class="w-full accent-green-500 h-1.5">
                                                </div>
                                            </div>

                                            <div x-data="{ used: {{ $eval->book_score !== null ? 'true' : 'false' }}, score: {{ $eval->book_score ?? 5 }} }">
                                                <div class="flex items-center gap-3">
                                                    <input type="checkbox" name="used_book" x-model="used" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 w-4 h-4">
                                                    <label class="text-xs sm:text-sm text-slate-300">الكتاب الرسمي</label>
                                                </div>
                                                <div x-show="used" class="mt-3 pl-2 sm:pl-7" style="display: none;">
                                                    <div class="flex justify-between text-[10px] sm:text-xs text-slate-400 mb-1"><span>التقييم</span><span x-text="score+'/10'"></span></div>
                                                    <input type="range" name="book_score" x-model="score" min="1" max="10" class="w-full accent-blue-500 h-1.5">
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <textarea name="notes" rows="2" placeholder="ملاحظات اليوم..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 sm:px-4 py-3 text-white focus:ring-cyan-500 focus:border-cyan-500 text-xs sm:text-sm resize-none">{{ $eval->notes }}</textarea>
                                        </div>

                                        <div class="relative">
                                            <input type="file" id="edit-image-upload-{{ $eval->id }}" accept="image/*" class="hidden" onchange="compressEditImage(event, {{ $eval->id }})">
                                            <label for="edit-image-upload-{{ $eval->id }}" class="flex items-center justify-center w-full p-3 sm:p-4 border-2 border-dashed border-slate-700 rounded-xl cursor-pointer hover:border-cyan-500 hover:bg-slate-800 transition-colors">
                                                <div class="flex flex-col items-center text-slate-400 text-center" id="edit-upload-text-{{ $eval->id }}">
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                    <span class="text-[10px] sm:text-xs font-bold">تحديث الصورة (اختياري)</span>
                                                </div>
                                            </label>
                                        </div>

                                        <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-400 text-slate-900 font-black text-xs sm:text-sm py-3.5 rounded-xl tracking-wider transition-all transform hover:-translate-y-1 mt-2">
                                            حفظ التعديلات
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
            <!-- نهاية قسم المجلدات والتقارير -->

        </div>
    </div>

    <!-- كود تشغيل الرسم البياني -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('focusChart').getContext('2d');
            const labels = @json($days);
            const dataPoints = @json($minutesData);

            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(56, 189, 248, 0.5)'); 
            gradient.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'دقائق التركيز',
                        data: dataPoints,
                        borderColor: '#38bdf8', 
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#1e1b4b',
                        pointBorderColor: '#38bdf8',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { family: 'JetBrains Mono', size: 14 },
                            bodyFont: { family: 'JetBrains Mono', size: 14 },
                            padding: 12,
                            borderColor: '#334155',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', font: { family: 'JetBrains Mono' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { family: 'JetBrains Mono' } }
                        }
                    }
                }
            });
        });
    </script>

    <!-- كود ضغط الصورة للنافذة -->
    <script>
        function compressEditImage(event, evalId) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById(`edit-upload-text-${evalId}`).innerHTML = `<span class="text-cyan-400 font-bold text-xs">تم إرفاق: ${file.name} (جاري الضغط...)</span>`;

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e) {
                const img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const MAX_WIDTH = 800;
                    let width = img.width;
                    let height = img.height;

                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    const compressedBase64 = canvas.toDataURL('image/webp', 0.6);
                    document.getElementById(`edit-compressed-image-${evalId}`).value = compressedBase64;
                    document.getElementById(`edit-upload-text-${evalId}`).innerHTML = `<span class="text-green-400 font-bold text-xs">تم الضغط والتجهيز! ✔️</span>`;
                }
            };
        }
    </script>
</x-app-layout>