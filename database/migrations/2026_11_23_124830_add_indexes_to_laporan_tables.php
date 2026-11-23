<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->index(['ditangani_oleh', 'status'], 'laporans_ditangani_status_index');
        });

        Schema::table('tanggapans', function (Blueprint $table) {
            $table->index('laporan_id', 'tanggapans_laporan_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanggapans', function (Blueprint $table) {
            $table->dropIndex('tanggapans_laporan_id_index');
        });

        Schema::table('laporans', function (Blueprint $table) {
            $table->dropIndex('laporans_ditangani_status_index');
        });
    }
};
