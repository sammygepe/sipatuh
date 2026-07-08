@extends('layouts.app')

@section('title', 'Edit Proyek')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">✏️ Edit Proyek</h1>

                <form method="POST" action="{{ route('master.update', [$aktivitas->id, 'proyek']) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Nama Proyek</label>
                        <input type="text" name="nama_proyek" 
                               value="{{ old('nama_proyek', $aktivitas->nama_proyek) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('nama_proyek')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" 
                               value="{{ old('tgl_mulai', $aktivitas->tgl_mulai ? \Carbon\Carbon::parse($aktivitas->tgl_mulai)->format('Y-m-d') : '') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('tgl_mulai')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Tanggal Berakhir</label>
                        <input type="date" name="tgl_berakhir" 
                               value="{{ old('tgl_berakhir', $aktivitas->tgl_berakhir ? \Carbon\Carbon::parse($aktivitas->tgl_berakhir)->format('Y-m-d') : '') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('tgl_berakhir')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Bobot (0-100)</label>
                        <input type="number" name="bobot" 
                               value="{{ old('bobot', $aktivitas->bobot) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" 
                               step="1" min="0" max="100" required>
                        @error('bobot')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            💾 Simpan Perubahan
                        </button>
                        <a href="{{ route('master.aktivitas') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection