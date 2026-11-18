<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kerja_praktik', function (Blueprint $table) {
            $table->id('id_kp');
            $table->string('nim', 30);
            $table->string('nidn', 30)->nullable();
            $table->string('instansi', 200);
            $table->text('alamat_instansi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_kp', ['Pengajuan', 'Berjalan', 'Selesai', 'Batal'])->default('Pengajuan');
            $table->string('usulan_dosen', 150)->nullable();
            $table->text('catatan_koordinator')->nullable();
            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('nidn')->references('nidn')->on('dosen')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kerja_praktik');
    }
};

