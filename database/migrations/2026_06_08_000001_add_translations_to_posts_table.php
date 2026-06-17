<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->text('title_en')->nullable()->after('title');
            $table->text('content_en')->nullable()->after('content');
            $table->text('title_es')->nullable()->after('title_en');
            $table->text('content_es')->nullable()->after('content_en');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'content_en', 'title_es', 'content_es']);
        });
    }
};
