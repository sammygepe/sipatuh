@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">
                    {{ isset($user) ? '✏️ Edit User' : '➕ Tambah User' }}
                </h1>

                <form method="POST" action="{{ isset($user) ? route('admin.user.update', $user->id) : route('admin.user.store') }}">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                        <input type="text" name="name" 
                               value="{{ old('name', $user->name ?? '') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm @error('name') border-red-500 @enderror" 
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" name="email" 
                               value="{{ old('email', $user->email ?? '') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm @error('email') border-red-500 @enderror" 
                               required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            {{ isset($user) ? 'Password (kosongkan jika tidak diubah)' : 'Password' }}
                        </label>
                        <input type="password" name="password" 
                               class="w-full border-gray-300 rounded-md shadow-sm @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Departemen</label>
                        <select name="departemen_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departemen as $d)
                                <option value="{{ $d->id }}" {{ (old('departemen_id', $user->departemen_id ?? '') == $d->id) ? 'selected' : '' }}>
                                    {{ $d->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 flex gap-6">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_atasan" value="1" 
                                   {{ old('is_atasan', $user->is_atasan ?? false) ? 'checked' : '' }}>
                            <span>Jadikan Atasan</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_admin" value="1" 
                                   {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
                            <span>Jadikan Admin</span>
                        </label>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            💾 Simpan
                        </button>
                        <a href="{{ route('admin.user') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection