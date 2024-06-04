<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table ->id()->startingValue(0)->primary();
            $table ->string("user_id");
            $table->foreign("user_id")->references("id")->on("users");
            $table ->integer("step_count");
            $table ->integer("stair_step_count");
            $table ->float("heart_rate");
            $table ->float("distance");
            $table ->date("datetime");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
