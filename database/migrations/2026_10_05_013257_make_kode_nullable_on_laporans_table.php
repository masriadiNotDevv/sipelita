<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kode laporan dibuat dari kolom `id` yang dijamin unik oleh auto-increment,
     * sehingga tidak ada lagi risiko kode ganda saat dua laporan dibuat bersamaan.
     * Kolom dibuat nullable sementara karena kode hanya dapat diisi setelah baris
     * tersimpan dan memperoleh nilai `id`.
     */
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->string('kode')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->string('kode')->nullable(false)->change();
        });
    }
};
