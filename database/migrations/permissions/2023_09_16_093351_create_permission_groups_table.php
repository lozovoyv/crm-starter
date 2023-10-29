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
        Schema::create('permission_groups', static function (Blueprint $table) {

            $table->unsignedSmallInteger('id', true)->from(1000);

            $table->boolean('active')->default(true);
            $table->boolean('locked')->default(false);

            $table->string('name')->unique();
            $table->string('description')->nullable();

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
        Schema::dropIfExists('permission_groups');
    }
};
