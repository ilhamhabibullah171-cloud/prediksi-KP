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
        Schema::table('kerja_praktik', function (Blueprint $table) {
            $table->string('judul_kp', 255)->nullable()->after('alamat_instansi');
            $table->string('kontak_instansi', 100)->nullable()->after('judul_kp');
            $table->string('berkas_pendaftaran', 255)->nullable()->after('kontak_instansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerja_praktik', function (Blueprint $table) {
            $table->dropColumn(['judul_kp', 'kontak_instansi', 'berkas_pendaftaran']);
        });
    }
};
