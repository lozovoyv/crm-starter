<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('permission_modules', static function (Blueprint $table) {

            $table->unsignedSmallInteger('id', true);

            $table->string('module')->unique();

            $table->string('name');

            $table->unsignedSmallInteger('order')->nullable()->default(0);

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
        Schema::dropIfExists('permission_modules');
    }
}
