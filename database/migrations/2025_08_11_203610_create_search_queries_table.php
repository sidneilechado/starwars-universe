<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->enum('type', ['films', 'people']);
            $table->integer('results_count')->default(0);
            $table->integer('response_time_ms');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('searched_at');
            $table->timestamps();

            $table->index(['query', 'type']);
            $table->index('searched_at');
            $table->index(['created_at', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};