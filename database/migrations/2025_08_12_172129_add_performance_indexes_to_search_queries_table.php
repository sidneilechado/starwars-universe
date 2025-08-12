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
        Schema::table('search_queries', function (Blueprint $table) {
            $table->index(['searched_at', 'type'], 'idx_searched_at_type');
            $table->index(['searched_at', 'results_count'], 'idx_searched_at_results');
            $table->index(['user_agent'], 'idx_user_agent');
            $table->index(['response_time_ms'], 'idx_response_time');
            $table->index(['searched_at', 'response_time_ms'], 'idx_searched_at_response_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_queries', function (Blueprint $table) {
            $table->dropIndex('idx_searched_at_type');
            $table->dropIndex('idx_searched_at_results');
            $table->dropIndex('idx_user_agent');
            $table->dropIndex('idx_response_time');
            $table->dropIndex('idx_searched_at_response_time');
        });
    }
};
