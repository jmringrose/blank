<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('question_sequences', function (Blueprint $table) {
            $table->json('email_history')->nullable()->after('sent');
        });
    }

    public function down(): void
    {
        Schema::table('question_sequences', function (Blueprint $table) {
            $table->dropColumn('email_history');
        });
    }
};