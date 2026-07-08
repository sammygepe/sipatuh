<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIPATUH') }} - @yield('title', 'Pencatatan Aktivitas Harian')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left Side - Logo & Menu -->
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-green-700">
                                📋 SIPATUH
                            </a>
                        </div>

                        <!-- Navigation Links Desktop -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('usul.form') }}" 
                               class="{{ request()->routeIs('usul.form') ? 'border-green-400 text-gray-900' : 'border-transparent text-gray-500' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                ➕ Usulkan
                            </a>
                            <a href="{{ route('kpi.laporan') }}" 
                               class="{{ request()->routeIs('kpi.laporan') ? 'border-green-400 text-gray-900' : 'border-transparent text-gray-500' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                📊 KPI
                            </a>
                            <a href="{{ route('riwayat.aktivitas') }}" 
                               class="{{ request()->routeIs('riwayat.aktivitas') ? 'border-green-400 text-gray-900' : 'border-transparent text-gray-500' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                📜 Riwayat
                            </a>
                        </div>
                    </div>

                    <!-- Right Side - User Menu & Admin/Atasan Menu -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        
                        <!-- MENU ATASAN (Dropdown) -->
                        @if(Auth::user()->is_atasan && !Auth::user()->is_admin)
                            <div class="relative ml-3" x-data="{ openAtasan: false }">
                                <button @click="openAtasan = !openAtasan" 
                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center">
                                    👔 Atasan
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="openAtasan" @click.outside="openAtasan = false" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                     style="display: none;">
                                    <a href="{{ route('approval.list') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        ✅ Approve Usulan
                                    </a>
                                    <a href="{{ route('master.aktivitas') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📋 Kelola Master Aktivitas
                                    </a>
                                    <a href="{{ route('atasan.assign.form') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🎯 Assign ke Bawahan
                                    </a>
                                    <a href="{{ route('riwayat.bawahan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📜 Riwayat Bawahan
                                    </a>
                                    <a href="{{ route('kpi.laporan.bawahan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📊 KPI Bawahan (Laporan)
                                    </a>
                                    <a href="{{ route('kpi.bawahan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📈 Nilai KPI Bawahan
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- MENU ADMIN (Dropdown) -->
                        @if(Auth::user()->is_admin)
                            <div class="relative ml-3" x-data="{ openAdmin: false }">
                                <button @click="openAdmin = !openAdmin" 
                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center">
                                    👑 Admin
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="openAdmin" @click.outside="openAdmin = false" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                     style="display: none;">
                                    <a href="{{ route('admin.departemen') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏢 Kelola Departemen
                                    </a>
                                    <a href="{{ route('admin.user') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        👥 Kelola User
                                    </a>
                                    <a href="{{ route('master.aktivitas') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📋 Kelola Master Aktivitas
                                    </a>                                    
                                    <a href="{{ route('admin.assign.form') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🎯 Assign Aktivitas
                                    </a>
                                    <a href="{{ route('admin.riwayat.semua') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📜 Semua Riwayat
                                    </a>
                                    <a href="{{ route('admin.kpi.semua') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📊 Semua KPI
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- NOTIFIKASI DESKTOP -->
                        @php
                            $notifikasi = App\Models\Notifikasi::where('user_id', Auth::id())
                                ->where('is_dibaca', false)
                                ->latest()
                                ->get();
                        @endphp

                        <div class="relative ml-3">
                            <button id="notifBtnDesktop" class="relative text-gray-600 hover:text-gray-900 focus:outline-none">
                                🔔
                                @if($notifikasi->count() > 0)
                                    <span style="position: absolute; top: -8px; right: -8px; background-color: #ef4444; color: white; font-size: 10px; font-weight: bold; border-radius: 9999px; height: 20px; width: 20px; display: flex; align-items: center; justify-content: center;">
                                        {{ $notifikasi->count() }}
                                    </span>
                                @endif
                            </button>

                            <div id="notifDropdownDesktop" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <div class="px-4 py-2 border-b font-semibold text-gray-700">📢 Notifikasi</div>
                                @forelse($notifikasi as $notif)
                                    <a href="{{ route('notifikasi.baca', $notif->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b">
                                        {{ $notif->pesan }}
                                        <div class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <div class="px-4 py-3 text-sm text-gray-500 text-center">Tidak ada notifikasi</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative ml-3" x-data="{ openUser: false }" @click.outside="openUser = false" @close.stop="openUser = false">
                            <div @click="openUser = !openUser" class="flex items-center cursor-pointer">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-600">
                                        <span class="text-sm font-medium leading-none text-white">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    </span>
                                </div>
                                <div class="ml-2">
                                    <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                    <span class="ml-1 text-xs text-gray-500">
                                        @if(Auth::user()->is_admin)
                                            (Admin)
                                        @elseif(Auth::user()->is_atasan)
                                            (Atasan)
                                        @else
                                            (Staff)
                                        @endif
                                    </span>
                                </div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <div x-show="openUser" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚪 Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger Menu (Mobile) -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open" class="sm:hidden" style="display: none;">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('usul.form') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">
                        ➕ Usulkan
                    </a>
                    <a href="{{ route('kpi.laporan') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">
                        📊 KPI
                    </a>
                    <a href="{{ route('riwayat.aktivitas') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">
                        📜 Riwayat
                    </a>

                    <!-- MENU ATASAN (Mobile) -->
                    @if(Auth::user()->is_atasan && !Auth::user()->is_admin)
                        <div class="pl-3 pr-4 py-2">
                            <div class="font-medium text-gray-700 mb-1">👔 Atasan</div>
                            <div class="ml-3 space-y-1">
                                <a href="{{ route('approval.list') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    ✅ Approve Usulan
                                </a>
                                <a href="{{ route('master.aktivitas') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📋 Kelola Master Aktivitas
                                </a>
                                <a href="{{ route('atasan.assign.form') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    🎯 Assign ke Bawahan
                                </a>
                                <a href="{{ route('riwayat.bawahan') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📜 Riwayat Bawahan
                                </a>
                                <a href="{{ route('kpi.laporan.bawahan') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📊 KPI Bawahan (Laporan)
                                </a>
                                <a href="{{ route('kpi.bawahan') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📈 Nilai KPI Bawahan
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- MENU ADMIN (Mobile) -->
                    @if(Auth::user()->is_admin)
                        <div class="pl-3 pr-4 py-2">
                            <div class="font-medium text-gray-700 mb-1">👑 Admin</div>
                            <div class="ml-3 space-y-1">
                                <a href="{{ route('admin.departemen') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    🏢 Kelola Departemen
                                </a>
                                <a href="{{ route('admin.user') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    👥 Kelola User
                                </a>
                                <a href="{{ route('master.aktivitas') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📋 Kelola Master Aktivitas
                                </a>
                                <a href="{{ route('admin.assign.form') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    🎯 Assign Aktivitas
                                </a>
                                <a href="{{ route('admin.riwayat.semua') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📜 Semua Riwayat
                                </a>
                                <a href="{{ route('admin.kpi.semua') }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800">
                                    📊 Semua KPI
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- NOTIFIKASI MOBILE -->
                    @php
                        $notifikasiMobile = App\Models\Notifikasi::where('user_id', Auth::id())
                            ->where('is_dibaca', false)
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    <div class="pl-3 pr-4 py-2">
                        <div class="font-medium text-gray-700 mb-1 flex items-center gap-2">
                            🔔 Notifikasi
                            @if($notifikasiMobile->count() > 0)
                                <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                                    {{ $notifikasiMobile->count() }}
                                </span>
                            @endif
                        </div>
                        <div class="ml-3 space-y-2 max-h-60 overflow-y-auto">
                            @forelse($notifikasiMobile as $notif)
                                <a href="{{ route('notifikasi.baca', $notif->id) }}" class="block py-1 text-sm text-gray-600 hover:text-gray-800 border-b border-gray-100">
                                    {{ $notif->pesan }}
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</div>
                                </a>
                            @empty
                                <div class="py-1 text-sm text-gray-400">Tidak ada notifikasi</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        <div class="text-xs text-gray-400 mt-1">
                            @if(Auth::user()->is_admin)
                                👑 Admin
                            @elseif(Auth::user()->is_atasan)
                                👔 Atasan
                            @else
                                👤 Staff
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                            🚪 Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Alpine.js untuk dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Script untuk Notifikasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifBtnDesktop = document.getElementById('notifBtnDesktop');
            const notifDropdownDesktop = document.getElementById('notifDropdownDesktop');
            
            if (notifBtnDesktop) {
                notifBtnDesktop.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (notifDropdownDesktop) {
                        notifDropdownDesktop.classList.toggle('hidden');
                    }
                });
            }

            document.addEventListener('click', function(event) {
                if (notifBtnDesktop && notifDropdownDesktop && 
                    !notifBtnDesktop.contains(event.target) && 
                    !notifDropdownDesktop.contains(event.target)) {
                    notifDropdownDesktop.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>