<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Tajawal:wght@400;700;900&display=swap');
        .font-tajawal { font-family: 'Tajawal', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>

    <div class="min-h-screen bg-slate-950 text-white py-12 relative overflow-hidden font-tajawal" dir="rtl">
        <!-- إضاءات الخلفية -->
        <div class="absolute top-0 right-[-10%] w-[600px] h-[600px] bg-cyan-900/20 rounded-full mix-blend-screen filter blur-[120px]"></div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 relative z-10">
            
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-l from-cyan-400 to-blue-500">
                        بناء مصفوفة عصبية جديدة
                    </h2>
                    <p class="text-slate-400 mt-2 font-mono-code text-sm" dir="ltr">> Initialize new data structures...</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition-colors text-sm border-b border-slate-700 hover:border-white pb-1">العودة للداشبورد</a>
            </div>

            <!-- الفورم المدمج مع Alpine.js للتحكم الديناميكي -->
            <form action="{{ route('plans.store') }}" method="POST" 
                  x-data="{
                      intervals: [
                          { id: Date.now(), tasks: [''] }
                      ],
                      addInterval() {
                          this.intervals.push({ id: Date.now(), tasks: [''] });
                      },
                      removeInterval(index) {
                          this.intervals.splice(index, 1);
                      },
                      addTask(intervalIndex) {
                          this.intervals[intervalIndex].tasks.push('');
                      },
                      removeTask(intervalIndex, taskIndex) {
                          this.intervals[intervalIndex].tasks.splice(taskIndex, 1);
                      }
                  }" 
                  class="space-y-6">
                @csrf

                <!-- عنوان الخطة -->
                <div class="glass-panel rounded-2xl p-6 border-t-4 border-cyan-500 shadow-lg shadow-cyan-900/20">
                    <label class="block text-sm font-bold text-slate-300 mb-2">اسم المصفوفة (الخطة)</label>
                    <input type="text" name="title" required placeholder="مثال: مراجعة البكالوريا الشاملة" 
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all">
                </div>

                <!-- الفترات والمهام (تتولد ديناميكياً) -->
                <div class="space-y-6">
                    <template x-for="(interval, iIndex) in intervals" :key="interval.id">
                        <div class="glass-panel rounded-2xl p-6 border border-slate-700/50 relative group transition-all hover:border-slate-500">
                            
                            <!-- زر الحذف للفترة -->
                            <button type="button" @click="removeInterval(iIndex)" x-show="intervals.length > 1" class="absolute top-4 left-4 text-red-500/50 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400 font-bold font-mono-code" x-text="iIndex + 1"></div>
                                <h3 class="text-lg font-bold text-slate-200">الفترة الدراسية (10 أيام)</h3>
                            </div>

                            <!-- حلقة المهام داخل الفترة -->
                            <div class="space-y-3 pl-11">
                                <template x-for="(task, tIndex) in interval.tasks" :key="tIndex">
                                    <div class="flex items-center gap-3">
                                        <!-- لاحظ السحر هنا: نربط الاسم بـ array لارافيل -->
                                        <input type="text" x-bind:name="'intervals[' + iIndex + '][tasks][]'" x-model="interval.tasks[tIndex]" required placeholder="اسم المهمة أو الدرس (مثال: وحدة النهايات)" 
                                               class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 transition-all">
                                        
                                        <button type="button" @click="removeTask(iIndex, tIndex)" x-show="interval.tasks.length > 1" class="text-slate-500 hover:text-red-400 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>

                                <!-- زر إضافة مهمة جديدة داخل نفس الفترة -->
                                <button type="button" @click="addTask(iIndex)" class="mt-3 text-sm text-cyan-500 hover:text-cyan-400 flex items-center gap-1 font-mono-code transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Add Task
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- زر إضافة فترة جديدة -->
                <button type="button" @click="addInterval()" class="w-full py-4 border-2 border-slate-700 border-dashed rounded-2xl flex items-center justify-center gap-2 text-slate-400 hover:text-white hover:border-slate-500 hover:bg-slate-800/50 transition-all font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    إضافة فترة دراسية جديدة (Interval)
                </button>

                <!-- زر الحفظ (التنشيط) -->
                <div class="pt-6">
                    <button type="submit" class="w-full bg-gradient-to-l from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black text-lg py-4 rounded-xl tracking-wider transition-all duration-300 transform hover:-translate-y-1 shadow-[0_0_20px_rgba(56,189,248,0.4)]">
                        تنشيط المصفوفة العصبية
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>