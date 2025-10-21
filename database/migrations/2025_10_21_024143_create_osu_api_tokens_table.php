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
        Schema::create('osu_api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name', 16)->unique;
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at');
            $table->string('token_type', 8);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('osu_api_tokens');
    }
};
