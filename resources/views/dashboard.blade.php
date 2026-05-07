<x-app-layout>
    <!-- تضمين خطوط ومؤثرات برمجية سريعة -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Tajawal:wght@400;700;900&display=swap');
        
        .glass-panel {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .neon-glow {
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
        }
        .font-tajawal { font-family: 'Tajawal', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
    </style>

    <!-- الخلفية الفضائية المظلمة -->
    <div class="min-h-screen bg-slate-950 text-white py-12 relative overflow-hidden font-tajawal" dir="rtl">
        
        <!-- إضاءات خلفية ضبابية (Blobs) -->
        <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-indigo-900/40 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-cyan-900/30 rounded-full mix-blend-screen filter blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 space-y-8">
            
            <!-- الهيدر (شاشة الترحيب) -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 bg-slate-900/50 p-6 rounded-2xl border border-slate-800">
                <div class="text-right">
                    <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-l from-cyan-400 via-blue-500 to-indigo-500 tracking-wide">
                        مرآة الفوضى | CHAOS MIRROR
                    </h2>
                    <p class="text-slate-400 mt-2 font-mono-code text-sm" dir="ltr">> System Online. Welcome, Reem.</p>
                </div>
                <div class="mt-4 md:mt-0 text-left md:text-right flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-xs text-slate-500 uppercase tracking-widest font-mono-code">Rank</div>
                        <div class="text-xl font-bold text-cyan-400 drop-shadow-[0_0_8px_rgba(56,189,248,0.8)]">محاربة البكالوريا</div>
                    </div>
                    <div class="w-12 h-12 rounded-full border-2 border-cyan-500 flex items-center justify-center bg-slate-800 shadow-[0_0_15px_rgba(56,189,248,0.3)]">
                        <span class="text-cyan-400 font-bold">R</span>
                    </div>
                </div>
            </div>

            <!-- شبكة الأقسام -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
            <!-- القسم المركزي: المفاعل (مؤقت التركيز) -->
            <div x-data="{ 
                    timer: null,
                    timeLeft: 25 * 60, // الافتراضي 25 دقيقة
                    isRunning: false,
                    sessionDuration: 25,
                    endTime: null,
                    
                    get formattedTime() {
                        let minutes = Math.floor(this.timeLeft / 60);
                        let seconds = this.timeLeft % 60;
                        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    },
                    
                    // دالة التهيئة: تعمل فور تحميل الصفحة للبحث عن أي مفاعل يعمل بالخلفية
                    init() {
                        let running = localStorage.getItem('chaos_running');
                        if (running === 'true') {
                            this.endTime = parseInt(localStorage.getItem('chaos_endTime'));
                            this.sessionDuration = parseInt(localStorage.getItem('chaos_duration'));
                            this.isRunning = true;
                            
                            // حساب الوقت المتبقي بناءً على الوقت الحقيقي الآن
                            this.updateTimeLeft();
                            
                            if (this.timeLeft > 0) {
                                this.resumeTimer(); // استكمال الدوران
                            } else {
                                this.stopTimer(true); // انتهى الوقت أثناء غياب المستخدم!
                            }
                        } else {
                            this.timeLeft = this.sessionDuration * 60;
                        }
                    },

                    updateTimeLeft() {
                        let now = Date.now();
                        this.timeLeft = Math.max(0, Math.floor((this.endTime - now) / 1000));
                    },
                    
                    startTimer() {
                        if (this.isRunning) return;
                        this.isRunning = true;
                        
                        // تحديد نقطة النهاية الدقيقة في المستقبل
                        this.endTime = Date.now() + (this.sessionDuration * 60 * 1000);
                        
                        // حفظ الحالة في المتصفح لتعيش بعد التحديث
                        localStorage.setItem('chaos_running', 'true');
                        localStorage.setItem('chaos_endTime', this.endTime);
                        localStorage.setItem('chaos_duration', this.sessionDuration);
                        
                        this.resumeTimer();
                    },

                    resumeTimer() {
                        this.timer = setInterval(() => {
                            this.updateTimeLeft();
                            if (this.timeLeft <= 0) {
                                this.stopTimer(true); // انتهى الوقت بنجاح
                            }
                        }, 1000);
                    },
                    
                    stopTimer(completed = false) {
                        clearInterval(this.timer);
                        this.isRunning = false;
                        
                        if (completed || confirm('هل أنتِ متأكدة من إيقاف المزامنة قبل انتهاء الوقت؟')) {
                            
                            // حساب الدقائق الفعلية التي قضاها
                            let actualDuration = completed ? this.sessionDuration : Math.floor((this.sessionDuration * 60 - this.timeLeft) / 60);
                            
                            if (actualDuration > 0) {
                                fetch('{{ url('/focus-sessions') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ 
                                        duration_minutes: actualDuration,
                                        is_fully_completed: completed 
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.status === 'success') {
                                        let xpElement = document.getElementById('xp-counter');
                                        let progressBar = document.getElementById('xp-progress-bar');
                                        xpElement.innerText = data.new_xp;
                                        progressBar.style.width = Math.min((data.new_xp / 1000) * 100, 100) + '%';
                                        
                                        // مؤثر بصري للـ XP
                                        xpElement.classList.add('scale-125', 'text-cyan-400');
                                        setTimeout(() => xpElement.classList.remove('scale-125', 'text-cyan-400'), 500);
                                        
                                        alert(data.message);
                                    }
                                });
                            }
                            
                            // تنظيف الذاكرة وتصفير المفاعل
                            localStorage.removeItem('chaos_running');
                            localStorage.removeItem('chaos_endTime');
                            localStorage.removeItem('chaos_duration');
                            this.timeLeft = this.sessionDuration * 60;
                            
                        } else {
                            this.isRunning = true;
                            this.resumeTimer(); // استكمال إذا تراجعت عن الإيقاف
                        }
                    }
                }" 
                class="lg:col-span-2 glass-panel rounded-3xl p-8 relative overflow-hidden group transition-all duration-500 hover:border-cyan-500/30">
                
                <div class="absolute top-0 right-0 w-full h-1 bg-gradient-to-l from-cyan-500 to-indigo-600"></div>
                
                <h3 class="text-2xl font-bold text-slate-200 mb-8 flex items-center gap-3">
                    <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    مفاعل التركيز العميق
                </h3>
                
                <div class="flex flex-col items-center justify-center py-6">
                    <div class="w-56 h-56 rounded-full border-[6px] border-slate-800 flex items-center justify-center relative shadow-[0_0_40px_rgba(0,0,0,0.8)] transition-all duration-300"
                        :class="isRunning ? 'scale-105 shadow-cyan-500/50' : ''">
                        <div class="absolute inset-0 rounded-full border-[6px] border-cyan-500 border-t-transparent border-l-transparent"
                            :class="isRunning ? 'animate-spin' : ''" style="animation-duration: 3s;"></div>
                        <div class="absolute inset-2 rounded-full border-[4px] border-indigo-500/50 border-b-transparent border-r-transparent"
                            :class="isRunning ? 'animate-spin' : ''" style="animation-duration: 2s; animation-direction: reverse;"></div>
                        
                        <span x-text="formattedTime" class="text-6xl font-mono-code text-white drop-shadow-[0_0_10px_rgba(255,255,255,0.5)] tracking-tighter" dir="ltr"></span>
                    </div>
                    
                    <button @click="isRunning ? stopTimer() : startTimer()" 
                            :class="isRunning ? 'from-red-500 to-pink-600 shadow-red-500/30 hover:from-red-400 hover:to-pink-500' : 'from-cyan-500 to-blue-600 shadow-cyan-500/30 hover:from-cyan-400 hover:to-blue-500'"
                            class="mt-12 bg-gradient-to-l text-white font-black text-lg py-4 px-16 rounded-full tracking-wider transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-lg">
                        <span x-text="isRunning ? 'إيقاف المزامنة (Stop Flow)' : 'بدء المزامنة العصبية (Start Flow)'"></span>
                    </button>
                </div>
            </div>

                <!-- الشريط الجانبي -->
                <div class="space-y-8">
                    
                    <!-- نواة الطاقة (النقاط والمكافآت) -->
                    <div class="glass-panel rounded-3xl p-6 border-r-4 border-purple-500 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl"></div>
                        @php 
                            // نجلب النقاط الحالية مرة واحدة لنستخدمها في الرقم والشريط
                            $currentXp = \App\Models\Achievement::where('user_id', auth()->id())->value('points') ?? 0; 
                        @endphp

                        <!-- نواة الطاقة (النقاط والمكافآت) -->
                        <div class="glass-panel rounded-3xl p-6 border-r-4 border-purple-500 relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl"></div>
                            <h3 class="text-lg font-bold text-slate-300 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                نواة الإنجاز
                            </h3>
                            <div class="flex justify-between items-end mb-3">
                                <!-- أضفنا inline-block و transition و transform للانسيابية -->
                                <span id="xp-counter" class="inline-block transform transition-all duration-500 ease-in-out text-5xl font-black text-transparent bg-clip-text bg-gradient-to-b from-purple-400 to-purple-600">
                                    {{ $currentXp }}
                                </span>
                                <span class="text-sm text-purple-400 font-mono-code mb-1">XP</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-3 mt-4 border border-slate-700 overflow-hidden">
                                <!-- جعلنا العرض ديناميكي مرتبط بالنقاط، وأضفنا transition-all duration-1000 -->
                                <div id="xp-progress-bar" class="bg-gradient-to-l from-purple-400 to-indigo-500 h-full rounded-full relative transition-all duration-1000 ease-out" 
                                    style="width: {{ min(($currentXp / 1000) * 100, 100) }}%">
                                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-white/50 rounded-full animate-pulse"></div>
                                </div>
                            </div>

                            <div class="mt-6 glass-panel p-4 rounded-2xl border-l-4 border-cyan-500">
                                <div class="flex justify-between text-xs text-slate-400 mb-2 font-mono-code">
                                    <span>Daily Goal Status</span>
                                    <span id="daily-progress-text">0/6 Hours</span>
                                </div>
                                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div id="daily-goal-bar" class="bg-cyan-500 h-full transition-all duration-1000" style="width: 0%"></div>
                                </div>
                            </div>

                            <p class="text-xs text-slate-400 mt-3 font-mono-code" dir="ltr">Unlock level 2 at 1000 XP</p>

                            <!-- الزر الجديد: رابط مركز التحليل (مصمم خصيصاً للموبايل/الويب فيو) -->
                            <a href="{{ route('statistics') }}" class="mt-6 w-full py-3.5 bg-purple-900/30 hover:bg-purple-800/50 border border-purple-500/30 rounded-xl flex items-center justify-center gap-2 text-purple-300 hover:text-white transition-all duration-300 font-bold text-sm shadow-[0_0_15px_rgba(168,85,247,0.1)] group">
                                <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                فتح مركز التحليل العصبي
                            </a>
                        </div>

                    <!-- الروابط العصبية (المصادر والدراسة) -->
                    <!-- مصفوفة المهام النشطة (الخطة الدراسية) -->
                    <div class="glass-panel rounded-3xl p-6 relative">
                        <!-- مصفوفة المهام النشطة (الخطة الدراسية) بنظام السلايدر -->
                    <div class="glass-panel rounded-3xl p-6 relative">
                        <h3 class="text-lg font-bold text-slate-300 mb-5 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            مصفوفة المهام الحالية
                        </h3>
                        
                        @if($activePlan)
                            <!-- تعريف السلايدر في Alpine.js -->
                            <div x-data="{ 
                                    currentInterval: 0, 
                                    totalIntervals: {{ $activePlan->intervals->count() }},
                                    next() { if(this.currentInterval < this.totalIntervals - 1) this.currentInterval++; },
                                    prev() { if(this.currentInterval > 0) this.currentInterval--; }
                                }">
                                
                                <!-- عنوان الخطة وأزرار التنقل -->
                                <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-700/50">
                                    <h4 class="text-cyan-400 font-bold text-sm">> {{ $activePlan->title }}</h4>
                                    
                                    <!-- أزرار التنقل (تظهر فقط إذا كان هناك أكثر من فترة) -->
                                    <div class="flex items-center gap-3" x-show="totalIntervals > 1" style="display: none;">
                                        <button @click="prev()" :disabled="currentInterval === 0" :class="currentInterval === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-800 text-cyan-400'" class="p-1.5 rounded-full transition-all border border-slate-700/50 focus:outline-none">
                                            <!-- سهم يمين (للسابق) -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </button>
                                        
                                        <!-- عداد الصفحات -->
                                        <span class="text-xs font-mono-code text-slate-400 font-bold">
                                            <span x-text="currentInterval + 1"></span> / <span x-text="totalIntervals"></span>
                                        </span>
                                        
                                        <button @click="next()" :disabled="currentInterval === totalIntervals - 1" :class="currentInterval === totalIntervals - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-800 text-cyan-400'" class="p-1.5 rounded-full transition-all border border-slate-700/50 focus:outline-none">
                                            <!-- سهم يسار (للتالي) -->
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- عرض الفترات بنظام الصفحات (السلايدر) -->
                                <div class="mt-4 relative min-h-[150px]">
                                    @foreach($activePlan->intervals as $index => $interval)
                                        <!-- كل فترة لها شرط الظهور الخاص بها مع أنيميشن انسيابي -->
                                        <div x-show="currentInterval === {{ $index }}" 
                                            x-transition:enter="transition ease-out duration-300" 
                                            x-transition:enter-start="opacity-0 transform translate-x-4" 
                                            x-transition:enter-end="opacity-100 transform translate-x-0" 
                                            style="display: {{ $index === 0 ? 'block' : 'none' }};"
                                            class="absolute top-0 left-0 w-full">
                                            
                                            <p class="text-xs text-slate-400 font-mono-code mb-4">
                                                [ {{ $interval->title }} - {{ $interval->duration_days }} Days ]
                                            </p>
                                            
                                            <ul class="space-y-3">
                                                @foreach($interval->tasks as $task)
                                                    <li x-data="{ completed: {{ $task->is_completed ? 'true' : 'false' }} }" 
                                                        class="p-4 bg-slate-900/80 rounded-xl border border-slate-800 hover:border-cyan-500/50 transition-all duration-300 flex justify-between items-center group">
                                                        
                                                        <span class="text-sm font-bold transition-all duration-300" 
                                                            :class="completed ? 'text-slate-500 line-through decoration-cyan-500/50' : 'text-slate-300 group-hover:text-white'">
                                                            {{ $task->task_name }}
                                                        </span>
                                                        
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" class="sr-only peer" x-model="completed" 
                                                                @change="
                                                                    fetch('{{ url('/tasks/' . $task->id . '/toggle') }}', {
                                                                        method: 'POST',
                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                            'Accept': 'application/json',
                                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                        }
                                                                    })
                                                                    .then(res => res.json())
                                                                    .then(data => {
                                                                        if(data.status === 'success') {
                                                                            let xpElement = document.getElementById('xp-counter');
                                                                            let progressBar = document.getElementById('xp-progress-bar');
                                                                            xpElement.innerText = data.new_xp;
                                                                            progressBar.style.width = Math.min((data.new_xp / 1000) * 100, 100) + '%';
                                                                            xpElement.classList.add('scale-110', 'text-cyan-400');
                                                                            setTimeout(() => xpElement.classList.remove('scale-110', 'text-cyan-400'), 400);
                                                                        }
                                                                    })
                                                                ">
                                                            <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-500 shadow-[0_0_10px_rgba(56,189,248,0)] peer-checked:shadow-[0_0_10px_rgba(56,189,248,0.5)]"></div>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <!-- زر التقييم اليومي -->
