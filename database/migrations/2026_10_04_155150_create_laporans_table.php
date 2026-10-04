<?php

use App\Enums\PrioritasLaporan;
use App\Enums\StatusLaporan;
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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fasilitas_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->string('prioritas')->default(PrioritasLaporan::Sedang->value);
            $table->string('status')->default(StatusLaporan::Menunggu->value);
            $table->string('bukti_foto')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'prioritas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
