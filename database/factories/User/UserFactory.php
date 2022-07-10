<?php

namespace Database\Factories\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static array $used = [];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $login = $this->faker->userName;

        while (in_array($login, self::$used, true)) {
            $login = $this->faker->userName;
        }

        self::$used[] = $login;

        return [
            'login' => $login,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
