<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('marketing_steps', function (Blueprint $table) {
            $table->longText('content')->nullable();
        });
    }

    public function down()
    {
        Schema::table('marketing_steps', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};