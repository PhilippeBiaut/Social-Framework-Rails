<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `foreignId()->constrained()` declares the constraint but does not create an
 * index on SQLite, so the everyday lookups below were full table scans:
 * a post's comments and likes, and a user's followers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->index('post_id');
            $table->index('user_id');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->index('post_id');
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->index('follower_id');
            $table->index('followed_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['post_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['post_id']);
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->dropIndex(['follower_id']);
            $table->dropIndex(['followed_id']);
        });
    }
};
