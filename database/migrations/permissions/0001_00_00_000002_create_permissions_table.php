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
        Schema::create('permissions', static function (Blueprint $table) {

            $table->unsignedSmallInteger('id', true);

            $table->string('key')->unique();
            $table->string('scope_name');
            $table->string('name');
            $table->string('description')->nullable();

            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();

            $table->foreign('scope_name')->references('scope_name')->on('permission_scopes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
