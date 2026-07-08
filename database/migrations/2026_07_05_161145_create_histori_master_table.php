<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histori_master', function (Blueprint $table) {
            $table->id();
            
            // ID aktivitas yang diubah
            $table->unsignedBigInteger('aktivitas_id');
            
            // Jenis aktivitas: 'rutin' atau 'proyek'
            $table->enum('jenis_aktivitas', ['rutin', 'proyek']);
            
            // Field yang diubah
            $table->string('field')->nullable();
            
            // Nilai lama dan baru
            $table->text('nilai_lama')->nullable();
            $table->text('nilai_baru')->nullable();
            
            // Aksi yang dilakukan: 'create', 'update', 'delete'
            $table->enum('aksi', ['create', 'update', 'delete']);
            
            // User yang melakukan perubahan
            $table->foreignId('diubah_oleh')->constrained('users');
            
            // Catatan tambahan (opsional)
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index(['aktivitas_id', 'jenis_aktivitas']);
            $table->index('diubah_oleh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_master');
    }
};