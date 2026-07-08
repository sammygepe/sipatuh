<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('aktivitas_rutin_id')->nullable()->constrained('master_aktivitas_rutin')->onDelete('cascade');
            $table->foreignId('proyek_id')->nullable()->constrained('master_proyek')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['belum', 'progress', 'selesai'])->default('belum');
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas_harian');
    }
};