@extends('layouts.app')

@section('title', 'Usulkan Aktivitas Baru')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">➕ Usulkan Aktivitas Baru</h1>
                <p class="text-gray-600 mb-6">Usulan akan dikirim ke atasan untuk persetujuan.</p>

                {{-- TAMPILKAN ERROR JIKA ADA --}}
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- TAMPILKAN DATA YANG DIKIRIM (DEBUG) --}}
                @if(old())
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
                        <strong>Data yang dikirim sebelumnya:</strong>
                        <pre class="mt-2 text-sm">{{ json_encode(old(), JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

                <form method="POST" action="{{ route('usul.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Jenis Aktivitas</label>
                        <select name="jenis" id="jenis" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Jenis</option>
                            <option value="rutin" {{ old('jenis') == 'rutin' ? 'selected' : '' }}>📋 Aktivitas Rutin</option>
                            <option value="proyek" {{ old('jenis') == 'proyek' ? 'selected' : '' }}>🚀 Aktivitas Proyek</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Nama Aktivitas / Proyek</label>
                        <input type="text" name="nama" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ old('nama') }}" required>
                    </div>

                    <div id="form_rutin" style="display: {{ old('jenis') == 'rutin' ? 'block' : 'none' }};">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Periode</label>
                            <select name="periode" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="daily" {{ old('periode') == 'daily' ? 'selected' : '' }}>📅 Harian (Daily)</option>
                                <option value="weekly" {{ old('periode') == 'weekly' ? 'selected' : '' }}>📆 Mingguan (Weekly)</option>
                                <option value="monthly" {{ old('periode') == 'monthly' ? 'selected' : '' }}>🗓️ Bulanan (Monthly)</option>
                            </select>
                        </div>
                    </div>

                    <div id="form_proyek" style="display: {{ old('jenis') == 'proyek' ? 'block' : 'none' }};">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ old('tgl_mulai') }}">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">Tanggal Berakhir</label>
                            <input type="date" name="tgl_berakhir" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ old('tgl_berakhir') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Bobot (0-100)</label>
                        <input type="number" name="bobot" class="w-full border-gray-300 rounded-md shadow-sm" step="1" min="0" max="100" value="{{ old('bobot') }}" placeholder="Kosongkan untuk default (rutin=10, proyek=50)">
                        <p class="text-sm text-gray-500 mt-1">Bobot mempengaruhi nilai KPI. Semakin besar bobot, semakin besar pengaruhnya.</p>
                    </div>

                    @if(Auth::user()->is_admin)
                        <div class="bg-green-50 p-3 rounded-md mb-4">
                            <p class="text-green-700 text-sm">🔓 Sebagai Admin, aktivitas akan langsung <strong>APPROVED</strong> tanpa perlu persetujuan.</p>
                        </div>
                    @endif

                    <button type="submit" style="background-color: #22c55e; color: white; padding: 10px 24px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer;">
                        📤 Kirim Usulan
                    </button>
                    <a href="{{ route('dashboard') }}" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('jenis').addEventListener('change', function() {
        const jenis = this.value;
        document.getElementById('form_rutin').style.display = jenis === 'rutin' ? 'block' : 'none';
        document.getElementById('form_proyek').style.display = jenis === 'proyek' ? 'block' : 'none';
    });
</script>
@endsection