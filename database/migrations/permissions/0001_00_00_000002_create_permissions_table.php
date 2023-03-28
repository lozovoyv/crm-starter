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
            $table->string('module');
            $table->string('name');
            $table->string('description')->nullable();

            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();

            $table->foreign('module')->references('module')->on('permission_modules')->cascadeOnDelete()->cascadeOnUpdate();
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
