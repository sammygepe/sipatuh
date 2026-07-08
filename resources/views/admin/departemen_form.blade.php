@extends('layouts.app')

@section('title', isset($departemen) ? 'Edit Departemen' : 'Tambah Departemen')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">
                    {{ isset($departemen) ? '✏️ Edit Departemen' : '➕ Tambah Departemen' }}
                </h1>

                <form method="POST" action="{{ isset($departemen) ? route('admin.departemen.update', $departemen->id) : route('admin.departemen.store') }}">
                    @csrf
                    @if(isset($departemen))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Nama Departemen</label>
                        <input type="text" name="nama" 
                               value="{{ old('nama', $departemen->nama ?? '') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm @error('nama') border-red-500 @enderror" 
                               required>
                        @error('nama')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Bobot Default (%)</label>
                        <input type="number" name="bobot_default" 
                               value="{{ old('bobot_default', $departemen->bobot_default ?? 100) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm @error('bobot_default') border-red-500 @enderror" 
                               step="1" min="0" max="100" required>
                        <p class="text-sm text-gray-500 mt-1">Bobot maksimal untuk departemen ini (0-100)</p>
                        @error('bobot_default')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            💾 Simpan
                        </button>
                        <a href="{{ route('admin.departemen') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection