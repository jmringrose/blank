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
            $table->date('tour_date')->nullable();
            $table->string('tour_date_str')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletter_sequences', function (Blueprint $table) {
            $table->dropColumn(['tour_date', 'tour_date_str']);
        });
    }
};
