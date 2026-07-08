@extends('layouts.app')

@section('title', 'Penilaian KPI Bawahan')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📈 Penilaian KPI Bawahan</h1>
                <p class="text-gray-600 mb-4">Bulan: <strong>{{ \Carbon\Carbon::parse($bulan . '-01')->format('F Y') }}</strong></p>
                
                <form method="POST" action="{{ route('kpi.simpan') }}">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 text-left">Nama Bawahan</th>
                                    <th class="border px-4 py-2 text-left">Nilai KPI (Auto)</th>
                                    <th class="border px-4 py-2 text-left">Nilai Akhir</th>
                                    <th class="border px-4 py-2 text-left">Grade</th>
                                    <th class="border px-4 py-2 text-left">Catatan</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bawahan ?? [] as $user)
                                <tr>
                                    <td class="border px-4 py-2">{{ $user->name }}</td>
                                    <td class="border px-4 py-2">{{ $hitungKPI[$user->id]['nilai'] ?? 0 }}</td>
                                    <td class="border px-4 py-2">
                                        <input type="number" name="nilai[{{ $user->id }}]" class="w-20 border rounded px-2 py-1" step="0.01" value="{{ $existing[$user->id]->nilai_total ?? ($hitungKPI[$user->id]['nilai'] ?? 0) }}">
                                    </td>
                                    <td class="border px-4 py-2">
                                        <select name="kategori[{{ $user->id }}]" class="border rounded px-2 py-1">
                                            <option value="A" {{ ($existing[$user->id]->kategori ?? ($hitungKPI[$user->id]['kategori'] ?? '')) == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ ($existing[$user->id]->kategori ?? ($hitungKPI[$user->id]['kategori'] ?? '')) == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ ($existing[$user->id]->kategori ?? ($hitungKPI[$user->id]['kategori'] ?? '')) == 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ ($existing[$user->id]->kategori ?? ($hitungKPI[$user->id]['kategori'] ?? '')) == 'D' ? 'selected' : '' }}>D</option>
                                        </select>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <textarea name="catatan[{{ $user->id }}]" class="w-full border rounded px-2 py-1" rows="2" placeholder="Catatan penilaian...">{{ $existing[$user->id]->catatan_atasan ?? '' }}</textarea>
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <button type="submit" name="user_id" value="{{ $user->id }}" class="bg-green-500 px-3 py-1 rounded hover:bg-green-600">💾 Simpan</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="border px-4 py-8 text-center text-gray-500">Tidak ada bawahan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection