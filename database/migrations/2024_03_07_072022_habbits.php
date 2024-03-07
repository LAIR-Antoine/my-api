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
        Schema::create('habbits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('begin_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('frequency')->nullable();
            $table->string('type')->nullable();
            $table->string('info')->nullable();
            $table->timestamps();
        });

        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('number')->nullable();
            $table->timestamps();
        });

        Schema::create('habbit_day', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habbit_id')->constrained();
            $table->foreignId('day_id')->constrained();
            $table->string('time')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habbits');
        Schema::dropIfExists('days');
        Schema::dropIfExists('habbit_day');
    }
};
