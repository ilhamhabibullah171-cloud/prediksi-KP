<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_prediksi_ml', function (Blueprint $table) {
            $table->id('id_prediksi');
            $table->unsignedBigInteger('id_kp');
            $table->float('rasio_kepatuhan_logbook')->default(0);
            $table->integer('jarak_hari_sertifikat')->default(0);
            $table->integer('jumlah_hari_terlewat')->default(0);
            $table->enum('label_prediksi', ['Tepat Waktu', 'Terlambat', 'Gagal'])->default('Tepat Waktu');
            $table->string('status_code', 20)->default('AMAN'); // AMAN, RENTAN, BAHAYA
            $table->float('probabilitas_sukses')->default(0); // e.g., 85.5%
            $table->text('detail_probabilitas')->nullable(); // JSON formatted probabilities
            $table->text('rekomendasi_ews')->nullable();
            $table->timestamp('tanggal_update_prediksi')->useCurrent();
            $table->timestamps();

            $table->foreign('id_kp')->references('id_kp')->on('kerja_praktik')->onDelete('cascade');
            $table->index('id_kp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_prediksi_ml');
    }
};

