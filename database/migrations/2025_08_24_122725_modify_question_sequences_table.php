<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('question_sequences', function (Blueprint $table) {
            $table->dropColumn('subject');
            $table->unsignedBigInteger('question_step_id')->nullable()->after('email');
            $table->boolean('sent')->default(false)->after('question_step_id');
            
            $table->foreign('question_step_id')->references('id')->on('question_steps')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('question_sequences', function (Blueprint $table) {
            $table->dropForeign(['question_step_id']);
            $table->dropColumn(['question_step_id', 'sent']);
            $table->string('subject')->after('email');
        });
    }
};