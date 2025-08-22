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
        Schema::table('newsletter_sequences', function (Blueprint $table) {
            $table->index('unsub_token');
            $table->index('email');
            $table->index('current_step');
            $table->index(['tour_date', 'current_step']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletter_sequences', function (Blueprint $table) {
            $table->dropIndex(['unsub_token']);
            $table->dropIndex(['email']);
            $table->dropIndex(['current_step']);
            $table->dropIndex(['tour_date', 'current_step']);
            $table->dropIndex(['created_at']);
        });
    }
};
