<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeder_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 50)->comment('mahasiswa/kelas/matakuliah');
            $table->string('referensi', 100)->comment('NIM / ID Kelas / ID Matakuliah');
            $table->integer('jumlah_data_feeder')->default(0);
            $table->integer('jumlah_data_siska')->default(0);
            $table->integer('jumlah_sync')->default(0);
            $table->integer('jumlah_gagal')->default(0);
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->foreignId('synced_by')->constrained('users')->cascadeOnDelete();
            $table->json('log_detail')->nullable();
            $table->timestamps();

            $table->index('tipe');
            $table->index('referensi');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeder_sync_logs');
    }
};
