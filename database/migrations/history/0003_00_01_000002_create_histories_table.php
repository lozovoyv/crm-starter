<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('histories', static function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedSmallInteger('action_id');
            $table->unsignedBigInteger('history_line_id')->nullable();

            $table->string('entry_name', 40);
            $table->unsignedBigInteger('entry_id')->nullable();
            $table->string('description')->nullable();

            $table->unsignedInteger('position_id')->nullable();

            $table->dateTime('timestamp');

            $table->foreign('history_line_id')->references('id')->on('history_lines')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('action_id')->references('id')->on('history_actions')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('position_id')->references('id')->on('positions')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
