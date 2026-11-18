<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id('id_sertifikat');
            $table->unsignedBigInteger('id_kp');
            $table->string('file_path');
            $table->date('tanggal_upload');
            $table->enum('status_verifikasi', ['Menunggu', 'Valid', 'Ditolak'])->default('Menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('id_kp')->references('id_kp')->on('kerja_praktik')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};

