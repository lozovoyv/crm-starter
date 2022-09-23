<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionInRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('permission_in_role', static function (Blueprint $table) {

            $table->unsignedSmallInteger('permission_id');
            $table->unsignedSmallInteger('role_id');

            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('permission_in_role');
    }
}
