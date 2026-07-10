<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeder_credentials', function (Blueprint $table) {
            $table->string('key_name', 100)->comment('feeder_url / feeder_port / feeder_username / feeder_password / feeder_endpoint / feeder_token / _feeder_config')->change();
        });
    }

    public function down(): void
    {
        Schema::table('feeder_credentials', function (Blueprint $table) {
            $table->string('key_name', 100)->comment('feeder_url / feeder_username / feeder_password / feeder_endpoint')->change();
        });
    }
};
