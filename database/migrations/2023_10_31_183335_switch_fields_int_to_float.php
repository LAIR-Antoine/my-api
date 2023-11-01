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
        Schema::table('distance_goal', function (Blueprint $table) {
            $table->float('distance_to_do')->change();
            $table->float('distance_done')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distance_goal', function (Blueprint $table) {
            $table->integer('distance_to_do')->change();
            $table->integer('distance_done')->change();
        });
    }
};