<div class="mt-6 pt-4 border-t border-slate-700/50 flex justify-between items-center">
    <div class="text-xs text-slate-400 font-mono-code">
        التقييمات المنجزة: <span class="text-cyan-400">{{ $interval->evaluations()->count() ?? 0 }}/{{ $interval->duration_days }}</span>
    </div>
    <button onclick="document.getElementById('eval-modal-{{ $interval->id }}').classList.remove('hidden')" class="py-2 px-4 bg-gradient-to-l from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold rounded-lg transition-all shadow-[0_0_10px_rgba(56,189,248,0.3)]">
        + إنهاء وتقييم اليوم
    </button>
</div>

<!-- Modal التقييم (مخفي افتراضياً) -->
<div id="eval-modal-{{ $interval->id }}" class="hidden fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
    <div class="flex min-h-full items-start justify-center p-2 sm:p-4">
        <div class="bg-slate-900 border border-cyan-500/30 rounded-2xl sm:rounded-3xl p-4 sm:p-6 w-full max-w-lg shadow-[0_0_50px_rgba(56,189,248,0.1)] my-8 sm:my-12 relative text-right" dir="rtl">
            <div class="flex justify-between items-center mb-5 sm:mb-6 border-b border-slate-800 pb-3 sm:pb-4 sticky top-0 bg-slate-900 z-10">
                <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    تقييم الأداء اليومي
                </h3>
                <button onclick="document.getElementById('eval-modal-{{ $interval->id }}').classList.add('hidden')" type="button" class="text-slate-400 hover:text-red-400 bg-slate-800 p-1.5 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('evaluations.store', $interval->id) }}" method="POST" id="eval-form-{{ $interval->id }}" class="space-y-5 pb-2">
                @csrf
                
                <!-- حقل مخفي للصورة المضغوطة -->
                <input type="hidden" name="compressed_image" id="compressed-image-{{ $interval->id }}">

                <!-- 1. التقييمات الأساسية (Sliders) -->
                <div class="space-y-5">
                    <div x-data="{ score: 5 }">
                        <div class="flex justify-between items-center text-xs sm:text-sm text-slate-300 mb-2">
                            <span>مستوى التركيز العام</span> 
                            <span class="font-mono-code text-cyan-400 font-bold bg-cyan-900/30 px-2 py-1 rounded" x-text="score + '/10'"></span>
                        </div>
                        <input type="range" name="focus_score" x-model="score" min="1" max="10" class="w-full accent-cyan-500">
                    </div>
                    <div x-data="{ score: 5 }">
                        <div class="flex justify-between items-center text-xs sm:text-sm text-slate-300 mb-2">
                            <span>جودة حل التمارين</span> 
                            <span class="font-mono-code text-purple-400 font-bold bg-purple-900/30 px-2 py-1 rounded" x-text="score + '/10'"></span>
                        </div>
                        <input type="range" name="exercises_score" x-model="score" min="1" max="10" class="w-full accent-purple-500">
                    </div>
                    <div x-data="{ score: 1 }">
                        <div class="flex justify-between items-center text-xs sm:text-sm text-slate-300 mb-2">
                            <span>الإرهاق الذهني والتشتت</span> 
                            <span class="font-mono-code text-red-400 font-bold bg-red-900/30 px-2 py-1 rounded" x-text="score + '/10'"></span>
                        </div>
                        <input type="range" name="mental_fatigue" x-model="score" min="1" max="10" class="w-full accent-red-500">
                    </div>
                </div>

                <!-- 2. المصادر الديناميكية -->
                <div class="p-3 sm:p-4 bg-slate-950/50 border border-slate-800 rounded-2xl space-y-4">
                    <h4 class="text-xs text-slate-400 font-bold mb-3 border-b border-slate-800 pb-2">المصادر المستخدمة اليوم:</h4>
                    
                    <!-- يوتيوب -->
                    <div x-data="{ used: false, score: 5 }">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="used_youtube" x-model="used" class="rounded border-slate-700 bg-slate-900 text-red-500 focus:ring-red-500 w-4 h-4">
                            <label class="text-xs sm:text-sm text-slate-300">يوتيوب (فيديوهات شرح)</label>
                        </div>
                        <div x-show="used" class="mt-3 pl-2 sm:pl-7" style="display: none;">
                            <div class="flex justify-between text-[10px] sm:text-xs text-slate-400 mb-1"><span>التقييم</span><span x-text="score+'/10'"></span></div>
                            <input type="range" name="youtube_score" x-model="score" min="1" max="10" class="w-full accent-red-500 h-1.5">
                        </div>
                    </div>

                    <!-- دفتر الأخت -->
                    <div x-data="{ used: false, score: 5 }">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="used_notebook" x-model="used" class="rounded border-slate-700 bg-slate-900 text-green-500 focus:ring-green-500 w-4 h-4">
                            <label class="text-xs sm:text-sm text-slate-300">الدفتر الخاص</label>
                        </div>
                        <div x-show="used" class="mt-3 pl-2 sm:pl-7" style="display: none;">
                            <div class="flex justify-between text-[10px] sm:text-xs text-slate-400 mb-1"><span>التقييم</span><span x-text="score+'/10'"></span></div>
                            <input type="range" name="notebook_score" x-model="score" min="1" max="10" class="w-full accent-green-500 h-1.5">
                        </div>
                    </div>

                    <!-- الكتاب الرسمي -->
                    <div x-data="{ used: false, score: 5 }">
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

                <!-- 3. الملاحظات والصورة (الضغط الذكي) -->
                <div>
                    <textarea name="notes" rows="2" placeholder="ملاحظات اليوم (قوانين صعبة، أفكار للغد...)" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 sm:px-4 py-3 text-white focus:ring-cyan-500 focus:border-cyan-500 text-xs sm:text-sm resize-none"></textarea>
                </div>

                <div class="relative">
                    <input type="file" id="image-upload-{{ $interval->id }}" accept="image/*" class="hidden" 
                           onchange="compressImage(event, {{ $interval->id }})">
                    <label for="image-upload-{{ $interval->id }}" class="flex items-center justify-center w-full p-3 sm:p-4 border-2 border-dashed border-slate-700 rounded-xl cursor-pointer hover:border-cyan-500 hover:bg-slate-800 transition-colors">
                        <div class="flex flex-col items-center text-slate-400 text-center" id="upload-text-{{ $interval->id }}">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            <span class="text-[10px] sm:text-xs font-bold">إرفاق صورة لمسألة اليوم (سيتم ضغطها)</span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="w-full bg-cyan-500 hover:bg-cyan-400 text-slate-900 font-black text-xs sm:text-sm py-3.5 rounded-xl tracking-wider transition-all transform hover:-translate-y-1 mt-2">
                    تأكيد وتشفير البيانات
                </button>
            </form>
        </div>
    </div>
