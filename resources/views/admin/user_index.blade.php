@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">👥 Kelola User</h1>
                    <a href="{{ route('admin.user.create') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        ➕ Tambah User
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">ID</th>
                                <th class="border px-4 py-2 text-left">Nama</th>
                                <th class="border px-4 py-2 text-left">Email</th>
                                <th class="border px-4 py-2 text-left">Departemen</th>
                                <th class="border px-4 py-2 text-left">Role</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="border px-4 py-2">{{ $user->id }}</td>
                                <td class="border px-4 py-2 font-medium">{{ $user->name }}</td>
                                <td class="border px-4 py-2">{{ $user->email }}</td>
                                <td class="border px-4 py-2">{{ $user->departemen->nama ?? '-' }}</td>
                                <td class="border px-4 py-2">
                                    @if($user->is_admin)
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">👑 Admin</span>
                                    @elseif($user->is_atasan)
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">👔 Atasan</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">👤 Staff</span>
                                    @endif
                                </span>
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('admin.user.edit', $user->id) }}" 
                                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm inline-block">
                                        ✏️ Edit
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-8 text-center text-gray-500">
                                    Tidak ada data user
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