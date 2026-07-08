@extends('layouts.app')

@section('title', 'Approval Usulan Aktivitas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">✅ Approval Usulan Aktivitas</h1>
                
                <h2 class="text-xl font-semibold mt-6 mb-3">📋 Usulan Aktivitas Rutin</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">Nama Aktivitas</th>
                                <th class="border px-4 py-2 text-left">Periode</th>
                                <th class="border px-4 py-2 text-left">Bobot</th>
                                <th class="border px-4 py-2 text-left">Diusulkan Oleh</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingRutin ?? [] as $rutin)
                            <tr>
                                <td class="border px-4 py-2">{{ $rutin->nama_aktivitas }}</td>
                                <td class="border px-4 py-2">{{ $rutin->periode }}</td>
                                <td class="border px-4 py-2">{{ $rutin->bobot }}</td>
                                <td class="border px-4 py-2">{{ $rutin->creator->name ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <form method="POST" action="{{ route('approval.approve', [$rutin->id, 'rutin']) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">✅ Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('approval.reject', [$rutin->id, 'rutin']) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">❌ Reject</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="border px-4 py-8 text-center text-gray-500">Tidak ada usulan pending</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h2 class="text-xl font-semibold mt-6 mb-3">🚀 Usulan Proyek</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">Nama Proyek</th>
                                <th class="border px-4 py-2 text-left">Tanggal Mulai</th>
                                <th class="border px-4 py-2 text-left">Tanggal Berakhir</th>
                                <th class="border px-4 py-2 text-left">Bobot</th>
                                <th class="border px-4 py-2 text-left">Diusulkan Oleh</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingProyek ?? [] as $proyek)
                            <tr>
                                <td class="border px-4 py-2">{{ $proyek->nama_proyek }}</td>
                                <td class="border px-4 py-2">{{ $proyek->tgl_mulai }}</td>
                                <td class="border px-4 py-2">{{ $proyek->tgl_berakhir }}</td>
                                <td class="border px-4 py-2">{{ $proyek->bobot }}</td>
                                <td class="border px-4 py-2">{{ $proyek->creator->name ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <form method="POST" action="{{ route('approval.approve', [$proyek->id, 'proyek']) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">✅ Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('approval.reject', [$proyek->id, 'proyek']) }}" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">❌ Reject</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-8 text-center text-gray-500">Tidak ada usulan pending</td>
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