<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>পেমেন্ট — SKYNITY WiFi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: linear-gradient(160deg, #0a0f1e 0%, #0d1b3e 50%, #0a0f1e 100%); min-height: 100vh; }
        body::before { content:''; position:fixed; inset:0; background-image:radial-gradient(circle at 1px 1px, rgba(99,102,241,0.1) 1px, transparent 0); background-size:32px 32px; pointer-events:none; z-index:0; }
        .glass { background:rgba(255,255,255,0.04); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.08); }
        .glass-strong { background:rgba(255,255,255,0.06); backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.1); }
        .pay-method { cursor:pointer; transition:all 0.2s; border:1.5px solid rgba(255,255,255,0.07); border-radius:14px; }
        .pay-method.selected { border-color:{{ $template->primary_color ?? '#6366f1' }}; background:{{ $template->primary_color ?? '#6366f1' }}18; box-shadow:0 0 16px {{ $template->primary_color ?? '#6366f1' }}25; }
    </style>
</head>
<body class="text-white relative">
@php
    $payPhone  = \Illuminate\Support\Facades\DB::table('settings')->where('key','shop_phone')->value('value') ?? '01XXXXXXXXX';
    $payNumbers = [
        'bkash'  => \Illuminate\Support\Facades\DB::table('settings')->where('key','bkash_number')->value('value')  ?? $payPhone,
        'nagad'  => \Illuminate\Support\Facades\DB::table('settings')->where('key','nagad_number')->value('value')  ?? $payPhone,
        'rocket' => \Illuminate\Support\Facades\DB::table('settings')->where('key','rocket_number')->value('value') ?? $payPhone,
    ];
