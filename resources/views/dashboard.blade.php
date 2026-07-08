@extends('layouts.app')

@section('title', 'Dashboard Pencatatan Harian')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-2">📋 SIPATUH - Pencatatan Aktivitas Harian</h1>
                <p class="text-gray-600 mb-4">Halo, <strong>{{ Auth::user()->name }}</strong> | Tanggal: <strong>{{ date('d-m-Y') }}</strong></p>

                <hr class="my-4">

                <form method="POST" action="{{ route('log.simpan') }}">
                    @csrf

                    {{-- AKTIVITAS RUTIN --}}
                    <h2 class="text-xl font-semibold mt-4 mb-3">📋 Aktivitas Rutin</h2>
                    @forelse($rutinHariIni as $rutin)
                    <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <strong>{{ $rutin->nama_aktivitas }}</strong>
                                <p class="text-sm text-gray-500">Bobot: {{ $rutin->bobot }} | {{ $rutin->periode }}</p>
                            </div>
                            <div>
                                <select name="status_rutin[{{ $rutin->id }}]" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="belum" {{ ($logRutin[$rutin->id]->status ?? '') == 'belum' ? 'selected' : '' }}>⭕ Belum Mulai</option>
                                    <option value="progress" {{ ($logRutin[$rutin->id]->status ?? '') == 'progress' ? 'selected' : '' }}>🔄 On Progress</option>
                                    <option value="selesai" {{ ($logRutin[$rutin->id]->status ?? '') == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <textarea name="detail_rutin[{{ $rutin->id }}]" class="w-full border-gray-300 rounded-md shadow-sm" rows="2" placeholder="Detail pekerjaan...">{{ $logRutin[$rutin->id]->detail ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-100 rounded-lg p-4 text-center text-gray-500 mb-3">
                        Tidak ada aktivitas rutin untuk hari ini.
                    </div>
                    @endforelse

                    {{-- AKTIVITAS PROYEK --}}
                    <h2 class="text-xl font-semibold mt-6 mb-3">🚀 Aktivitas Proyek</h2>
                    @forelse($proyekAktif as $proyek)
                    <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <strong>{{ $proyek->nama_proyek }}</strong>
                                <p class="text-sm text-gray-500">
                                    Bobot: {{ $proyek->bobot }} | 
                                    Deadline: {{ \Carbon\Carbon::parse($proyek->tgl_berakhir)->format('d-m-Y') }}
                                </p>
                            </div>
                            <div>
                                <select name="status_proyek[{{ $proyek->id }}]" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="belum" {{ ($logProyek[$proyek->id]->status ?? '') == 'belum' ? 'selected' : '' }}>⭕ Belum Mulai</option>
                                    <option value="progress" {{ ($logProyek[$proyek->id]->status ?? '') == 'progress' ? 'selected' : '' }}>🔄 On Progress</option>
                                    <option value="selesai" {{ ($logProyek[$proyek->id]->status ?? '') == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <textarea name="detail_proyek[{{ $proyek->id }}]" class="w-full border-gray-300 rounded-md shadow-sm" rows="2" placeholder="Detail pekerjaan...">{{ $logProyek[$proyek->id]->detail ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-100 rounded-lg p-4 text-center text-gray-500 mb-3">
                        Tidak ada aktivitas proyek aktif.
                    </div>
                    @endforelse

                    {{-- TOMBOL SIMPAN --}}
                    <div class="mt-6">
                        <button type="submit" style="background-color: #22c55e; color: white; padding: 10px 24px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                            💾 Simpan Aktivitas Hari Ini
                        </button>
                    </div>
                    
                </form>

                {{-- Debug info (hapus setelah selesai) --}}
                <p class="text-xs text-gray-400 mt-4">
                    Debug: {{ $rutinHariIni->count() }} rutin, {{ $proyekAktif->count() }} proyek
                </p>
            </div>
        </div>
    </div>
</div>
@endsection