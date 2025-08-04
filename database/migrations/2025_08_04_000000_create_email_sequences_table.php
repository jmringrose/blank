<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_sequences', function (Blueprint $table) {

            $table->id();
            $table->string('first');
            $table->string('last');
            $table->string('email');
            $table->integer('current_step')->default(1);
            $table->dateTime('next_send_at')->nullable();
            $table->string('unsub_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequences');
    }
};
