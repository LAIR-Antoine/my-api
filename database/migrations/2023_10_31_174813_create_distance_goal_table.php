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
        Schema::create('distance_goal', function (Blueprint $table) {
            $table->id();
            $table->string('sport');
            $table->integer('distance_to_do');
            $table->integer('distance_done');
            $table->date('begin_date');
            $table->date('end_date');
            $table->string('state');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distance_goal');
    }
};
