<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ট্রায়াল পেন্ডিং - SKYNITY WiFi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: {{ $template->background_color ?? 'linear-gradient(180deg, #0f172a 0%, #1e293b 100%)' }}; min-height: 100vh; }
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.5; }
            100% { transform: scale(0.8); opacity: 1; }
        }
    </style>
</head>
<body class="text-white flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full text-center">
        <!-- Pending Animation -->
        <div class="relative inline-block mb-8">
            <div class="w-32 h-32 rounded-full bg-yellow-500/20 flex items-center justify-center pulse-ring">
                <div class="w-24 h-24 rounded-full bg-yellow-500/30 flex items-center justify-center">
                    <svg class="w-12 h-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold mb-2" style="font-size: {{ $template->heading_font_size ?? 24 }}px;">ট্রায়াল রিকোয়েস্ট পেন্ডিং!</h1>
        <p class="text-slate-400 mb-8">এডমিন অ্যাপ্রুভ করলে WiFi অটোমেটিক চালু হবে</p>

        <!-- Request Details -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5 mb-6 text-left" style="border-radius: {{ $template->package_card_radius ?? 16 }}px;">
            <h3 class="text-center font-semibold mb-4 text-green-400">📋 আপনার রিকোয়েস্ট</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-slate-400">রিকোয়েস্ট ID</span>
                    <span class="text-white font-mono">#{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">নাম</span>
                    <span class="text-white">{{ $request->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">মোবাইল</span>
                    <span class="text-white">{{ $request->customer_phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">ট্রায়াল</span>
                    <span class="text-green-400 font-semibold">৫ দিন • 10 Mbps</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">স্ট্যাটাস</span>
                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm">পেন্ডিং</span>
                </div>
            </div>
        </div>

        <!-- What happens next -->
        <div class="bg-blue-900/30 border border-blue-500/30 rounded-xl p-4 mb-6 text-left" style="border-radius: {{ $template->package_card_radius ?? 16 }}px;">
            <h4 class="font-semibold mb-3 text-blue-300">📌 এরপর কি হবে?</h4>
            <ul class="text-sm text-slate-300 space-y-2">
                <li class="flex items-start">
                    <span class="text-green-400 mr-2">✓</span>
                    এডমিন আপনার রিকোয়েস্ট রিভিউ করবে
                </li>
                <li class="flex items-start">
                    <span class="text-green-400 mr-2">✓</span>
                    অ্যাপ্রুভ হলে WiFi অটোমেটিক কানেক্ট হবে
                </li>
                <li class="flex items-start">
                    <span class="text-green-400 mr-2">✓</span>
                    SMS/নোটিফিকেশন পাবেন লগইন তথ্য সহ
                </li>
            </ul>
        </div>

        <!-- Auto Refresh Status -->
        <div class="text-sm text-slate-500 mb-4">
            <span id="countdown">30</span> সেকেন্ড পর স্ট্যাটাস চেক হবে...
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
            <a href="{{ route('captive.index') }}" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-xl font-semibold transition" style="border-radius: {{ $template->button_radius ?? 12 }}px;">
                ফিরে যান
            </a>
            <button onclick="checkStatus()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition" style="border-radius: {{ $template->button_radius ?? 12 }}px;">
                স্ট্যাটাস চেক
            </button>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-xs text-slate-500">
            SKYNITY WiFi • সুরক্ষিত সংযোগ
        </div>
    </div>

    <script>
        const requestId = {{ $request->id }};
        let countdown = 30;

        // Countdown timer
        setInterval(() => {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            if (countdown <= 0) {
                countdown = 30;
                checkStatus();
            }
        }, 1000);

        async function checkStatus() {
            try {
                const response = await fetch(`/captive/status/${requestId}`);
                const data = await response.json();

                if (data.status === 'approved') {
                    window.location.href = data.redirect;
                } else if (data.status === 'rejected') {
                    alert('দুঃখিত! আপনার রিকোয়েস্ট বাতিল হয়েছে।\n' + (data.message || ''));
                    window.location.href = '/captive';
                }
            } catch (error) {
                console.error('Status check failed:', error);
            }
        }
    </script>
</body>
</html>
