<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('images', static function (Blueprint $table) {
            $table->id();
            $table->string('hash');
            $table->string('disk');
            $table->string('filename');
            $table->string('extension');
            $table->string('original_filename');
            $table->string('mime');
            $table->integer('size');

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            $table->integer('top')->nullable();
            $table->integer('left')->nullable();
            $table->integer('bottom')->nullable();
            $table->integer('right')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
}
