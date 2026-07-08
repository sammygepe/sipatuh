@extends('layouts.app')

@section('title', 'Kelola Departemen')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">🏢 Kelola Departemen</h1>
                    <a href="{{ route('admin.departemen.create') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        ➕ Tambah Departemen
                    </a>
                </div>

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

                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">ID</th>
                                <th class="border px-4 py-2 text-left">Nama Departemen</th>
                                <th class="border px-4 py-2 text-left">Bobot Default</th>
                                <th class="border px-4 py-2 text-left">Jumlah User</th>
                                <th class="border px-4 py-2 text-left">Dibuat</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departemen as $d)
                            <tr>
                                <td class="border px-4 py-2">{{ $d->id }}</td>
                                <td class="border px-4 py-2 font-medium">{{ $d->nama }}</td>
                                <td class="border px-4 py-2">{{ $d->bobot_default }}%</td>
                                <td class="border px-4 py-2">{{ $d->users()->count() }} user</td>
                                <td class="border px-4 py-2">{{ $d->created_at->format('d-m-Y') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('admin.departemen.edit', $d->id) }}" 
                                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm inline-block">
                                        ✏️ Edit
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-8 text-center text-gray-500">
                                    Tidak ada data departemen
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