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
        Schema::create('question_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('first');
            $table->string('last');
            $table->string('email')->unique();
            $table->string('subject');
            $table->string('unsub_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_sequences');
    }
};