</div>

                                                    
                                            <!-- نهاية قسم التقييم -->
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- أضفنا مسافة فارغة تحت المهام لكي لا يغطيها الزر لأننا استخدمنا absolute -->
                                <div class="pt-24 pb-4"></div>
                            </div>

                            <a href="{{ route('plans.edit', $activePlan->id) }}" class="mt-4 w-full py-3 border border-slate-700 border-dashed rounded-xl flex items-center justify-center gap-2 text-slate-400 hover:text-purple-400 hover:border-purple-500/50 hover:bg-purple-500/10 transition-all duration-300 font-mono-code text-sm group">
                                <span>Edit & Add Data</span>
                                <svg class="w-4 h-4 transform group-hover:rotate-90 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                        @else
                            <div class="text-center py-6">
                                <p class="text-sm text-slate-500 mb-4">لا توجد مصفوفة نشطة حالياً.</p>
                                <a href="{{ route('plans.create') }}" class="inline-block py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-lg transition-all shadow-lg shadow-indigo-500/30">
                                    + بناء خطة جديدة
                                </a>
                            </div>
                        @endif
                    </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<script>
    // خوارزمية ضغط الصور في المتصفح للحفاظ على مساحة الـ 1 جيجا
    function compressImage(event, intervalId) {
        const file = event.target.files[0];
        if (!file) return;

        // تغيير النص ليُظهر أنه تم الاختيار
        document.getElementById(`upload-text-${intervalId}`).innerHTML = `<span class="text-cyan-400 font-bold text-xs">تم إرفاق: ${file.name} (جاري الضغط...)</span>`;

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // تصغير الأبعاد بنسبة كبيرة (أقصى عرض 800 بكسل كافي جداً للموبايل)
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

                // ضغط الصورة بجودة 60% وتحويلها لـ WEBP (أخف صيغة)
                const compressedBase64 = canvas.toDataURL('image/webp', 0.6);
                
                // وضع النتيجة في الحقل المخفي ليرسلها لـ لارافيل
                document.getElementById(`compressed-image-${intervalId}`).value = compressedBase64;
                
                document.getElementById(`upload-text-${intervalId}`).innerHTML = `<span class="text-green-400 font-bold text-xs">تم الضغط والتجهيز بنجاح! ✔️</span>`;
            }
        };
    }
</script>
</x-app-layout>