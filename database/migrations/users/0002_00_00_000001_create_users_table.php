<?php

use App\Models\Dictionaries\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {

            $table->unsignedInteger('id', true);

            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('status_id')->default(UserStatus::default);

            $table->rememberToken();


            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('dictionary_user_statuses')->restrictOnDelete()->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
