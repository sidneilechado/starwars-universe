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
            $table->string('session_id')->nullable()->after('user_agent');
            $table->string('country_code', 2)->nullable()->after('session_id');
            $table->string('device_type')->nullable()->after('country_code');
            $table->string('browser')->nullable()->after('device_type');
            $table->boolean('has_results')->default(true)->after('browser');
            $table->string('referrer')->nullable()->after('has_results');
            $table->json('query_metadata')->nullable()->after('referrer');
            
            $table->index('session_id');
            $table->index('country_code');
            $table->index('device_type');
            $table->index('has_results');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_queries', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['country_code']);
            $table->dropIndex(['device_type']);
            $table->dropIndex(['has_results']);
            
            $table->dropColumn([
                'session_id',
                'country_code', 
                'device_type',
                'browser',
                'has_results',
                'referrer',
                'query_metadata'
            ]);
        });
    }
};
