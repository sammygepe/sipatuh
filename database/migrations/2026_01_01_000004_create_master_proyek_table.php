<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_proyek', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek', 255);
            $table->date('tgl_mulai');
            $table->date('tgl_berakhir');
            $table->date('tgl_selesai')->nullable();
            $table->decimal('bobot', 5, 2)->default(50.00);
            $table->foreignId('departemen_id')->constrained('departemen');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected', 'selesai'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_proyek');
    }
};