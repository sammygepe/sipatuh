@extends('layouts.app')

@section('title', 'Semua Riwayat Aktivitas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📜 Semua Riwayat Aktivitas</h1>
                <p class="text-gray-600 mb-4">Admin dapat melihat riwayat semua user</p>

                <!-- FILTER FORM -->
                <form method="GET" action="{{ route('admin.riwayat.semua') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        {{-- Bulan --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Bulan</label>
                            <select name="bulan" class="w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($daftarBulan as $key => $nama)
                                    <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Tahun --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Tahun</label>
                            <select name="tahun" class="w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($daftarTahun as $thn)
                                    <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>
                                        {{ $thn }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Pilih User (DROPDOWN) --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Pilih User</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Semua User</option>
                                @foreach($userList as $user)
                                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->departemen->nama ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Departemen --}}
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
                        
                        {{-- Filter Status --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Semua Status</option>
                                @foreach($statusList as $key => $label)
                                    <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Tombol --}}
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                🔍 Filter
                            </button>
                            <a href="{{ route('admin.riwayat.semua') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                ↻ Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- TABEL RIWAYAT -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">Tanggal</th>
                                <th class="border px-4 py-2 text-left">User</th>
                                <th class="border px-4 py-2 text-left">Departemen</th>
                                <th class="border px-4 py-2 text-left">Jenis</th>
                                <th class="border px-4 py-2 text-left">Aktivitas</th>
                                <th class="border px-4 py-2 text-left">Status</th>
                                <th class="border px-4 py-2 text-left">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $log)
                            <tr>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}</td>
                                <td class="border px-4 py-2 font-medium">{{ $log->user->name ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $log->user->departemen->nama ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    @if($log->aktivitas_rutin_id)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Rutin</span>
                                    @else
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Proyek</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    @if($log->aktivitas_rutin_id)
                                        {{ $log->aktivitasRutin->nama_aktivitas ?? '-' }}
                                    @else
                                        {{ $log->proyek->nama_proyek ?? '-' }}
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    @if($log->status == 'selesai')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">✅ Selesai</span>
                                    @elseif($log->status == 'progress')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">🔄 Progress</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">⭕ Belum</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">{{ $log->detail ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="border px-4 py-8 text-center text-gray-500">
                                    Tidak ada riwayat aktivitas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="mt-4">
                    {{ $riwayat->appends(request()->query())->links() }}
                </div>
                
                <div class="mt-2 text-sm text-gray-500">
                    Menampilkan {{ $riwayat->firstItem() ?? 0 }} - {{ $riwayat->lastItem() ?? 0 }} dari {{ $riwayat->total() }} data
                </div>
            </div>
        </div>
    </div>
</div>
@endsection