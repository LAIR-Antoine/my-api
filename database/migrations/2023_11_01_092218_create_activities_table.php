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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->integer('strava_id');
            $table->string('name');
            $table->string('type');
            $table->datetime('start_date_local')->nullable();
            $table->string('location')->nullable();
            $table->float('distance')->nullable();
            $table->integer('moving_time')->nullable();
            $table->integer('elapsed_time')->nullable();
            $table->integer('total_elevation_gain')->nullable();
            $table->float('average_speed')->nullable();
            $table->float('max_speed')->nullable();
            $table->float('average_heartrate')->nullable();
            $table->float('max_heartrate')->nullable();
            $table->float('average_cadence')->nullable();
            $table->float('average_watts')->nullable();
            $table->float('max_watts')->nullable();
            $table->integer('suffer_score')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
