<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পেমেন্ট সাবমিট হয়েছে — SKYNITY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(160deg, #0a0f1e 0%, #0d1b3e 50%, #0a0f1e 100%); min-height: 100vh; }
        body::before { content:''; position:fixed; inset:0; background-image:radial-gradient(circle at 1px 1px, rgba(99,102,241,0.1) 1px, transparent 0); background-size:32px 32px; pointer-events:none; z-index:0; }
        .glass { background:rgba(255,255,255,0.05); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.1); }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 1.2s linear infinite; }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(251,191,36,0.4); }
            70% { box-shadow: 0 0 0 20px rgba(251,191,36,0); }
            100% { box-shadow: 0 0 0 0 rgba(251,191,36,0); }
        }
        .pulse-ring { animation: pulse-ring 2s ease-out infinite; }
    </style>
</head>
<body class="text-white relative">
<div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">

        {{-- ICON --}}
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 rounded-3xl flex items-center justify-center pulse-ring"
                 style="background: linear-gradient(135deg, rgba(251,191,36,0.2), rgba(245,158,11,0.1)); border: 2px solid rgba(251,191,36,0.4);">
                <svg class="w-12 h-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- TITLE --}}
        <h1 class="text-2xl font-black text-white text-center mb-2">পেমেন্ট সাবমিট হয়েছে!</h1>
        <p class="text-gray-400 text-sm text-center mb-7">এডমিন যাচাই করার পর আপনার ইন্টারনেট চালু হবে</p>

        {{-- ORDER INFO --}}
        <div class="glass rounded-2xl p-5 mb-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">অর্ডার তথ্য</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-gray-400">
                    <span>প্যাকেজ</span>
                    <span class="text-white font-semibold">{{ $package->name }}</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>মূল্য</span>
                    <span class="text-emerald-400 font-bold">৳{{ number_format($request->amount) }}</span>
                </div>
                @if($request->transaction_id)
                <div class="flex justify-between text-gray-400">
                    <span>TrxID</span>
                    <span class="text-white font-mono font-semibold">{{ $request->transaction_id }}</span>
                </div>
                @endif
                <div class="flex justify-between text-gray-400">
                    <span>রিকোয়েস্ট</span>
                    <span class="text-indigo-400 font-bold">#{{ $request->id }}</span>
                </div>
            </div>
        </div>

        {{-- STATUS BOX --}}
        <div class="glass rounded-2xl p-5 mb-5">
            <div id="statusContent" class="flex flex-col items-center gap-4 py-2">
                {{-- Loading state --}}
                <div id="loadingState" class="flex flex-col items-center gap-3">
                    <svg class="spinner w-10 h-10 text-indigo-400" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5" stroke-dasharray="50" stroke-dashoffset="20" stroke-linecap="round"/>
                    </svg>
                    <div class="text-center">
                        <p class="text-white font-semibold" id="statusText">যাচাই করা হচ্ছে...</p>
                        <p class="text-gray-500 text-xs mt-1">এডমিন অ্যাপ্রুভ করলে পেজ আপডেট হবে</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEPS --}}
        <div class="glass rounded-2xl p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">পরবর্তী ধাপ</h3>
            <div class="space-y-3">
                @foreach([['✓','পেমেন্ট সাবমিট হয়েছে','emerald'],['⏳','এডমিন যাচাই করছেন','amber'],['🚀','ইন্টারনেট চালু হবে','indigo']] as [$icon,$text,$color])
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-{{ $color }}-500/15 border border-{{ $color }}-500/25 flex items-center justify-center text-sm flex-shrink-0">{{ $icon }}</div>
                    <p class="text-gray-400 text-sm">{{ $text }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-center text-xs text-gray-700 mt-6">এই পেজ বন্ধ করবেন না — স্বয়ংক্রিয়ভাবে আপডেট হবে</p>
    </div>
</div>

<script>
if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');

const requestId = {{ $request->id }};

function checkStatus() {
    fetch('/captive/status/' + requestId)
        .then(r => r.json())
        .then(data => {
            const statusText = document.getElementById('statusText');

            if (data.status === 'approved') {
                statusText.textContent = '✅ ' + data.message;
                setTimeout(() => window.location.href = data.redirect, 1500);
            } else if (data.status === 'rejected') {
                document.getElementById('loadingState').innerHTML = `
                    <div class="w-12 h-12 rounded-2xl bg-red-500/15 border border-red-500/25 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-red-400 font-semibold">${data.message}</p>
                        <a href="/captive" class="text-xs text-indigo-400 mt-2 inline-block hover:underline">আবার চেষ্টা করুন</a>
                    </div>`;
            } else {
                statusText.textContent = data.message;
                setTimeout(checkStatus, 5000);
            }
        })
        .catch(() => setTimeout(checkStatus, 10000));
}

setTimeout(checkStatus, 3000);
</script>
</body>
</html>
