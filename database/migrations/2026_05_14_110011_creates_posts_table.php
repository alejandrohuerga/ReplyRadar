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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->unique();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('subreddit');
            $table->string('url');
            $table->string('author')->nullable();
            $table->integer('reddit_score')->default(0);
            $table->integer('num_comments')->default(0);

            // Scoring engine
            $table->float('intent_score')->default(0);
            $table->float('match_score')->default(0);
            $table->float('engagement_score')->default(0);
            $table->float('final_score')->default(0);

            // Deduplicación
            $table->string('content_hash')->index();

            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
