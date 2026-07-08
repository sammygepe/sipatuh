@extends('layouts.app')

@section('title', 'Edit Aktivitas Rutin')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">✏️ Edit Aktivitas Rutin</h1>

                <form method="POST" action="{{ route('master.update', [$aktivitas->id, 'rutin']) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Nama Aktivitas</label>
                        <input type="text" name="nama_aktivitas" 
                               value="{{ old('nama_aktivitas', $aktivitas->nama_aktivitas) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('nama_aktivitas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Periode</label>
                        <select name="periode" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="daily" {{ $aktivitas->periode == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $aktivitas->periode == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $aktivitas->periode == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
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