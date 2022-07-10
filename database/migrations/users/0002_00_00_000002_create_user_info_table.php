<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_info', static function (Blueprint $table) {

            $table->unsignedInteger('user_id')->unique()->primary();

            $table->string('lastname');
            $table->string('firstname');
            $table->string('patronymic')->nullable();

            $table->enum('gender', ['male', 'female']);

            $table->date('birthdate')->nullable();

            $table->string('mobile_phone')->nullable();
            $table->string('email')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_info');
    }
}
