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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('source', 20)->default('reddit')->after('content_hash')->index();
            $table->integer('like_count')->nullable()->after('source');
            $table->integer('retweet_count')->nullable()->after('like_count');
            $table->integer('reply_count')->nullable()->after('retweet_count');
            $table->string('author_handle')->nullable()->after('reply_count');
            $table->bigInteger('author_followers')->nullable()->after('author_handle');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['source', 'like_count', 'retweet_count', 'reply_count', 'author_handle', 'author_followers']);
        });
    }
};
