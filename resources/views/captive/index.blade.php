<!DOCTYPE html>
<html lang="bn" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>{{ $template->welcome_title ?? 'SKYNITY WiFi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: '{{ $template->font_family ?? 'Poppins' }}', 'Poppins', sans-serif; }

        body {
            background: {{ $template->background_color ?? 'linear-gradient(160deg, #0a0f1e 0%, #0d1b3e 50%, #0a0f1e 100%)' }};
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(99,102,241,0.1) 1px, transparent 0);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes wifiPulse {
            0%, 100% { opacity: 0.35; }
            50% { opacity: 1; }
        }
        .wifi-arc-1 { animation: wifiPulse 1.8s ease-in-out infinite; }
        .wifi-arc-2 { animation: wifiPulse 1.8s ease-in-out 0.35s infinite; }
        .wifi-arc-3 { animation: wifiPulse 1.8s ease-in-out 0.7s infinite; }

        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
        .glass-strong { background: rgba(255,255,255,0.06); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.1); }

        .pkg-card {
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.07);
            border-radius: {{ $template->package_card_radius ?? 16 }}px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .pkg-card:hover { background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.35); transform: translateY(-3px); }
        .pkg-card.selected {
            background: rgba(99,102,241,0.15);
            border-color: {{ $template->primary_color ?? '#6366f1' }};
            box-shadow: 0 0 20px {{ $template->primary_color ?? '#6366f1' }}35;
            transform: translateY(-3px);
        }

        .tab-pill { border-radius: 12px; transition: all 0.3s; }
        .tab-pill.active {
            background: linear-gradient(135deg, {{ $template->primary_color ?? '#6366f1' }}, #a855f7);
            color: white;
            box-shadow: 0 4px 15px {{ $template->primary_color ?? '#6366f1' }}45;
        }

        .slider { -webkit-appearance: none; width: 100%; height: 5px; border-radius: 99px; background: rgba(255,255,255,0.1); outline: none; }
        .slider::-webkit-slider-thumb { -webkit-appearance: none; width: 20px; height: 20px; border-radius: 50%; background: {{ $template->primary_color ?? '#6366f1' }}; box-shadow: 0 0 0 4px {{ $template->primary_color ?? '#6366f1' }}30; cursor: pointer; }

        .packages-grid {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat({{ $template->package_grid_sm ?? 2 }}, minmax(0, 1fr));
        }
        @media (min-width: 640px) { .packages-grid { grid-template-columns: repeat({{ $template->package_grid_md ?? 2 }}, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .packages-grid { grid-template-columns: repeat({{ $template->package_grid_lg ?? 3 }}, minmax(0, 1fr)); } }

        .speed-text { background: linear-gradient(135deg, {{ $template->primary_color ?? '#818cf8' }}, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        .cta-btn { background: {{ $template->cta_button_color ?? 'linear-gradient(135deg, #6366f1, #a855f7)' }}; color: {{ $template->cta_button_text_color ?? '#fff' }}; border-radius: {{ $template->button_radius ?? 14 }}px; transition: all 0.2s; }
        .cta-btn:not(:disabled):hover { filter: brightness(1.1); transform: translateY(-1px); }
        .cta-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .dev-btn { border: 1.5px solid rgba(255,255,255,0.08); border-radius: 12px; cursor: pointer; transition: all 0.2s; color: #6b7280; }
        .dev-btn.active { background: {{ $template->primary_color ?? '#6366f1' }}; border-color: transparent; color: white; box-shadow: 0 4px 12px {{ $template->primary_color ?? '#6366f1' }}40; }

        {{ $template->custom_css ?? '' }}
    </style>
</head>
<body class="text-white relative">
<div class="relative z-10 max-w-md mx-auto px-4 py-6 pb-10">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="relative w-11 h-11">
                <div class="absolute inset-0 rounded-2xl opacity-20" style="background: linear-gradient(135deg, {{ $template->primary_color ?? '#6366f1' }}, #a855f7);"></div>
                <div class="w-full h-full flex items-center justify-center">
                    <svg viewBox="0 0 40 40" class="w-8 h-8" fill="none">
                        <path class="wifi-arc-3" d="M5 19 Q20 5 35 19" stroke="{{ $template->primary_color ?? '#818cf8' }}" stroke-width="2.5" stroke-linecap="round"/>
                        <path class="wifi-arc-2" d="M10 24 Q20 13 30 24" stroke="#a78bfa" stroke-width="2.5" stroke-linecap="round"/>
                        <path class="wifi-arc-1" d="M15 29 Q20 22 25 29" stroke="#c4b5fd" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="20" cy="33" r="2.8" fill="{{ $template->primary_color ?? '#818cf8' }}"/>
                    </svg>
                </div>
            </div>
            <div>
                <h1 class="text-lg font-black tracking-tight text-white leading-tight">{{ $template->welcome_title ?? 'SKYNITY WiFi' }}</h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs text-emerald-400 font-semibold">CONNECTED</span>
                </div>
            </div>
        </div>
        <select id="langSelect" onchange="changeLanguage(this.value)"
                class="glass text-white text-xs px-3 py-2 rounded-xl border-0 focus:outline-none cursor-pointer">
            <option value="bn">🇧🇩 বাং</option>
            <option value="en">🇬🇧 EN</option>
        </select>
    </div>

    {{-- DEVICE INFO STRIP --}}
    <div class="glass rounded-2xl px-4 py-3 mb-5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-indigo-500/20 border border-indigo-500/25 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-xs font-semibold">{{ $router->hotspot_name ?? 'SKYNITY Hotspot' }}</p>
                <p class="text-gray-600 text-xs font-mono">{{ $clientMac ?? '--:--:--:--:--:--' }}</p>
            </div>
        </div>
        <span class="px-2.5 py-1 bg-emerald-500/12 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/25">WiFi ✓</span>
    </div>

    {{-- TABS --}}
    <div class="glass rounded-2xl p-1.5 mb-5 grid grid-cols-3 gap-1">
        <button onclick="showTab('trial')" id="tabTrial" class="tab-pill active py-2.5 text-xs font-bold">
            <span id="tabTrialText">🎁 ট্রায়াল</span>
        </button>
        <button onclick="showTab('packages')" id="tabPackages" class="tab-pill py-2.5 text-xs font-bold text-gray-500">
            <span id="tabPackagesText">📦 প্যাকেজ</span>
        </button>
        <button onclick="showTab('custom')" id="tabCustom" class="tab-pill py-2.5 text-xs font-bold text-gray-500">
            <span id="tabCustomText">⚙️ কাস্টম</span>
        </button>
    </div>

    {{-- TAB: FREE TRIAL --}}
    <div id="trialSection" class="tab-content">
        <div class="glass-strong rounded-2xl p-5 border border-emerald-500/15">
            <div class="text-center mb-5">
                <div class="inline-flex w-16 h-16 rounded-2xl bg-emerald-500/12 border border-emerald-500/20 items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
                <h3 id="trialTitle" class="text-xl font-black text-white">৫ দিন ফ্রি ট্রায়াল!</h3>
                <p id="trialSubtitle" class="text-emerald-400 text-sm mt-1">নতুন ইউজারদের জন্য • 10 Mbps</p>
            </div>

            <form action="{{ route('captive.trial') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="router_id" value="{{ $router->id }}">
                <input type="hidden" name="mac_address" value="{{ $clientMac }}">
                <input type="hidden" name="ip_address" value="{{ $clientIp }}">

                <div>
                    <label id="nameLabel" class="block text-xs text-gray-500 mb-1.5 font-medium">আপনার নাম</label>
                    <input type="text" name="customer_name" required placeholder="পুরো নাম লিখুন"
                           class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/40 transition">
                </div>
                <div>
                    <label id="phoneLabel" class="block text-xs text-gray-500 mb-1.5 font-medium">মোবাইল নম্বর</label>
                    <input type="tel" name="customer_phone" required placeholder="01XXXXXXXXX" pattern="01[0-9]{9}"
                           class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/40 transition">
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-sm mt-2 transition hover:opacity-90"
                        style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 8px 20px rgba(16,185,129,0.25);">
                    <span id="trialBtn">ফ্রি ট্রায়াল নিন →</span>
                </button>
                <p id="trialNote" class="text-xs text-gray-600 text-center">এডমিন অ্যাপ্রুভ করলে WiFi চালু হবে</p>
            </form>
        </div>
    </div>

    {{-- TAB: PACKAGES --}}
    <div id="packagesSection" class="tab-content hidden">
        <p id="packageTitle" class="text-xs font-bold text-gray-600 uppercase tracking-widest mb-3 px-1">প্যাকেজ বেছে নিন</p>

        @if($packages->count() > 0)
        <div class="packages-grid mb-4">
            @foreach($packages as $i => $package)
            @php
                $speed = preg_replace('/[^0-9]/', '', explode('/', $package->speed_limit ?? '10M')[0]) ?: '10';
                $price = $package->selling_price ?? $package->price;
                $colors = [['#818cf8','#a855f7'],['#34d399','#10b981'],['#60a5fa','#3b82f6'],['#f472b6','#ec4899']];
                [$c1,$c2] = $colors[$i % 4];
            @endphp
            <div class="pkg-card p-4 relative {{ $i===1 ? 'ring-1 ring-purple-500/40' : '' }}"
                 onclick="selectPackage({{ $package->id }}, {{ $price }}, '{{ addslashes($package->name) }}', this)">
                @if($i === 1)
                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2">
                    <span class="px-2.5 py-0.5 text-white text-xs font-bold rounded-full whitespace-nowrap" style="background: linear-gradient(135deg, #7c3aed, #a855f7);">★ জনপ্রিয়</span>
                </div>
                @endif
                <div class="mb-3">
                    <p class="text-2xl font-black" style="background: linear-gradient(135deg, {{ $c1 }}, {{ $c2 }}); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $speed }}<span class="text-sm">Mbps</span></p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $package->validity ?? '30' }} দিন মেয়াদ</p>
                </div>
                <p class="text-gray-500 text-xs mb-2 leading-tight">{{ $package->name }}</p>
                <p class="text-white text-lg font-black">৳{{ number_format($price) }}</p>
            </div>
            @endforeach
        </div>

        <form action="{{ route('captive.payment') }}" method="GET" id="packageForm">
            <input type="hidden" name="mac" value="{{ $clientMac }}">
            <input type="hidden" name="package_id" id="selectedPackageId" value="">
            <input type="hidden" name="type" value="preset">
            <button type="submit" disabled id="packageSubmitBtn" class="cta-btn w-full py-3.5 font-bold text-sm">
                <span id="pkgBtnText">প্যাকেজ সিলেক্ট করুন</span>
            </button>
        </form>
        @else
        <div class="glass rounded-2xl p-10 text-center">
            <p id="noPackageText" class="text-gray-600 text-sm">কোনো প্যাকেজ পাওয়া যায়নি</p>
        </div>
        @endif
    </div>

    {{-- TAB: CUSTOM BUILDER --}}
    <div id="customSection" class="tab-content hidden">
        <div class="glass-strong rounded-2xl p-5">
            <h3 id="customTitle" class="text-sm font-bold text-white mb-5 text-center">🛠️ কাস্টম প্যাকেজ বিল্ডার</h3>

            <div class="mb-5">
                <div class="flex justify-between items-center mb-3">
                    <span id="speedLabel" class="text-xs text-gray-500 font-semibold uppercase tracking-wider">স্পীড</span>
                    <span id="customSpeedValue" class="speed-text text-xl font-black">10 Mbps</span>
                </div>
                <input type="range" min="5" max="100" value="10" step="5" class="slider" id="customSpeedSlider" oninput="updateCustomPrice()">
                <div class="flex justify-between text-xs text-gray-700 mt-2"><span>5 Mbps</span><span>100 Mbps</span></div>
            </div>

            <div class="mb-5">
                <div class="flex justify-between items-center mb-3">
                    <span id="durationLabel" class="text-xs text-gray-500 font-semibold uppercase tracking-wider">মেয়াদ</span>
                    <span id="customDaysValue" class="text-white text-xl font-black">15 দিন</span>
                </div>
                <input type="range" min="5" max="90" value="15" step="5" class="slider" id="customDaysSlider" oninput="updateCustomPrice()">
                <div class="flex justify-between text-xs text-gray-700 mt-2"><span>5 দিন</span><span>90 দিন</span></div>
            </div>

            <div class="mb-5">
                <p id="deviceLabel" class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-3">ডিভাইস সংখ্যা</p>
                <div class="grid grid-cols-4 gap-2">
                    @foreach([['📱','১টি',1],['📱📱','২টি',2],['💻📱','৩টি',3],['📱×4','৪টি',4]] as [$icon,$lbl,$n])
                    <button type="button" onclick="selectDevice({{ $n }}, this)"
                            class="dev-btn py-3 text-center text-xs font-semibold {{ $n===1 ? 'active' : '' }}">
                        <div class="text-base mb-0.5">{{ $icon }}</div>{{ $lbl }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl p-4 mb-4" style="background:rgba(0,0,0,0.3);">
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span id="basePriceLabel">বেস প্রাইস</span><span id="basePriceValue">৳0</span>
                    </div>
                    <div class="flex justify-between text-emerald-400">
                        <span id="discountLabel">ডিসকাউন্ট</span><span id="discountValue">-৳0</span>
                    </div>
                    <div class="border-t border-white/5"></div>
                    <div class="flex justify-between items-center">
                        <span id="totalLabel" class="font-bold text-white">মোট</span>
                        <span id="totalPriceValue" class="text-2xl font-black text-emerald-400">৳150</span>
                    </div>
                    <p id="perDayPrice" class="text-right text-xs text-gray-600">প্রতিদিন ৳10</p>
                </div>
            </div>

            <button onclick="submitCustomPackage()" class="cta-btn w-full py-3.5 font-bold text-sm">
                <span id="customSubmitBtn">কাস্টম প্যাকেজ নিন →</span>
            </button>
        </div>
    </div>

    {{-- VOUCHER LOGIN --}}
    <div class="glass rounded-2xl p-5 mt-5">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 text-center flex items-center justify-center gap-2">
            <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <span id="voucherTitle">ভাউচার দিয়ে লগইন</span>
        </h3>
        <form action="{{ $router->hotspot_url ?? 'http://192.168.88.1/login' }}" method="POST" class="space-y-2.5">
            <input type="hidden" name="dst" value="">
            <input type="hidden" name="popup" value="true">
            <input type="text" name="username" placeholder="ইউজারনেম / Username"
                   class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500/40 transition">
            <input type="password" name="password" placeholder="পাসওয়ার্ড / Password"
                   class="w-full px-4 py-3 glass rounded-xl text-white placeholder-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500/40 transition">
            <button type="submit" class="cta-btn w-full py-3 font-bold text-sm">
                <span id="loginBtn">লগইন করুন</span>
            </button>
        </form>
    </div>

    {{-- FOOTER --}}
    <p class="text-center text-xs text-gray-700 mt-6">{{ $template->footer_text ?? '© SKYNITY WiFi — সুরক্ষিত সংযোগ' }}</p>
</div>

<script>
if ('serviceWorker' in navigator) navigator.serviceWorker.register('/sw.js');

function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    ['Trial','Packages','Custom'].forEach(t => {
        const btn = document.getElementById('tab' + t);
        btn.classList.remove('active');
        btn.classList.add('text-gray-500');
    });
    document.getElementById(tab + 'Section').classList.remove('hidden');
    const active = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
    active.classList.add('active');
    active.classList.remove('text-gray-500');
}

let selectedPackageId = null;
function selectPackage(id, price, name, el) {
    document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedPackageId = id;
    const btn = document.getElementById('packageSubmitBtn');
    btn.disabled = false;
    document.getElementById('selectedPackageId').value = id;
    document.getElementById('pkgBtnText').textContent = translations[currentLang].paymentBtn + ' — ৳' + Number(price).toLocaleString();
}

let selectedDevices = 1;
function selectDevice(n, el) {
    document.querySelectorAll('.dev-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    selectedDevices = n;
    updateCustomPrice();
}

function updateCustomPrice() {
    const speed = parseInt(document.getElementById('customSpeedSlider').value);
    const days  = parseInt(document.getElementById('customDaysSlider').value);
    const mult  = [1, 1.5, 2, 2.5][selectedDevices - 1];
    const base  = speed * 2 * days * mult;
    const disc  = days >= 60 ? 30 : days >= 30 ? 20 : days >= 15 ? 10 : 0;
    const save  = Math.round(base * disc / 100);
    const total = Math.round(base - save);
    const t = translations[currentLang];

    document.getElementById('customSpeedValue').textContent = speed + ' Mbps';
    document.getElementById('customDaysValue').textContent = days + ' ' + t.daysText;
    document.getElementById('basePriceValue').textContent = '৳' + Math.round(base).toLocaleString();
    document.getElementById('discountValue').textContent = disc ? '-৳' + save.toLocaleString() + ' (' + disc + '%)' : '-৳0';
    document.getElementById('totalPriceValue').textContent = '৳' + total.toLocaleString();
    document.getElementById('perDayPrice').textContent = t.perDayText + ' ৳' + Math.round(total / days);
}

function submitCustomPackage() {
    const speed = document.getElementById('customSpeedSlider').value;
    const days  = document.getElementById('customDaysSlider').value;
    const total = document.getElementById('totalPriceValue').textContent.replace(/[৳,\s]/g, '');
    window.location.href = '/captive/payment?' + new URLSearchParams({ type:'custom', speed, days, devices: selectedDevices, price: total, mac: '{{ $clientMac }}' });
}

let currentLang = 'bn';
const translations = {
    bn: {
        tabTrialText:'🎁 ট্রায়াল', tabPackagesText:'📦 প্যাকেজ', tabCustomText:'⚙️ কাস্টম',
        trialTitle:'৫ দিন ফ্রি ট্রায়াল!', trialSubtitle:'নতুন ইউজারদের জন্য • 10 Mbps',
        nameLabel:'আপনার নাম', phoneLabel:'মোবাইল নম্বর',
        trialBtn:'ফ্রি ট্রায়াল নিন →', trialNote:'এডমিন অ্যাপ্রুভ করলে WiFi চালু হবে',
        packageTitle:'প্যাকেজ বেছে নিন', pkgBtnText:'প্যাকেজ সিলেক্ট করুন',
        paymentBtn:'পেমেন্ট করুন', noPackageText:'কোনো প্যাকেজ পাওয়া যায়নি',
        customTitle:'🛠️ কাস্টম প্যাকেজ বিল্ডার', speedLabel:'স্পীড', durationLabel:'মেয়াদ',
        deviceLabel:'ডিভাইস সংখ্যা', basePriceLabel:'বেস প্রাইস', discountLabel:'ডিসকাউন্ট',
        totalLabel:'মোট', perDayText:'প্রতিদিন', daysText:'দিন',
        customSubmitBtn:'কাস্টম প্যাকেজ নিন →', voucherTitle:'ভাউচার দিয়ে লগইন', loginBtn:'লগইন করুন',
    },
    en: {
        tabTrialText:'🎁 Trial', tabPackagesText:'📦 Packages', tabCustomText:'⚙️ Custom',
        trialTitle:'5 Days Free Trial!', trialSubtitle:'10 Mbps for new users',
        nameLabel:'Your Name', phoneLabel:'Phone Number',
        trialBtn:'Get Free Trial →', trialNote:'WiFi activates after admin approval',
        packageTitle:'Select a Package', pkgBtnText:'Select Package',
        paymentBtn:'Pay', noPackageText:'No packages available',
        customTitle:'🛠️ Custom Package Builder', speedLabel:'Speed', durationLabel:'Duration',
        deviceLabel:'Devices', basePriceLabel:'Base Price', discountLabel:'Discount',
        totalLabel:'Total', perDayText:'Per day', daysText:'Days',
        customSubmitBtn:'Get Custom Package →', voucherTitle:'Login with Voucher', loginBtn:'Login',
    }
};

function changeLanguage(lang) {
    currentLang = lang;
    const t = translations[lang];
    Object.keys(t).forEach(k => { const el = document.getElementById(k); if (el) el.textContent = t[k]; });
    updateCustomPrice();
    localStorage.setItem('skynity_lang', lang);
}

document.addEventListener('DOMContentLoaded', () => {
    const lang = localStorage.getItem('skynity_lang') || 'bn';
    document.getElementById('langSelect').value = lang;
    changeLanguage(lang);
    updateCustomPrice();
});
</script>
</body>
</html>
