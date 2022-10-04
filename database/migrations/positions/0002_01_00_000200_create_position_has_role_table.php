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
        Schema::create('position_has_role', static function (Blueprint $table) {

            $table->unsignedInteger('position_id');
            $table->unsignedSmallInteger('role_id');

            $table->foreign('position_id')->references('id')->on('positions')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('permission_roles')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('position_has_role');
    }
};
