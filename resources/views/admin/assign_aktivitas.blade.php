@extends('layouts.app')

@section('title', 'Assign Aktivitas ke User')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">🎯 Assign Aktivitas ke User</h1>
                <p class="text-gray-600 mb-6">Admin dapat membuat aktivitas dan langsung menugaskan ke user tertentu tanpa perlu approval.</p>

                <form method="POST" action="{{ route('admin.assign.store') }}">
                    @csrf

                    {{-- Jenis Aktivitas --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Jenis Aktivitas</label>
                        <select name="jenis" id="jenis" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Jenis</option>
                            <option value="rutin">📋 Aktivitas Rutin</option>
                            <option value="proyek">🚀 Aktivitas Proyek</option>
                        </select>
                    </div>

                    {{-- Nama Aktivitas --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Nama Aktivitas / Proyek</label>
                        <input type="text" name="nama" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Form Rutin --}}
                    <div id="form_rutin" style="display: none;">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Periode</label>
                            <select name="periode" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="daily">📅 Harian (Daily)</option>
                                <option value="weekly">📆 Mingguan (Weekly)</option>
                                <option value="monthly">🗓️ Bulanan (Monthly)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Form Proyek --}}
                    <div id="form_proyek" style="display: none;">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Tanggal Berakhir</label>
                            <input type="date" name="tgl_berakhir" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    {{-- Bobot --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Bobot (0-100)</label>
                        <input type="number" name="bobot" class="w-full border-gray-300 rounded-md shadow-sm" step="1" min="0" max="100">
                        <p class="text-sm text-gray-500 mt-1">Kosongkan untuk default (rutin=10, proyek=50)</p>
                    </div>

                    {{-- PILIH USER / DEPARTEMEN --}}
                    <div class="mb-4 p-4 bg-gray-50 rounded-md">
                        <label class="block text-gray-700 font-medium mb-2">👥 Tugaskan Kepada</label>
                        
                        <div class="mb-3">
                            <label class="inline-flex items-center">
                                <input type="radio" name="assign_type" value="specific_user" class="mr-2" checked> Tugas ke user tertentu
                            </label>
                            <label class="inline-flex items-center ml-4">
                                <input type="radio" name="assign_type" value="department" class="mr-2"> Tugas ke seluruh departemen
                            </label>
                        </div>

                        {{-- Pilih user tertentu --}}
                        <div id="user_select">
                            <label class="block text-sm font-medium mb-1">Pilih User</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->departemen->nama ?? '-' }})
                                        @if($user->is_admin) - Admin @elseif($user->is_atasan) - Atasan @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih departemen --}}
                        <div id="department_select" style="display: none;">
                            <label class="block text-sm font-medium mb-1">Pilih Departemen</label>
                            <select name="departemen_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Departemen</option>
                                @foreach($departemen as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">*Aktivitas akan diberikan ke SEMUA user di departemen ini</p>
                        </div>
                    </div>

                    <div class="bg-green-50 p-3 rounded-md mb-4">
                        <p class="text-green-700 text-sm">🔓 Sebagai Admin, aktivitas akan langsung <strong>APPROVED</strong> dan langsung terlihat oleh user yang dipilih.</p>
                    </div>

                    <button type="submit" style="background-color: #22c55e; color: white; padding: 10px 24px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                        📤 Assign & Kirim
                    </button>
                    <a href="{{ route('dashboard') }}" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle form rutin/proyek
    document.getElementById('jenis').addEventListener('change', function() {
        const jenis = this.value;
        document.getElementById('form_rutin').style.display = jenis === 'rutin' ? 'block' : 'none';
        document.getElementById('form_proyek').style.display = jenis === 'proyek' ? 'block' : 'none';
    });

    // Toggle assign type
    const assignTypeRadios = document.querySelectorAll('input[name="assign_type"]');
    const userSelectDiv = document.getElementById('user_select');
    const departmentSelectDiv = document.getElementById('department_select');

    assignTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'specific_user') {
                userSelectDiv.style.display = 'block';
                departmentSelectDiv.style.display = 'none';
            } else {
                userSelectDiv.style.display = 'none';
                departmentSelectDiv.style.display = 'block';
            }
        });
    });
</script>
@endsection