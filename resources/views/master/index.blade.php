@extends('layouts.app')

@section('title', 'Kelola Master Aktivitas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📋 Kelola Master Aktivitas</h1>
                <p class="text-gray-600 mb-6">Edit atau nonaktifkan aktivitas rutin & proyek</p>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- TAB NAVIGATION --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-4">
                        <a href="{{ route('master.aktivitas', ['tab' => 'rutin']) }}"
                           class="py-2 px-4 border-b-2 {{ $tab == 'rutin' ? 'border-blue-500 text-blue-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            📋 Aktivitas Rutin
                        </a>
                        <a href="{{ route('master.aktivitas', ['tab' => 'proyek']) }}"
                           class="py-2 px-4 border-b-2 {{ $tab == 'proyek' ? 'border-blue-500 text-blue-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            🚀 Proyek
                        </a>
                    </nav>
                </div>

                {{-- ================= TAB RUTIN ================= --}}
                @if($tab == 'rutin')
                    <form method="GET" action="{{ route('master.aktivitas') }}" class="mb-4">
                        <input type="hidden" name="tab" value="rutin">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Cari Nama</label>
                                <input type="text" name="search_rutin" value="{{ $searchRutin }}"
                                       placeholder="Nama aktivitas..."
                                       class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Periode</label>
                                <select name="periode" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua Periode</option>
                                    <option value="daily"   {{ $periode == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly"  {{ $periode == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ $periode == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                            {{-- ← KOLOM BARU: Dikerjakan Oleh --}}
                            <div>
                                <label class="block text-sm font-medium mb-1">Dikerjakan Oleh</label>
                                <select name="user_rutin" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua User</option>
                                    @foreach($userList as $u)
                                        <option value="{{ $u->id }}" {{ $userRutin == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Status</label>
                                <select name="status_rutin" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="approved" {{ $statusRutin == 'approved' ? 'selected' : '' }}>Aktif</option>
                                    <option value="pending"  {{ $statusRutin == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="rejected" {{ $statusRutin == 'rejected' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            @if(Auth::user()->is_admin)
                            <div>
                                <label class="block text-sm font-medium mb-1">Departemen</label>
                                <select name="departemen_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua Departemen</option>
                                    @foreach($departemenList as $dept)
                                        <option value="{{ $dept->id }}" {{ $departemenId == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="flex items-end gap-2">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">🔍 Filter</button>
                                <a href="{{ route('master.aktivitas', ['tab' => 'rutin']) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">↻ Reset</a>
                                <a href="{{ route('master.export.rutin', request()->query()) }}"
                                   class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    📥 Export
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 text-left">ID</th>
                                    <th class="border px-4 py-2 text-left">Nama Aktivitas</th>
                                    <th class="border px-4 py-2 text-left">Periode</th>
                                    <th class="border px-4 py-2 text-left">Bobot</th>
                                    <th class="border px-4 py-2 text-left">Departemen</th>
                                    <th class="border px-4 py-2 text-left">Dikerjakan Oleh</th>
                                    <th class="border px-4 py-2 text-left">Status</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rutin as $item)
                                <tr>
                                    <td class="border px-4 py-2">{{ $item->id }}</td>
                                    <td class="border px-4 py-2 font-medium">{{ $item->nama_aktivitas }}</td>
                                    <td class="border px-4 py-2">{{ $item->periode }}</td>
                                    <td class="border px-4 py-2">{{ $item->bobot }}</td>
                                    <td class="border px-4 py-2">{{ $item->departemen->nama ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $item->user->name ?? 'Belum ditugaskan' }}</td>
                                    <td class="border px-4 py-2">
                                        @if($item->status == 'approved')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                                        @elseif($item->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('master.edit', [$item->id, 'rutin']) }}"
                                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm inline-block">
                                            ✏️ Edit
                                        </a>
                                        @if($item->status == 'approved')
                                        <form action="{{ route('master.delete', [$item->id, 'rutin']) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('Nonaktifkan aktivitas {{ $item->nama_aktivitas }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                                🗑️ Nonaktifkan
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="border px-4 py-8 text-center text-gray-500">
                                        Tidak ada aktivitas rutin
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $rutin->appends(request()->query())->links() }}
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        Menampilkan {{ $rutin->firstItem() ?? 0 }} - {{ $rutin->lastItem() ?? 0 }} dari {{ $rutin->total() }} data
                    </div>
                @endif

                {{-- ================= TAB PROYEK ================= --}}
                @if($tab == 'proyek')
                    <form method="GET" action="{{ route('master.aktivitas') }}" class="mb-4">
                        <input type="hidden" name="tab" value="proyek">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Cari Nama</label>
                                <input type="text" name="search_proyek" value="{{ $searchProyek }}"
                                       placeholder="Nama proyek..."
                                       class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            {{-- ← KOLOM BARU: Dikerjakan Oleh --}}
                            <div>
                                <label class="block text-sm font-medium mb-1">Dikerjakan Oleh</label>
                                <select name="user_proyek" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua User</option>
                                    @foreach($userList as $u)
                                        <option value="{{ $u->id }}" {{ $userProyek == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Status</label>
                                <select name="status_proyek" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="approved" {{ $statusProyek == 'approved' ? 'selected' : '' }}>Aktif</option>
                                    <option value="pending"  {{ $statusProyek == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="rejected" {{ $statusProyek == 'rejected' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Status Proyek</label>
                                <select name="status_proyek_akhir" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua</option>
                                    <option value="belum"    {{ $statusProyekAkhir == 'belum' ? 'selected' : '' }}>⭕ Belum</option>
                                    <option value="progress" {{ $statusProyekAkhir == 'progress' ? 'selected' : '' }}>🔄 Progress</option>
                                    <option value="selesai"  {{ $statusProyekAkhir == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                </select>
                            </div>
                            @if(Auth::user()->is_admin)
                            <div>
                                <label class="block text-sm font-medium mb-1">Departemen</label>
                                <select name="departemen_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua Departemen</option>
                                    @foreach($departemenList as $dept)
                                        <option value="{{ $dept->id }}" {{ $departemenId == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="flex items-end gap-2">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">🔍 Filter</button>
                                <a href="{{ route('master.aktivitas', ['tab' => 'proyek']) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">↻ Reset</a>
                                <a href="{{ route('master.export.proyek', request()->query()) }}"
                                   class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    📥 Export
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 text-left">ID</th>
                                    <th class="border px-4 py-2 text-left">Nama Proyek</th>
                                    <th class="border px-4 py-2 text-left">Tgl Mulai</th>
                                    <th class="border px-4 py-2 text-left">Tgl Berakhir</th>
                                    <th class="border px-4 py-2 text-left">Bobot</th>
                                    <th class="border px-4 py-2 text-left">Departemen</th>
                                    <th class="border px-4 py-2 text-left">Dikerjakan Oleh</th>
                                    <th class="border px-4 py-2 text-left">Status Proyek</th>
                                    <th class="border px-4 py-2 text-left">Status</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proyek as $item)
                                @php
                                    $log = $logTerakhir[$item->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="border px-4 py-2">{{ $item->id }}</td>
                                    <td class="border px-4 py-2 font-medium">{{ $item->nama_proyek }}</td>
                                    <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y') }}</td>
                                    <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($item->tgl_berakhir)->format('d-m-Y') }}</td>
                                    <td class="border px-4 py-2">{{ $item->bobot }}</td>
                                    <td class="border px-4 py-2">{{ $item->departemen->nama ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $item->user->name ?? 'Belum ditugaskan' }}</td>
                                    <td class="border px-4 py-2">
                                        @if($log)
                                            @if($log->status == 'selesai')
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">✅ Selesai</span>
                                                @if($item->tgl_selesai)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d-m-Y') }}
                                                    </div>
                                                @endif
                                            @elseif($log->status == 'progress')
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">🔄 Progress</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">⭕ Belum</span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum ada log</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">
                                        @if($item->status == 'approved')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                                        @elseif($item->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('master.edit', [$item->id, 'proyek']) }}"
                                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm inline-block">
                                            ✏️ Edit
                                        </a>
                                        @if($item->status == 'approved')
                                        <form action="{{ route('master.delete', [$item->id, 'proyek']) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('Nonaktifkan proyek {{ $item->nama_proyek }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                                🗑️ Nonaktifkan
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="border px-4 py-8 text-center text-gray-500">
                                        Tidak ada proyek
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $proyek->appends(request()->query())->links() }}
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        Menampilkan {{ $proyek->firstItem() ?? 0 }} - {{ $proyek->lastItem() ?? 0 }} dari {{ $proyek->total() }} data
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection