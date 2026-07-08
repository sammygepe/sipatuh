@extends('layouts.app')

@section('title', 'Assign Aktivitas ke Bawahan')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">🎯 Assign Aktivitas ke Bawahan</h1>
                <p class="text-gray-600 mb-6">Atasan dapat membuat aktivitas dan langsung menugaskan ke bawahan tanpa perlu approval.</p>

                <form method="POST" action="{{ route('atasan.assign.store') }}">
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

                    {{-- PILIH BAWAHAN --}}
                    <div class="mb-4 p-4 bg-gray-50 rounded-md">
                        <label class="block text-gray-700 font-medium mb-2">👥 Tugaskan Kepada Bawahan</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Bawahan</option>
                            @foreach($bawahan as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->departemen->nama ?? '-' }})</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">*Hanya bawahan Anda yang tersedia</p>
                    </div>

                    <div class="bg-green-50 p-3 rounded-md mb-4">
                        <p class="text-green-700 text-sm">🔓 Sebagai Atasan, aktivitas akan langsung <strong>APPROVED</strong> dan langsung terlihat oleh bawahan yang dipilih.</p>
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
</script>
@endsection