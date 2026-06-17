<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('author_karma')->default(0)->after('author');
            $table->float('urgency_score')->default(0)->after('engagement_score');
            $table->float('depth_score')->default(0)->after('urgency_score');
            $table->float('freshness_score')->default(0)->after('depth_score');
            $table->float('competition_score')->default(0)->after('freshness_score');
            $table->boolean('op_engaged')->default(false)->after('competition_score');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['author_karma', 'urgency_score', 'depth_score', 'freshness_score', 'competition_score', 'op_engaged']);
        });
    }
};
