<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // El más importante: ranking por score dentro de un keyword
            $table->index(['keyword_id', 'final_score'], 'idx_posts_keyword_score');

            // Para filtrar por subreddit
            $table->index(['keyword_id', 'subreddit'], 'idx_posts_keyword_subreddit');

            // Para ordenar por fecha
            $table->index(['keyword_id', 'posted_at'], 'idx_posts_keyword_date');

            // Para el dashboard global (todos los posts de un usuario)
            $table->index('final_score', 'idx_posts_final_score');
        });

        Schema::table('keywords', function (Blueprint $table) {
            // Para el scheduler: keywords activas pendientes de fetch
            $table->index(['is_active', 'last_fetched_at'], 'idx_keywords_active_fetched');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('idx_posts_keyword_score');
            $table->dropIndex('idx_posts_keyword_subreddit');
            $table->dropIndex('idx_posts_keyword_date');
            $table->dropIndex('idx_posts_final_score');
        });

        Schema::table('keywords', function (Blueprint $table) {
            $table->dropIndex('idx_keywords_active_fetched');
        });
    }
};
