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

                {{-- AKTIVITAS RUTIN --}}
                <h2 class="text-xl font-semibold mt-6 mb-3">📋 Aktivitas Rutin</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">ID</th>
                                <th class="border px-4 py-2 text-left">Nama Aktivitas</th>
                                <th class="border px-4 py-2 text-left">Periode</th>
                                <th class="border px-4 py-2 text-left">Bobot</th>
                                <th class="border px-4 py-2 text-left">Departemen</th>
                                <th class="border px-4 py-2 text-left">Dikerjakan Oleh</th>  <!-- TAMBAHKAN -->
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
                                <td class="border px-4 py-2">{{ $item->user->name ?? 'Belum ditugaskan' }}</td>  <!-- TAMBAHKAN -->
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

                {{-- PROYEK --}}
                <h2 class="text-xl font-semibold mt-6 mb-3">🚀 Proyek</h2>
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
                                <th class="border px-4 py-2 text-left">Dikerjakan Oleh</th>  <!-- TAMBAHKAN -->
                                <th class="border px-4 py-2 text-left">Status</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proyek as $item)
                            <tr>
                                <td class="border px-4 py-2">{{ $item->id }}</td>
                                <td class="border px-4 py-2 font-medium">{{ $item->nama_proyek }}</td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y') }}</td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($item->tgl_berakhir)->format('d-m-Y') }}</td>
                                <td class="border px-4 py-2">{{ $item->bobot }}</td>
                                <td class="border px-4 py-2">{{ $item->departemen->nama ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $item->user->name ?? 'Belum ditugaskan' }}</td>  <!-- TAMBAHKAN -->
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
                                <td colspan="9" class="border px-4 py-8 text-center text-gray-500">
                                    Tidak ada proyek
                                </td>
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