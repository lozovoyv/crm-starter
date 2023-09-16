<?php
declare(strict_types=1);

use App\Models\Users\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {

            $table->unsignedInteger('id', true);

            $table->unsignedTinyInteger('status_id')->default(UserStatus::default);
            $table->boolean('locked')->default(false);

            $table->string('email')->unique()->nullable();
            $table->string('username', 50)->unique()->nullable();
            $table->string('phone', 20)->unique()->nullable();

            $table->string('password')->nullable();

            $table->string('lastname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('patronymic')->nullable();

            $table->string('display_name')->nullable();

            $table->rememberToken();

            $table->dateTime('email_verified_at')->nullable();
            $table->dateTime('phone_verified_at')->nullable();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('user_statuses')->restrictOnDelete()->cascadeOnUpdate();
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
};
