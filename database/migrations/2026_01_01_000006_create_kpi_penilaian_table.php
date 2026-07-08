<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('bulan', 7);
            $table->decimal('nilai_total', 5, 2);
            $table->char('kategori', 1);
            $table->text('catatan_atasan')->nullable();
            $table->foreignId('dinilai_oleh')->constrained('users');
            $table->timestamps();
            $table->unique(['user_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_penilaian');
    }
};