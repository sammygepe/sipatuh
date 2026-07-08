<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->after('email')->constrained('departemen');
            $table->boolean('is_atasan')->default(false)->after('departemen_id');
            $table->boolean('is_admin')->default(false)->after('is_atasan');
            $table->foreignId('atasan_id')->nullable()->after('is_admin')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropForeign(['atasan_id']);
            $table->dropColumn(['departemen_id', 'is_atasan', 'is_admin', 'atasan_id']);
        });
    }
};