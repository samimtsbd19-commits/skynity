<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SKYNITY WiFi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        .bg-mesh {
            background-color: #060414;
            background-image:
                radial-gradient(ellipse 80% 60% at 20% 20%, rgba(99,102,241,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 80%, rgba(168,85,247,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 50% 50%, rgba(59,130,246,0.08) 0%, transparent 70%);
        }

        /* Animated grid */
        .grid-lines {
            background-image:
                linear-gradient(rgba(99,102,241,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.06) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* Glow ring on card */
        .card-glow {
            box-shadow:
                0 0 0 1px rgba(99,102,241,0.2),
                0 25px 60px rgba(0,0,0,0.6),
                0 0 40px rgba(99,102,241,0.08);
        }

        /* Logo pulse */
        @keyframes logo-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
            50% { box-shadow: 0 0 0 12px rgba(99,102,241,0); }
        }
        .logo-pulse { animation: logo-pulse 3s ease-in-out infinite; }

        /* Input focus glow */
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(99,102,241,0.25);
        }

        /* Signal rings animation */
        @keyframes ring-expand {
            0% { transform: scale(0.6); opacity: 0.8; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        .ring-1 { animation: ring-expand 2.5s ease-out infinite; }
        .ring-2 { animation: ring-expand 2.5s ease-out 0.8s infinite; }
        .ring-3 { animation: ring-expand 2.5s ease-out 1.6s infinite; }

        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%);
            background-size: 200% 200%;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background-position: right center;
            box-shadow: 0 8px 25px rgba(99,102,241,0.4);
            transform: translateY(-1px);
        }
        .btn-gradient:active { transform: translateY(0); }
    </style>
</head>
<body class="bg-mesh grid-lines min-h-screen flex items-center justify-center p-4">

    <!-- Decorative signal rings (background) -->
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <div class="absolute inset-0 w-96 h-96 -translate-x-1/2 -translate-y-1/2 rounded-full border border-indigo-500/10 ring-1"></div>
        <div class="absolute inset-0 w-72 h-72 -translate-x-1/2 -translate-y-1/2 rounded-full border border-indigo-500/10 ring-2"></div>
        <div class="absolute inset-0 w-48 h-48 -translate-x-1/2 -translate-y-1/2 rounded-full border border-indigo-500/10 ring-3"></div>
    </div>

    <div class="w-full max-w-sm relative z-10">

        <!-- ===== Logo Section ===== -->
        <div class="text-center mb-8">
            <!-- Icon with rings -->
            <div class="relative inline-flex items-center justify-center mb-5">
                <!-- Outer glow rings -->
                <div class="absolute w-24 h-24 rounded-full bg-indigo-500/10 animate-ping" style="animation-duration: 3s;"></div>
                <!-- Icon container -->
                <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-700 flex items-center justify-center logo-pulse shadow-2xl">
                    <!-- WiFi SVG Icon -->
                    <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1.5 8.5C5.5 4.5 10.5 2.5 12 2.5s6.5 2 10.5 6"/>
                        <path d="M5 12c1.9-1.9 4.1-2.9 7-2.9s5.1 1 7 2.9"/>
                        <path d="M8.5 15.5c1-1 2.1-1.5 3.5-1.5s2.5.5 3.5 1.5"/>
                        <circle cx="12" cy="19" r="1.8" fill="currentColor" stroke="none"/>
                    </svg>
                    <!-- Shine overlay -->
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-t from-transparent to-white/10"></div>
                </div>
            </div>

            <!-- Brand name -->
            <h1 class="text-4xl font-black tracking-tight mb-1">
                <span class="text-white">SKY</span><span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">NITY</span>
            </h1>
            <p class="text-gray-500 text-sm font-medium tracking-widest uppercase">WiFi Management System</p>
        </div>

        <!-- ===== Login Card ===== -->
        <div class="bg-gray-900/80 backdrop-blur-xl rounded-2xl card-glow p-7">

            <div class="mb-6 text-center">
                <h2 class="text-lg font-bold text-gray-100">স্বাগতম!</h2>
                <p class="text-gray-500 text-sm mt-0.5">অ্যাডমিন অ্যাকাউন্টে লগইন করুন</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-sm flex items-start gap-2.5">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="input-glow w-full bg-gray-800/60 border border-gray-700 text-gray-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:border-indigo-500 transition placeholder-gray-600"
                            placeholder="admin@skynity.com">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5 uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" required
                            class="input-glow w-full bg-gray-800/60 border border-gray-700 text-gray-200 rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:border-indigo-500 transition placeholder-gray-600"
                            placeholder="••••••••••">
                    </div>
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember"
                               class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-indigo-600 focus:ring-indigo-500 focus:ring-offset-gray-900">
                        <span class="text-sm text-gray-400">মনে রাখুন</span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="btn-gradient w-full text-white font-bold py-3 px-6 rounded-xl text-sm mt-2 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    লগইন করুন
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-700 text-xs mt-6">
            © {{ date('Y') }} SKYNITY WiFi · MikroTik Hotspot Management
        </p>
    </div>

</body>
</html>
