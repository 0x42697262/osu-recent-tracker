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

        Schema::create('beatmapsets', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();

            $table->string('artist');
            $table->string('artist_unicode', 128);
            $table->string('creator', 32);
            $table->string('title', 128);
            $table->string('title_unicode', 128);
            $table->unsignedBigInteger('user_id');

            $table->timestamps();
        });

        Schema::create('beatmaps', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();

            // Basic metadata
            $table->unsignedBigInteger('beatmapset_id');
            $table->decimal('difficulty_rating', 5, 2);
            $table->unsignedBigInteger('user_id');
            $table->string('status', 16); // ranked, qualified, etc.
            $table->unsignedInteger('total_length');
            $table->string('version', 128);
            $table->string('checksum', 32);

            // Optional gameplay parameters
            $table->decimal('bpm', 7, 3);
            $table->decimal('cs', 3, 1);
            $table->decimal('ar', 3, 1);
            $table->decimal('drain', 3, 1);
            $table->decimal('accuracy', 3, 1);
            $table->unsignedInteger('hit_length');
            $table->unsignedInteger('count_circles');
            $table->unsignedInteger('count_sliders');
            $table->unsignedInteger('count_spinners');

            $table->timestamp('last_updated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beatmaps');
    }
};
