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
        Schema::create('stress_data', function (Blueprint $table) {
            $table->string("id")->primary();
            $table->string("user_id");
            $table->foreign("user_id")->references("id")->on("users");
            $table->string("device_id");
            $table->dateTime("datetime");
            $table->integer("stress_level");
            $table->float("average_heart_rate");
            $table->float("latitude");
            $table->float("longitude");
            $table->string("prediction")->nullable();
            $table->integer("step_count")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stress_data');
    }
};
