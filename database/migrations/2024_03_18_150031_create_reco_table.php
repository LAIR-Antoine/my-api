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
        Schema::create('reco', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('spotify')->nullable();
            $table->string('strava')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reco');
    }
};
