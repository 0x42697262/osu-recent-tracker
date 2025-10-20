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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();

            $table->binary('record_hash');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('beatmap_id');
            $table->dateTime('ended_at')->index();

            // score
            $table->decimal('pp', 8, 3)->nullable();
            $table->decimal('accuracy', 18, 16);
            $table->unsignedBigInteger('classic_total_score');
            $table->unsignedBigInteger('total_score');
            $table->unsignedBigInteger('legacy_total_score');
            $table->unsignedInteger('max_combo');

            // identifiers
            $table->string('rank', 3);
            $table->boolean('is_perfect_combo')->default(false);
            $table->boolean('passed')->default(false);
            $table->boolean('has_replay')->default(false);

            // statistics
            $table->json('mods')->default(json_encode([]));
            $table->unsignedInteger('great')->default(0);
            $table->unsignedInteger('ok')->default(0);
            $table->unsignedInteger('meh')->default(0);
            $table->unsignedInteger('miss')->default(0);
            $table->unsignedInteger('ignore_hit')->default(0);
            $table->unsignedInteger('ignore_miss')->default(0);
            $table->unsignedInteger('large_tick_hit')->default(0);
            $table->unsignedInteger('slider_tail_hit')->default(0);

            $table->timestamps();

            // uniqueness and indexes
            $table->unique('record_hash', 'ux_scores_record_hash');
            $table->index(['user_id', 'ended_at'], 'idx_user_endedat');

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
