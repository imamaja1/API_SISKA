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
        Schema::create('api_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_user_id')->nullable()->constrained('api_users')->nullOnDelete();

            $table->string('endpoint');
            $table->string('method', 10);

            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('request_payload')->nullable();
            $table->integer('response_status')->nullable();
            $table->timestamp('accessed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_access_logs');
    }
};