@endphp
<div class="relative z-10 max-w-md mx-auto px-4 py-6 pb-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('captive.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            ফিরে যান
        </a>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-white font-black text-lg tracking-tight">SKYNITY</span>
        </div>
    </div>

    {{-- ORDER SUMMARY --}}
    <div class="glass-strong rounded-2xl p-5 mb-5">
        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">📦 অর্ডার সামারি</h2>
        @if(isset($isCustom) && $isCustom)
        <div class="space-y-2.5 text-sm">
            <div class="flex justify-between text-gray-400"><span>স্পীড</span><span class="text-white font-semibold">{{ $customData['speed'] }} Mbps</span></div>
            <div class="flex justify-between text-gray-400"><span>মেয়াদ</span><span class="text-white font-semibold">{{ $customData['days'] }} দিন</span></div>
            <div class="flex justify-between text-gray-400"><span>ডিভাইস</span><span class="text-white font-semibold">{{ $customData['devices'] }}টি</span></div>
            <div class="border-t border-white/5"></div>
            <div class="flex justify-between items-center">
                <span class="text-white font-bold">মোট</span>
                <span class="text-3xl font-black text-emerald-400">৳{{ number_format($customData['price']) }}</span>
            </div>
        </div>
        @else
        <div class="space-y-2.5 text-sm">
            <div class="flex justify-between text-gray-400"><span>প্যাকেজ</span><span class="text-white font-semibold">{{ $package->name }}</span></div>
            <div class="flex justify-between text-gray-400"><span>স্পীড</span><span class="text-white font-semibold">{{ $package->speed_limit ?? 'Unlimited' }}</span></div>
            <div class="flex justify-between text-gray-400"><span>মেয়াদ</span><span class="text-white font-semibold">{{ $package->validity ?? '30' }} দিন</span></div>
            <div class="border-t border-white/5"></div>
            <div class="flex justify-between items-center">
                <span class="text-white font-bold">মোট</span>
                <span class="text-3xl font-black text-emerald-400">৳{{ number_format($package->selling_price ?? $package->price) }}</span>
            </div>
        </div>
        @endif
    </div>

    {{-- FORM --}}
    <form action="{{ isset($isCustom) && $isCustom ? route('captive.custom.submit') : route('captive.submit') }}"
          method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="router_id"   value="{{ $router->id }}">
        <input type="hidden" name="mac_address" value="{{ $clientMac }}">
        <input type="hidden" name="ip_address"  value="{{ $clientIp }}">
        @if(isset($isCustom) && $isCustom)
            <input type="hidden" name="speed"   value="{{ $customData['speed'] }}">
            <input type="hidden" name="days"    value="{{ $customData['days'] }}">
            <input type="hidden" name="devices" value="{{ $customData['devices'] }}">
            <input type="hidden" name="price"   value="{{ $customData['price'] }}">
        @else
            <input type="hidden" name="package_id" value="{{ $package->id }}">
        @endif

        {{-- CUSTOMER INFO --}}
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">👤 আপনার তথ্য</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-medium">নাম *</label>
                    <input type="text" name="customer_name" required placeholder="পুরো নাম"
                           class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500/40 transition">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-medium">মোবাইল *</label>
                    <input type="tel" name="customer_phone" required placeholder="01XXXXXXXXX" pattern="01[0-9]{9}"
                           class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500/40 transition">
                </div>
            </div>
        </div>

        {{-- PAYMENT METHOD --}}
        <div class="glass-strong rounded-2xl p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">💳 পেমেন্ট মেথড</h3>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <label class="pay-method selected p-3 flex items-center gap-3"
                       onclick="selectPayment('bkash', this, '{{ $payNumbers['bkash'] }}')">
                    <input type="radio" name="payment_method" value="bkash" checked class="hidden">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-sm flex-shrink-0"
                         style="background:linear-gradient(135deg,#e91e8c,#c2185b);">b</div>
                    <span class="text-sm font-semibold">বিকাশ</span>
                </label>
                <label class="pay-method p-3 flex items-center gap-3"
                       onclick="selectPayment('nagad', this, '{{ $payNumbers['nagad'] }}')">
                    <input type="radio" name="payment_method" value="nagad" class="hidden">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-sm flex-shrink-0"
                         style="background:linear-gradient(135deg,#f97316,#ea580c);">N</div>
                    <span class="text-sm font-semibold">নগদ</span>
                </label>
                <label class="pay-method p-3 flex items-center gap-3"
                       onclick="selectPayment('rocket', this, '{{ $payNumbers['rocket'] }}')">
                    <input type="radio" name="payment_method" value="rocket" class="hidden">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-sm flex-shrink-0"
                         style="background:linear-gradient(135deg,#9333ea,#7c3aed);">R</div>
                    <span class="text-sm font-semibold">রকেট</span>
                </label>
                <label class="pay-method p-3 flex items-center gap-3"
                       onclick="selectPayment('cash', this, null)">
                    <input type="radio" name="payment_method" value="cash" class="hidden">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-xl flex-shrink-0"
                         style="background:linear-gradient(135deg,#10b981,#059669);">৳</div>
                    <span class="text-sm font-semibold">ক্যাশ</span>
                </label>
            </div>

            {{-- Mobile payment info --}}
            <div id="mobilePayInfo" class="rounded-xl p-4 mb-4"
                 style="background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2);">
                <p class="text-xs text-indigo-300 mb-2 font-medium">এই নম্বরে টাকা পাঠান:</p>
                <div class="flex items-center justify-between gap-3">
                    <span id="payNumber" class="text-xl font-black text-white tracking-wider">{{ $payNumbers['bkash'] }}</span>
                    <button type="button" id="copyBtn" onclick="copyPayNumber()"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg flex-shrink-0 transition hover:opacity-80"
                            style="background:rgba(99,102,241,0.3); color:#a5b4fc; border:1px solid rgba(99,102,241,0.35);">
                        কপি
                    </button>
                </div>
            </div>

            <div id="transactionField">
                <label class="block text-xs text-gray-500 mb-1.5 font-medium">ট্রানজেকশন আইডি *</label>
                <input type="text" name="transaction_id" id="transactionInput"
                       placeholder="যেমন: 8N7ABC123X"
                       class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500/40 transition"
                       required>
            </div>
        </div>

        @if($errors->any())
        <div class="rounded-xl p-4" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25);">
            @foreach($errors->all() as $error)
            <p class="text-red-300 text-sm">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <button type="submit"
                class="w-full py-4 rounded-2xl font-bold text-base transition hover:opacity-90 active:scale-95"
                style="background:{{ $template->cta_button_color ?? 'linear-gradient(135deg,#6366f1,#a855f7)' }}; color:{{ $template->cta_button_text_color ?? '#fff' }}; box-shadow:0 8px 25px rgba(99,102,241,0.35);">
            কনফার্ম করুন ✓
        </button>
        <p class="text-center text-xs text-gray-600">পেমেন্টের পর এডমিন যাচাই করবেন • সাধারণত ৫-১০ মিনিট</p>
    </form>
</div>

<script>
if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');

let currentPayNumber = '{{ $payNumbers['bkash'] }}';

function selectPayment(method, el, number) {
    document.querySelectorAll('.pay-method').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;

    const infoBox = document.getElementById('mobilePayInfo');
    const txField = document.getElementById('transactionField');
    const txInput = document.getElementById('transactionInput');

    if (method === 'cash') {
        infoBox.style.display = 'none';
        txField.style.display = 'none';
        txInput.removeAttribute('required');
    } else {
        infoBox.style.display = 'block';
        txField.style.display = 'block';
        txInput.setAttribute('required', 'required');
        currentPayNumber = number;
        document.getElementById('payNumber').textContent = number;
    }
}

function copyPayNumber() {
    if (!currentPayNumber) return;
    navigator.clipboard.writeText(currentPayNumber).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.textContent = 'কপি ✓';
        setTimeout(() => btn.textContent = 'কপি', 1500);
    });
}
</script>
</body>
</html>
