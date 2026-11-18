<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->unsignedBigInteger('id_kp')->unique();
            $table->float('nilai_pembimbing_instansi')->nullable();
            $table->float('nilai_dosen_pembimbing')->nullable();
            $table->float('nilai_disiplin')->nullable();
            $table->float('nilai_laporan')->nullable();
            $table->float('nilai_presentasi')->nullable();
            $table->float('nilai_akhir')->nullable();
            $table->string('grade', 5)->nullable();
            $table->text('catatan_evaluasi')->nullable();
            $table->timestamps();

            $table->foreign('id_kp')->references('id_kp')->on('kerja_praktik')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};

