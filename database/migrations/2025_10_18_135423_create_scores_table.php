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
        /**
         *
         * What we need:
         *  player info
         *  - `hash` for uniqueness, SHA-256 binary (32 bytes), checking if we got duplicate entries
         *  - `user_id` player user id
         *  - `username` player username
         *  score stats
         *  - `created_at` date of score submission
         *  - `id` score id (failed scores are given with id only for lazer; some scores will get deleted)
         *  - `accuracy`
         *  - `max_combo`
         *  - `mods`
         *  - `passed`
         *  - `perfect`
         *  - `pp`
         *  - `rank`
         *  - `score`
         *  - `count_100`
         *  - `count_300`
         *  - `count_50`
         *  - `count_geki`
         *  - `count_katu`
         *  - `count_miss`
         *  minimum beatmap info
         *  - `beatmap_id`
         *
         *  then maybe create another table for beatmaps?
         */
        Schema::create('scores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('beatmap_id');

            $table->binary('record_hash');
            $table->unsignedBigInteger('score_id');

            // core score stats
            $table->decimal('accuracy', 18, 16);
            $table->unsignedInteger('max_combo');
            $table->unsignedBigInteger('enabled_mods')->default(0);
            $table->boolean('passed')->default(false);
            $table->boolean('perfect')->default(false);
            $table->decimal('pp', 8, 3);
            $table->string('rank', 3);
            $table->unsignedBigInteger('score');

            // statistics
            $table->unsignedInteger('count_100');
            $table->unsignedInteger('count_300');
            $table->unsignedInteger('count_50');
            $table->unsignedInteger('count_geki');
            $table->unsignedInteger('count_katu');
            $table->unsignedInteger('count_miss');

            // canonical event time (when score was created/submitted)
            $table->dateTime('score_time')->index();

            $table->timestamps();

            // uniqueness and indexes
            $table->unique('record_hash', 'ux_scores_record_hash');
            $table->index(['user_id', 'score_time'], 'idx_user_scoretime');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('players')
                ->onDelete('cascade');

            $table
                ->foreign('beatmap_id')
                ->references('id')
                ->on('beatmaps')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
