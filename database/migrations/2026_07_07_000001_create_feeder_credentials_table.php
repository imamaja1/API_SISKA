<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeder_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 100)->unique()->comment('feeder_url / feeder_username / feeder_password / feeder_endpoint');
            $table->text('key_value')->comment('Nilai terenkripsi (AES-256-CBC)');
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeder_credentials');
    }
};
