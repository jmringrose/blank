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
        Schema::create('newsletter_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('first');
            $table->string('last');
            $table->string('email');
            $table->integer('current_step')->default(1);
            $table->string('unsub_token')->nullable();
            $table->timestamp('next_send_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_sequences');
    }
};