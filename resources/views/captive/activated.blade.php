<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎉 ইন্টারনেট চালু! — SKYNITY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(160deg, #052e16 0%, #0d2e1a 50%, #052e16 100%); min-height: 100vh; }
        body::before { content:''; position:fixed; inset:0; background-image:radial-gradient(circle at 1px 1px, rgba(16,185,129,0.12) 1px, transparent 0); background-size:32px 32px; pointer-events:none; z-index:0; }

        .glass { background:rgba(255,255,255,0.05); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.1); }

        @keyframes checkDraw {
            to { stroke-dashoffset: 0; }
        }
        .check-path { stroke-dasharray: 80; stroke-dashoffset: 80; animation: checkDraw 0.6s 0.3s ease forwards; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .float-icon { animation: float 3s ease-in-out infinite; }

        /* Confetti */
        @keyframes confettiFall {
            0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
        }
        .confetti-piece { position: fixed; width: 8px; height: 8px; top: -20px; pointer-events: none; z-index: 50; animation: confettiFall linear forwards; }
    </style>
</head>
<body class="text-white relative">

<div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">

        {{-- SUCCESS ICON --}}
        <div class="flex justify-center mb-6 float-icon">
            <div class="w-28 h-28 rounded-3xl flex items-center justify-center"
                 style="background:linear-gradient(135deg, rgba(16,185,129,0.3), rgba(5,150,105,0.15)); border:2px solid rgba(52,211,153,0.5); box-shadow:0 0 60px rgba(16,185,129,0.3);">
                <svg class="w-14 h-14" viewBox="0 0 60 60" fill="none">
                    <circle cx="30" cy="30" r="28" stroke="rgba(52,211,153,0.3)" stroke-width="2"/>
                    <path class="check-path" d="M16 30 L26 40 L44 20" stroke="#34d399" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        {{-- TITLE --}}
        <div class="text-center mb-6">
            <h1 class="text-3xl font-black text-white mb-2">🎉 অভিনন্দন!</h1>
            <p class="text-emerald-400 font-semibold text-lg">আপনার ইন্টারনেট চালু হয়েছে!</p>
            <p class="text-gray-500 text-sm mt-1">নিচের তথ্য দিয়ে WiFi তে লগইন করুন</p>
        </div>

        {{-- CREDENTIALS CARD --}}
        <div class="glass rounded-2xl p-5 mb-5">
            <h3 class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-4 text-center">🔑 লগইন তথ্য</h3>
            <div class="space-y-3">
                <div class="rounded-xl p-4 flex items-center justify-between" style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.06);">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">ইউজারনেম</p>
                        <p class="text-xl font-mono font-black text-white" id="usernameText">{{ $user->hotspot_username ?? $request->voucher_code }}</p>
                    </div>
                    <button onclick="copyText('usernameText', this)"
                            class="px-3 py-1.5 text-xs font-bold rounded-xl transition hover:opacity-80 flex-shrink-0"
                            style="background:rgba(52,211,153,0.15); color:#34d399; border:1px solid rgba(52,211,153,0.25);">
                        কপি
                    </button>
                </div>
                <div class="rounded-xl p-4 flex items-center justify-between" style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.06);">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">পাসওয়ার্ড</p>
                        <p class="text-xl font-mono font-black text-white" id="passwordText">{{ $user->hotspot_password ?? '••••••' }}</p>
                    </div>
                    <button onclick="copyText('passwordText', this)"
                            class="px-3 py-1.5 text-xs font-bold rounded-xl transition hover:opacity-80 flex-shrink-0"
                            style="background:rgba(52,211,153,0.15); color:#34d399; border:1px solid rgba(52,211,153,0.25);">
                        কপি
                    </button>
                </div>
            </div>

            {{-- Package info --}}
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="rounded-xl p-3 text-center" style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15);">
                    <p class="text-xs text-gray-500 mb-1">প্যাকেজ</p>
                    <p class="text-sm font-bold text-emerald-400 truncate">{{ $request->package->name ?? 'Custom' }}</p>
                </div>
                <div class="rounded-xl p-3 text-center" style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15);">
                    <p class="text-xs text-gray-500 mb-1">মেয়াদ শেষ</p>
                    <p class="text-sm font-bold text-emerald-400">{{ $user->subscription_expires_at?->format('d M Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- HOW TO CONNECT --}}
        <div class="glass rounded-2xl p-5 mb-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">📋 কিভাবে কানেক্ট করবেন</h3>
            <div class="space-y-3">
                @foreach(['WiFi নেটওয়ার্কে কানেক্ট থাকুন','ব্রাউজারে লগইন পেজ আসলে উপরের তথ্য দিন','লগইন বাটনে ক্লিক করুন','ইন্টারনেট উপভোগ করুন 🚀'] as $i => $step)
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black flex-shrink-0 text-white"
                         style="background:linear-gradient(135deg,#6366f1,#a855f7);">{{ $i+1 }}</div>
                    <p class="text-gray-400 text-sm">{{ $step }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- DASHBOARD LINK --}}
        @if($user)
        <a href="{{ route('customer.dashboard') }}"
           class="block w-full py-4 rounded-2xl font-bold text-center text-base text-emerald-900 transition hover:opacity-90 mb-4"
           style="background:linear-gradient(135deg,#34d399,#10b981); box-shadow:0 8px 25px rgba(16,185,129,0.3);">
            আপনার ড্যাশবোর্ডে যান →
        </a>
        @endif

        <div class="text-center rounded-xl p-3" style="background:rgba(251,191,36,0.08); border:1px solid rgba(251,191,36,0.2);">
            <p class="text-amber-400 text-xs font-semibold">⚠️ এই তথ্যগুলো স্ক্রিনশট করে রাখুন!</p>
        </div>
    </div>
</div>

{{-- Hidden auto-login form --}}
<form id="autoLoginForm" action="{{ $request->router->hotspot_url ?? 'http://192.168.88.1/login' }}" method="POST" style="display:none;">
    <input type="hidden" name="dst" value="">
    <input type="hidden" name="popup" value="true">
    <input type="hidden" name="username" value="{{ $user->hotspot_username ?? $request->voucher_code }}">
    <input type="hidden" name="password" value="{{ $user->hotspot_password ?? '' }}">
</form>

<script>
if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');

// Confetti
function spawnConfetti() {
    const colors = ['#34d399','#6366f1','#f59e0b','#ec4899','#60a5fa','#a855f7'];
    for (let i = 0; i < 60; i++) {
        const el = document.createElement('div');
        el.className = 'confetti-piece';
        el.style.left = Math.random() * 100 + 'vw';
        el.style.background = colors[Math.floor(Math.random() * colors.length)];
        el.style.animationDuration = (Math.random() * 2 + 2) + 's';
        el.style.animationDelay = Math.random() * 1.5 + 's';
        el.style.borderRadius = Math.random() > 0.5 ? '50%' : '3px';
        el.style.width = (Math.random() * 6 + 6) + 'px';
        el.style.height = (Math.random() * 6 + 6) + 'px';
        document.body.appendChild(el);
        el.addEventListener('animationend', () => el.remove());
    }
}

function copyText(id, btn) {
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'কপি ✓';
        setTimeout(() => btn.textContent = orig, 1500);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    spawnConfetti();
    setTimeout(() => spawnConfetti(), 2000);
    // Auto login after 3s
    setTimeout(() => {
        const form = document.getElementById('autoLoginForm');
        if (form) form.submit();
    }, 3000);
});
</script>
</body>
</html>
