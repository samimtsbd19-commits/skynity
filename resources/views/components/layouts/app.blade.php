<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SKYNITY WiFi') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                    },
                },
            },
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #1e1b4b; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 4px; }

        /* Gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(160deg, #0f0c29 0%, #1e1b4b 40%, #312e81 100%);
        }
        .gradient-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        }

        /* Card hover */
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.3);
        }

        /* Sidebar transition */
        .sidebar-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Nav item active glow */
        .nav-active {
            background: rgba(99, 102, 241, 0.25);
            border-left: 3px solid #818cf8;
            box-shadow: inset 0 0 20px rgba(99,102,241,0.1);
        }
        .nav-item:hover {
            background: rgba(99, 102, 241, 0.15);
        }

        /* Bottom nav active */
        .bottom-nav-active {
            color: #818cf8;
        }
        .bottom-nav-active svg {
            filter: drop-shadow(0 0 6px rgba(129,140,248,0.6));
        }

        /* Pulse dot */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse-dot { animation: pulse-dot 2s infinite; }

        /* Logo glow */
        .logo-glow {
            filter: drop-shadow(0 0 12px rgba(129,140,248,0.5));
        }

        /* Mobile safe area */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen" x-data="{ sidebarOpen: false, moreOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- ================================
             MOBILE OVERLAY BACKDROP
        ================================ -->
        <div
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-20 md:hidden"
            style="display:none"
        ></div>

        <!-- ================================
             SIDEBAR
        ================================ -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed md:relative md:translate-x-0 inset-y-0 left-0 z-30
                   flex flex-col w-72 md:w-64 gradient-bg border-r border-indigo-900/60
                   sidebar-transition md:flex flex-shrink-0 shadow-2xl md:shadow-none"
        >
            <!-- ---- Logo ---- -->
            <div class="flex items-center justify-between h-16 px-5 border-b border-indigo-900/50">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <!-- SVG Logo Icon -->
                    <div class="relative flex-shrink-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg logo-glow group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1.5 8.5C5.5 4.5 10.5 2.5 12 2.5s6.5 2 10.5 6"/>
                                <path d="M5 12c1.9-1.9 4.1-2.9 7-2.9s5.1 1 7 2.9"/>
                                <path d="M8.5 15.5c1-1 2.1-1.5 3.5-1.5s2.5.5 3.5 1.5"/>
                                <circle cx="12" cy="19" r="1.5" fill="currentColor" stroke="none"/>
                            </svg>
                        </div>
                        <!-- Live dot -->
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 rounded-full border-2 border-indigo-900 pulse-dot"></span>
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight">
                            <span class="text-white">SKY</span><span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">NITY</span>
                        </span>
                        <p class="text-[10px] text-indigo-400 -mt-1 font-medium tracking-widest uppercase">WiFi Manager</p>
                    </div>
                </a>
                <!-- Close button (mobile only) -->
                <button @click="sidebarOpen = false" class="md:hidden p-1.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- ---- Navigation ---- -->
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

                <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('dashboard') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span data-lang="dashboard">ড্যাশবোর্ড</span>
                </a>

                <a href="{{ route('routers') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('routers') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('routers') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                    <span data-lang="routers">রাউটার</span>
                </a>

                <a href="{{ route('packages') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('packages') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('packages') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span data-lang="packages">প্যাকেজ</span>
                </a>

                <!-- Section: Voucher -->
                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest" data-lang="vouchers">ভাউচার</p>
                </div>

                <a href="{{ route('vouchers.generate') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('vouchers.generate') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('vouchers.generate') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span data-lang="generate">জেনারেট করুন</span>
                </a>

                <a href="{{ route('vouchers.list') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('vouchers.list') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('vouchers.list') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span data-lang="list">তালিকা</span>
                </a>

                <!-- Section: MikroTik -->
                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest" data-lang="mikrotik">MikroTik</p>
                </div>

                <a href="{{ route('sessions') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('sessions') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('sessions') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span data-lang="sessions">অ্যাক্টিভ সেশন</span>
                </a>

                <a href="{{ route('hotspot.users') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('hotspot.users') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('hotspot.users') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span data-lang="hotspotUsers">হটস্পট ইউজার</span>
                </a>

                <a href="{{ route('ip.binding') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('ip.binding') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('ip.binding') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span data-lang="ipBinding">IP Binding</span>
                </a>

                <a href="{{ route('profiles') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('profiles') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('profiles') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span data-lang="profileSync">প্রোফাইল সিঙ্ক</span>
                </a>

                <a href="{{ route('traffic') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('traffic') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('traffic') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span data-lang="traffic">ট্রাফিক মনিটর</span>
                </a>

                <a href="{{ route('stock.users') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('stock.users') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('stock.users') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span data-lang="stockUsers">স্টক ইউজার</span>
                    @php $stockCount = \App\Models\StockUser::where('status', 'available')->count(); @endphp
                    @if($stockCount > 0)
                    <span class="ml-auto bg-emerald-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stockCount }}</span>
                    @endif
                </a>

                <!-- Section: Captive Portal -->
                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest" data-lang="captivePortal">Captive Portal</p>
                </div>

                <a href="{{ route('hotspot.requests') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('hotspot.requests') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('hotspot.requests') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span data-lang="requests">Requests</span>
                    @php $pendingCount = \App\Models\HotspotRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="ml-auto bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('templates') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('templates') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('templates') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <span data-lang="templateEditor">Template Editor</span>
                </a>

                <!-- Section: Reports -->
                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest" data-lang="reports">Reports</p>
                </div>

                <a href="{{ route('reports.sales') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('reports.*') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('reports.*') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span data-lang="salesReport">Sales Report</span>
                </a>

                <!-- Section: System -->
                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest" data-lang="system">System</p>
                </div>

                <a href="{{ route('settings') }}" @click="sidebarOpen = false"
                   class="nav-item flex items-center px-3 py-2.5 text-gray-300 rounded-xl transition-all text-sm font-medium {{ request()->routeIs('settings') ? 'nav-active text-indigo-300' : '' }}">
                    <svg class="w-4.5 h-4.5 mr-3 flex-shrink-0 {{ request()->routeIs('settings') ? 'text-indigo-400' : 'text-gray-500' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span data-lang="settings">Settings</span>
                </a>
            </nav>

            <!-- ---- User Footer ---- -->
            <div class="p-4 border-t border-indigo-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow">
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-200 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-indigo-400 truncate">{{ auth()->user()->role ?? 'admin' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ================================
             MAIN CONTENT AREA
        ================================ -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            <!-- Top Bar -->
            <header class="bg-gray-900/95 backdrop-blur border-b border-gray-800 h-14 md:h-16 flex items-center justify-between px-4 md:px-6 flex-shrink-0 sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <!-- Hamburger (mobile) -->
                    <button @click="sidebarOpen = true" class="md:hidden p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <!-- Logo (mobile only, small) -->
                    <div class="md:hidden flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1.5 8.5C5.5 4.5 10.5 2.5 12 2.5s6.5 2 10.5 6"/>
                                <path d="M5 12c1.9-1.9 4.1-2.9 7-2.9s5.1 1 7 2.9"/>
                                <path d="M8.5 15.5c1-1 2.1-1.5 3.5-1.5s2.5.5 3.5 1.5"/>
                                <circle cx="12" cy="19" r="1.5" fill="currentColor" stroke="none"/>
                            </svg>
                        </div>
                        <span class="text-sm font-black"><span class="text-white">SKY</span><span class="text-indigo-400">NITY</span></span>
                    </div>
                    <!-- Page Title (desktop) -->
                    <h1 class="hidden md:block text-base font-semibold text-gray-200">@yield('title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-2 md:gap-3">
                    <!-- Language Switcher -->
                    <select id="adminLangSelect" onchange="changeAdminLanguage(this.value)"
                            class="bg-gray-800 text-white text-xs md:text-sm px-2 md:px-3 py-1.5 rounded-lg border border-gray-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="bn">বাং</option>
                        <option value="en">EN</option>
                    </select>

                    <!-- Notifications -->
                    @livewire('admin-notifications')

                    <!-- Date (desktop only) -->
                    <span class="hidden lg:block text-xs text-gray-500 bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-700">{{ now()->format('d M, Y') }}</span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-950 p-4 md:p-6 pb-20 md:pb-6">
                <!-- Flash Messages -->
                @if (session()->has('message'))
                    <div class="mb-4 p-4 bg-emerald-900/40 border border-emerald-700/50 text-emerald-300 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-900/40 border border-red-700/50 text-red-300 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- ================================
         MOBILE BOTTOM NAVIGATION
    ================================ -->
    <nav class="fixed bottom-0 left-0 right-0 bg-gray-900/98 backdrop-blur-lg border-t border-gray-800 md:hidden z-10 pb-safe">
        <div class="flex justify-around items-center h-16 px-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 {{ request()->routeIs('dashboard') ? 'bottom-nav-active' : 'text-gray-500 hover:text-gray-300' }} transition-colors">
                <svg class="w-5 h-5" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-medium">হোম</span>
            </a>
            <!-- Vouchers -->
            <a href="{{ route('vouchers.list') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 {{ request()->routeIs('vouchers.*') ? 'bottom-nav-active' : 'text-gray-500 hover:text-gray-300' }} transition-colors">
                <svg class="w-5 h-5" fill="{{ request()->routeIs('vouchers.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <span class="text-[10px] font-medium">ভাউচার</span>
            </a>
            <!-- Sessions -->
            <a href="{{ route('sessions') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 {{ request()->routeIs('sessions') ? 'bottom-nav-active' : 'text-gray-500 hover:text-gray-300' }} transition-colors">
                <svg class="w-5 h-5" fill="{{ request()->routeIs('sessions') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-[10px] font-medium">সেশন</span>
            </a>
            <!-- Hotspot Users -->
            <a href="{{ route('hotspot.users') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 {{ request()->routeIs('hotspot.users') ? 'bottom-nav-active' : 'text-gray-500 hover:text-gray-300' }} transition-colors">
                <svg class="w-5 h-5" fill="{{ request()->routeIs('hotspot.users') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-[10px] font-medium">ইউজার</span>
            </a>
            <!-- More (opens sidebar) -->
            <button @click="sidebarOpen = true" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-2 text-gray-500 hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="text-[10px] font-medium">আরও</span>
            </button>
        </div>
    </nav>

    @livewireScripts

    <script>
        // Admin Language Translations
        const adminTranslations = {
            bn: {
                dashboard: 'ড্যাশবোর্ড', routers: 'রাউটার', packages: 'প্যাকেজ',
                vouchers: 'ভাউচার', generate: 'জেনারেট করুন', list: 'তালিকা',
                mikrotik: 'MikroTik', sessions: 'অ্যাক্টিভ সেশন', hotspotUsers: 'হটস্পট ইউজার',
                ipBinding: 'IP Binding', profileSync: 'প্রোফাইল সিঙ্ক', traffic: 'ট্রাফিক মনিটর',
                stockUsers: 'স্টক ইউজার', captivePortal: 'ক্যাপটিভ পোর্টাল', requests: 'রিকোয়েস্ট',
                templateEditor: 'টেমপ্লেট এডিটর', reports: 'রিপোর্ট', salesReport: 'বিক্রয় রিপোর্ট',
                system: 'সিস্টেম', settings: 'সেটিংস',
            },
            en: {
                dashboard: 'Dashboard', routers: 'Routers', packages: 'Packages',
                vouchers: 'Vouchers', generate: 'Generate', list: 'List',
                mikrotik: 'MikroTik', sessions: 'Active Sessions', hotspotUsers: 'Hotspot Users',
                ipBinding: 'IP Binding', profileSync: 'Profile Sync', traffic: 'Traffic Monitor',
                stockUsers: 'Stock Users', captivePortal: 'Captive Portal', requests: 'Requests',
                templateEditor: 'Template Editor', reports: 'Reports', salesReport: 'Sales Report',
                system: 'System', settings: 'Settings',
            }
        };

        function changeAdminLanguage(lang) {
            const t = adminTranslations[lang];
            document.querySelectorAll('[data-lang]').forEach(el => {
                const key = el.getAttribute('data-lang');
                if (t[key]) el.textContent = t[key];
            });
            localStorage.setItem('skynity_admin_lang', lang);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('skynity_admin_lang') || 'bn';
            const langSelect = document.getElementById('adminLangSelect');
            if (langSelect) {
                langSelect.value = savedLang;
                changeAdminLanguage(savedLang);
            }
        });
    </script>
</body>
</html>
