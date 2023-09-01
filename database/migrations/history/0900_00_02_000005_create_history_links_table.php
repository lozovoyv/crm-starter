<?php
declare(strict_types=1);

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
        Schema::create('history_links', static function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('history_id');

            $table->string('entry_type')->nullable();
            $table->unsignedBigInteger('entry_id')->nullable();
            $table->string('entry_caption')->nullable();
            $table->string('entry_mark')->nullable();

            $table->foreign('history_id')->references('id')->on('histories')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('history_links');
    }
};
