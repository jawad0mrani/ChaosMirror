<x-app-layout>
    <!-- تم إزالة x-slot header الافتراضي لمنع ظهور الشريط الأبيض المزعج للارافيل -->

    <!-- CSS اختراقي وقوي لإجبار مكونات لارافيل على التصميم الفضائي -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Tajawal:wght@400;700;900&display=swap');
        
        /* إخفاء الهيدر الأبيض الافتراضي الخاص بـ app.blade.php إن وُجد */
        header.bg-white { display: none !important; }
        
        .profile-container {
            font-family: 'Tajawal', sans-serif;
        }

        /* تدمير خلفيات لارافيل البيضاء */
        .profile-container .bg-white {
            background-color: transparent !important;
            box-shadow: none !important;
        }
        
        /* ضبط النصوص لتكون منسقة يميناً */
        .profile-container section { width: 100%; direction: rtl; text-align: right; }
        
        .profile-container h2 { 
            color: #38bdf8 !important; 
            font-family: 'Tajawal', sans-serif !important; 
            font-weight: 900 !important; 
            font-size: 1.5rem !important;
            margin-bottom: 0.5rem;
        }
        
        .profile-container p { 
            color: #94a3b8 !important; 
            font-family: 'Tajawal', sans-serif !important; 
            font-size: 0.9rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        .profile-container label { 
            color: #cbd5e1 !important; 
            font-family: 'JetBrains Mono', monospace !important; 
            font-weight: bold; 
            display: block; 
            margin-bottom: 0.5rem;
            margin-top: 1rem;
        }
        
        /* تلوين الحقول (Inputs) لتناسب النيون */
        .profile-container input[type="text"], 
        .profile-container input[type="email"], 
        .profile-container input[type="password"] {
            background-color: rgba(15, 23, 42, 0.8) !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            border-radius: 0.75rem !important;
            font-family: 'JetBrains Mono', monospace;
            padding: 0.75rem 1rem !important;
            width: 100% !important;
            direction: ltr; /* الحقول دائما يسار لليمين للإيميل والباسورد */
            text-align: left;
            transition: all 0.3s ease;
        }
        .profile-container input:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2) !important;
        }
        
        /* تلوين وضبط أزرار الحفظ */
        .profile-container button[type="submit"],
        .profile-container .inline-flex {
            background: linear-gradient(to left, #06b6d4, #3b82f6) !important;
            color: white !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.6rem 2rem !important;
            font-family: 'Tajawal', sans-serif !important;
            font-weight: 700 !important;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4) !important;
            transition: all 0.3s ease !important;
            text-transform: uppercase;
        }
        .profile-container button[type="submit"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.6) !important;
        }

        /* تعديل المسافات لأزرار لارافيل */
        .profile-container .flex.items-center.gap-4 {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
            margin-top: 2rem;
            gap: 1rem;
        }
        
        /* رسالة "تم الحفظ" */
        .profile-container .text-sm.text-gray-600 {
            color: #10b981 !important; /* لون أخضر للنجاح */
            font-weight: bold;
        }

        /* الأيقونات الجمالية */
        .deco-svg {
            position: absolute;
            top: -10%;
            left: -5%;
            width: 250px;
            height: 250px;
            opacity: 0.03;
            pointer-events: none;
            transition: all 0.5s ease;
            transform: rotate(-15deg);
        }
        .glass-card:hover .deco-svg {
            opacity: 0.08;
            transform: rotate(0deg) scale(1.1);
        }
    </style>

    <div class="pt-8 pb-12 bg-slate-950 min-h-screen relative overflow-hidden profile-container" dir="rtl">
        <!-- إضاءات خلفية خافتة -->
        <div class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] bg-cyan-900/20 rounded-full mix-blend-screen filter blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-purple-900/20 rounded-full mix-blend-screen filter blur-[100px]"></div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
            
            <!-- عنوان الصفحة -->
            <div class="flex items-center gap-3 mb-8 px-4 sm:px-0">
                <svg class="w-8 h-8 text-cyan-400 drop-shadow-[0_0_10px_rgba(56,189,248,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <h2 class="font-black text-3xl text-transparent bg-clip-text bg-gradient-to-l from-cyan-400 to-purple-500 font-tajawal tracking-wide">
                    إعدادات البصمة العصبية
                </h2>
            </div>

            <!-- معلومات البروفايل الأساسية -->
            <div class="p-8 sm:p-10 bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.3)] border-r-4 border-r-cyan-500 relative overflow-hidden glass-card">
                <!-- الأيقونة الجمالية في الخلفية على اليسار -->
                <svg class="deco-svg text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- تغيير كلمة المرور -->
            <div class="p-8 sm:p-10 bg-slate-900/60 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.3)] border-r-4 border-r-purple-500 relative overflow-hidden glass-card">
                <!-- الأيقونة الجمالية في الخلفية على اليسار -->
                <svg class="deco-svg text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- تم إزالة قسم حذف الحساب نهائياً -->

        </div>
    </div>
</x-app-layout>