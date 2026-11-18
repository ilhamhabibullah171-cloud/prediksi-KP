<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook', function (Blueprint $table) {
            $table->id('id_logbook');
            $table->unsignedBigInteger('id_kp');
            $table->integer('hari_ke'); // 1 to 30
            $table->date('tanggal_kegiatan');
            $table->text('deskripsi_pekerjaan');
            $table->string('dokumentasi')->nullable(); // Foto/berkas kegiatan
            $table->enum('status_validasi', ['Pending', 'Approved', 'Revisi'])->default('Pending');
            $table->text('catatan_dosen')->nullable();
            $table->timestamp('waktu_submit')->nullable();
            $table->timestamps();

            $table->foreign('id_kp')->references('id_kp')->on('kerja_praktik')->onDelete('cascade');
            $table->unique(['id_kp', 'hari_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook');
    }
};

