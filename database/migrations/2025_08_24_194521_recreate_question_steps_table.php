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
        Schema::dropIfExists('question_steps');
        
        Schema::create('question_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->string('title');
            $table->text('notes')->nullable();
            $table->boolean('draft')->default(true);
            $table->string('filename')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_steps');
    }
};