@extends('layouts.app')

@section('title', 'Semua KPI User')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📊 Semua KPI User</h1>
                <p class="text-gray-600 mb-6">Admin dapat melihat KPI semua user</p>

                {{-- FILTER FORM --}}
                <form method="GET" action="{{ route('admin.kpi.semua') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Filter Periode --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Periode</label>
                            <select name="bulan" class="w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($daftarPeriode as $value => $label)
                                    <option value="{{ $value }}" {{ $bulan == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filter Departemen --}}
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
                        
                        {{-- Filter Nama --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama User</label>
                            <input type="text" name="nama" value="{{ $cariNama }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm" 
                                   placeholder="Cari nama...">
                        </div>
                        
                        {{-- Tombol --}}
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                🔍 Filter
                            </button>
                            <a href="{{ route('admin.kpi.semua') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                ↻ Reset
                            </a>
                            <a href="{{ route('admin.kpi.export', request()->all()) }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                📥 Export
                            </a>
                        </div>
                    </div>
                </form>

                {{-- TABEL HASIL --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">#</th>
                                <th class="border px-4 py-2 text-left">Nama</th>
                                <th class="border px-4 py-2 text-left">Departemen</th>
                                <th class="border px-4 py-2 text-left">Role</th>
                                <th class="border px-4 py-2 text-left">Nilai Auto</th>
                                <th class="border px-4 py-2 text-left">Grade Auto</th>
                                <th class="border px-4 py-2 text-left">Nilai Akhir</th>
                                <th class="border px-4 py-2 text-left">Grade Akhir</th>
                                <th class="border px-4 py-2 text-left">Catatan Atasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataKpi as $index => $kpi)
                            <tr>
                                <td class="border px-4 py-2">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2 font-medium">{{ $kpi['nama'] }}</td>
                                <td class="border px-4 py-2">{{ $kpi['departemen'] }}</td>
                                <td class="border px-4 py-2">
                                    @if($kpi['role'] == 'Admin')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">👑 Admin</span>
                                    @elseif($kpi['role'] == 'Atasan')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">👔 Atasan</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">👤 Staff</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">{{ number_format($kpi['nilai_auto'], 2) }}</td>
                                <td class="border px-4 py-2">
                                    @include('components.grade-badge', ['grade' => $kpi['kategori']])
                                </td>
                                <td class="border px-4 py-2">{{ number_format($kpi['nilai_akhir'], 2) }}</td>
                                <td class="border px-4 py-2">
                                    @include('components.grade-badge', ['grade' => $kpi['kategori_akhir']])
                                </td>
                                <td class="border px-4 py-2 max-w-xs">
                                    {{ Str::limit($kpi['catatan'], 50) ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="border px-4 py-8 text-center text-gray-500">
                                    Tidak ada data user yang ditemukan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(!empty($cariNama) || !empty($departemenId))
                <div class="mt-4 text-sm text-gray-500">
                    Menampilkan hasil filter: 
                    @if(!empty($departemenId)) Departemen: {{ $departemenList->find($departemenId)->nama ?? '-' }} @endif
                    @if(!empty($cariNama)) Nama: "{{ $cariNama }}" @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection