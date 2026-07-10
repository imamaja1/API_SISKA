<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeder_sync_logs', function (Blueprint $table) {
            $table->string('tipe_sync', 50)->nullable()->after('tipe')->comment('mahasiswa_per_khs/mahasiswa_semua_khs/kelas_per_mahasiswa/kelas_semua_siswa');
            $table->string('tahun_akademik', 20)->nullable()->after('tipe_sync');
            $table->string('semester', 5)->nullable()->after('tahun_akademik');
            $table->string('kode_matakuliah', 50)->nullable()->after('semester');
            $table->string('kelas', 10)->nullable()->after('kode_matakuliah');

            $table->index('tipe_sync');
            $table->index(['tahun_akademik', 'semester']);
            $table->index('kode_matakuliah');
        });
    }

    public function down(): void
    {
        Schema::table('feeder_sync_logs', function (Blueprint $table) {
            $table->dropIndex(['tipe_sync']);
            $table->dropIndex(['tahun_akademik', 'semester']);
            $table->dropIndex(['kode_matakuliah']);
            $table->dropColumn(['tipe_sync', 'tahun_akademik', 'semester', 'kode_matakuliah', 'kelas']);
        });
    }
};
