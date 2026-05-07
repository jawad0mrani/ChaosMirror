<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chaos Mirror - بوابة الدخول</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Tajawal:wght@400;700;900&display=swap');
        body { font-family: 'Tajawal', sans-serif; background-color: #020617; overflow: hidden; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
        
        /* تأثيرات الزجاج */
        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(56, 189, 248, 0.1);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(56, 189, 248, 0.05);
        }

        /* أنيميشن المعادلات العائمة */
        .math-symbol {
            position: absolute;
            color: rgba(56, 189, 248, 0.15);
            font-family: 'JetBrains Mono', monospace;
            user-select: none;
            pointer-events: none;
            animation: float 20s infinite linear;
        }
        @keyframes float {
            0% { transform: translateY(110vh) rotate(0deg) scale(0.8); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) rotate(360deg) scale(1.2); opacity: 0; }
        }
        
        /* إضاءة النبض للخلفية */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.5;
            animation: pulse-glow 8s infinite alternate;
        }
        @keyframes pulse-glow {
            0% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(1.5); opacity: 0.6; }
        }
    </style>
</head>
<body class="text-white h-screen w-screen relative flex items-center justify-center">

    <!-- الخلفية العلمية (الفيزياء والرياضيات) -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="glow-orb bg-cyan-900 w-[500px] h-[500px] top-[-10%] right-[-10%]"></div>
        <div class="glow-orb bg-purple-900 w-[600px] h-[600px] bottom-[-20%] left-[-10%]" style="animation-delay: -4s;"></div>
        
        <!-- توليد رموز رياضية عشوائية تطفو -->
        <script>
            const symbols = ['∫f(x)dx', 'E=mc²', '∇×B', '∑n=1', 'e^(iπ)+1=0', 'lim(x→∞)', 'ΔxΔp≥ℏ/2', 'F=ma', 'λ=h/p', 'Φ', 'Ω', '∞'];
            for(let i=0; i<25; i++) {
                let el = document.createElement('div');
                el.className = 'math-symbol text-2xl md:text-4xl font-bold text-cyan-500/20';
                el.innerText = symbols[Math.floor(Math.random() * symbols.length)];
                el.style.left = Math.random() * 100 + 'vw';
                el.style.animationDuration = (Math.random() * 20 + 15) + 's';
                el.style.animationDelay = (Math.random() * -30) + 's';
                el.style.fontSize = (Math.random() * 2 + 1) + 'rem';
                document.querySelector('.absolute.inset-0').appendChild(el);
            }
        </script>
    </div>

    <!-- بطاقة تسجيل الدخول المركزية -->
    <div class="relative z-10 w-full max-w-md px-6">
        
        <!-- اللوجو -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-cyan-500 to-purple-600 flex items-center justify-center shadow-[0_0_30px_rgba(56,189,248,0.5)] mb-6 transform hover:rotate-12 transition-all duration-500">
                <svg class="w-10 h-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-500 font-mono-code tracking-widest text-center">
                CHAOS MIRROR
            </h1>
            <p class="text-cyan-500/70 text-sm mt-2 font-mono-code tracking-widest">> SYSTEM_AUTHENTICATION</p>
        </div>

        <!-- الفورم -->
        <div class="glass-panel rounded-3xl p-8 sm:p-10 border-t-4 border-t-cyan-500">
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-300 mb-2">البصمة التعريفية (Email)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                               class="w-full bg-slate-900/80 border border-slate-700 text-white rounded-xl pr-10 pl-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all font-mono-code" dir="ltr" placeholder="user@chaos.sys">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-300 mb-2">مفتاح التشفير (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                               class="w-full bg-slate-900/80 border border-slate-700 text-white rounded-xl pr-10 pl-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all font-mono-code" dir="ltr" placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-900 text-cyan-500 shadow-sm focus:ring-cyan-500 focus:ring-offset-slate-900 transition-all">
                        <span class="mr-2 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">حفظ الاتصال العصبي</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm text-cyan-500 hover:text-cyan-400 hover:underline transition-colors font-mono-code" href="{{ route('password.request') }}">
                            Lost Key?
                        </a>
                    @endif
                </div>

                <button type="submit" class="w-full relative group overflow-hidden rounded-xl bg-slate-800 p-[1px] mt-4">
                    <span class="absolute inset-0 bg-gradient-to-r from-cyan-500 via-purple-500 to-cyan-500 opacity-70 group-hover:opacity-100 animate-[spin_3s_linear_infinite] transition-opacity duration-300"></span>
                    <div class="relative flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-8 py-4 transition-all group-hover:bg-slate-900/80 text-white font-bold tracking-widest text-lg">
                        <span>تأكيد الهوية</span>
                        <svg class="w-5 h-5 text-cyan-400 group-hover:translate-x-reverse-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </div>
                </button>
            </form>
        </div>
        
        <!-- طابع برمجي سفلي -->
        <div class="text-center mt-8">
            <p class="text-slate-600 text-xs font-mono-code">v2.0.4 | Made by Jawad0mrani</p>
        </div>
    </div>
</body>
</html>