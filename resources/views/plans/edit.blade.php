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
        <div class="absolute top-0 right-[-10%] w-[600px] h-[600px] bg-purple-900/20 rounded-full mix-blend-screen filter blur-[120px]"></div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 relative z-10">
            
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-l from-purple-400 to-cyan-500">
                        تعديل المصفوفة العصبية
                    </h2>
                    <p class="text-slate-400 mt-2 font-mono-code text-sm" dir="ltr">> Modifying existing structures...</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition-colors text-sm border-b border-slate-700 hover:border-white pb-1">إلغاء والعودة</a>
            </div>

            <!-- الفورم مع حقن البيانات الموجودة مسبقاً -->
            <form action="{{ route('plans.update', $plan->id) }}" method="POST" 
                  x-data="{
                      title: '{{ $plan->title }}',
                      // حقن الفترات والمهام من قاعدة البيانات إلى الجافاسكريبت
                      intervals: {{ Illuminate\Support\Js::from($plan->intervals->map(function($interval) {
                          return [
                              'id' => $interval->id,
                              'is_new' => false,
                              'tasks' => $interval->tasks->map(function($task) {
                                  return [
                                      'id' => $task->id,
                                      'name' => $task->task_name,
                                      'is_completed' => (bool) $task->is_completed,
                                      'is_new' => false
                                  ];
                              })->toArray()
                          ];
                      })->toArray()) }},
                      
                      addInterval() {
                          this.intervals.push({ id: Date.now(), is_new: true, tasks: [{ id: Date.now(), name: '', is_new: true, is_completed: false }] });
                      },
                      addTask(intervalIndex) {
                          this.intervals[intervalIndex].tasks.push({ id: Date.now(), name: '', is_new: true, is_completed: false });
                      },
                      removeTask(intervalIndex, taskIndex) {
                          this.intervals[intervalIndex].tasks.splice(taskIndex, 1);
                      }
                  }" 
                  class="space-y-6">
                
                @csrf
                @method('PUT')
                <!-- حقل مخفي يجمع كل البيانات كـ JSON ويرسلها للـ Controller دفعة واحدة -->
                <input type="hidden" name="plan_data" x-bind:value="JSON.stringify(intervals)">

                <!-- عنوان الخطة -->
                <div class="flex justify-between items-center mb-6 border-b border-slate-700/50 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold font-mono-code"
                             :class="interval.is_new ? 'bg-cyan-500/20 text-cyan-400' : 'bg-purple-500/20 text-purple-400'"
                             x-text="iIndex + 1"></div>
                        <h3 class="text-lg font-bold text-slate-200" x-text="interval.is_new ? 'فترة جديدة مضافة' : 'فترة دراسية سابقة'"></h3>
                    </div>
                
                    <template x-if="interval.id">
                        <button type="button" 
                                @click="if(confirm('هل أنت متأكد من حذف هذه الفترة بما فيها من مهام؟')) { 
                                    let f = document.createElement('form');
                                    f.method = 'POST';
                                    f.action = '/intervals/' + interval.id;
                                    f.innerHTML = '<input type=\'hidden\' name=\'_method\' value=\'DELETE\'><input type=\'hidden\' name=\'_token\' value=\'{{ csrf_token() }}\'>';
                                    document.body.appendChild(f);
                                    f.submit();
                                }"
                                class="text-red-500 hover:text-red-400 text-sm font-bold bg-red-500/10 px-3 py-1.5 rounded-lg border border-red-500/20 transition-all hover:bg-red-500/20 m-0">
                            ❌ حذف الفترة
                        </button>
                    </template>
                </div>

                <!-- الفترات والمهام -->
                <div class="space-y-6">
                    <template x-for="(interval, iIndex) in intervals" :key="interval.id">
                        <div class="glass-panel rounded-2xl p-6 border border-slate-700/50 relative transition-all"
                             :class="interval.is_new ? 'border-cyan-500/50 bg-cyan-900/5' : ''">
                            
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold font-mono-code"
                                     :class="interval.is_new ? 'bg-cyan-500/20 text-cyan-400' : 'bg-purple-500/20 text-purple-400'"
                                     x-text="iIndex + 1"></div>
                                <h3 class="text-lg font-bold text-slate-200" x-text="interval.is_new ? 'فترة جديدة مضافة' : 'فترة دراسية سابقة'"></h3>
                            </div>

                            <div class="space-y-3 pl-11">
                                <template x-for="(task, tIndex) in interval.tasks" :key="task.id">
                                    <div class="flex items-center gap-3">
                                        
                                        <!-- إذا كانت المهمة منجزة (للقراءة فقط) -->
                                        <template x-if="task.is_completed">
                                            <div class="flex-1 flex items-center gap-3 bg-slate-900/50 border border-slate-800 rounded-lg px-4 py-2.5 opacity-60">
                                                <svg class="w-5 h-5 text-cyan-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                <span x-text="task.name" class="text-sm text-slate-400 line-through"></span>
                                                <span class="text-xs text-slate-500 font-mono-code mr-auto">Completed</span>
                                            </div>
                                        </template>

                                        <!-- إذا لم تكن منجزة (قابلة للتعديل) -->
                                        <template x-if="!task.is_completed">
                                            <div class="flex-1 flex items-center gap-3">
                                                <input type="text" x-model="task.name" required placeholder="اسم المهمة أو الدرس" 
                                                       class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-1 focus:border-cyan-500 transition-all"
                                                       :class="task.is_new ? 'focus:ring-cyan-500' : 'focus:ring-purple-500'">
                                                
                                                <button type="button" @click="removeTask(iIndex, tIndex)" x-show="task.is_new" class="text-slate-500 hover:text-red-400 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>

                                    </div>
                                </template>

                                <button type="button" @click="addTask(iIndex)" class="mt-3 text-sm flex items-center gap-1 font-mono-code transition-colors"
                                        :class="interval.is_new ? 'text-cyan-500 hover:text-cyan-400' : 'text-purple-500 hover:text-purple-400'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Add Task
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addInterval()" class="w-full py-4 border-2 border-slate-700 border-dashed rounded-2xl flex items-center justify-center gap-2 text-slate-400 hover:text-white hover:border-slate-500 hover:bg-slate-800/50 transition-all font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    إضافة فترة دراسية جديدة (Interval)
                </button>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-gradient-to-l from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white font-black text-lg py-4 rounded-xl tracking-wider transition-all duration-300 shadow-[0_0_20px_rgba(168,85,247,0.4)]">
                        تحديث ومزامنة المصفوفة
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
