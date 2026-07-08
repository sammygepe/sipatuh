@extends('layouts.app')

@section('title', 'Laporan KPI Saya')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">📊 Laporan KPI - {{ Auth::user()->name }}</h1>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">Bulan</th>
                                <th class="border px-4 py-2 text-left">Nilai KPI</th>
                                <th class="border px-4 py-2 text-left">Grade</th>
                                <th class="border px-4 py-2 text-left">Catatan Atasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporan ?? [] as $item)
                            <tr>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($item['bulan'] . '-01')->format('F Y') }}</td>
                                <td class="border px-4 py-2">{{ $item['nilai'] }}</td>
                                <td class="border px-4 py-2">
                                    @if($item['kategori'] == 'A')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">A (Sangat Baik)</span>
                                    @elseif($item['kategori'] == 'B')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">B (Baik)</span>
                                    @elseif($item['kategori'] == 'C')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">C (Cukup)</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded">D (Kurang)</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">{{ $item['catatan'] ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="border px-4 py-8 text-center text-gray-500">Belum ada data laporan KPI</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection