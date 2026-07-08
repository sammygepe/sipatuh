<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_aktivitas_rutin', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aktivitas', 255);
            $table->enum('periode', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->decimal('bobot', 5, 2)->default(10.00);
            $table->foreignId('departemen_id')->constrained('departemen')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_aktivitas_rutin');
    }
};