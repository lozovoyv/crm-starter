<?php

namespace Database\Factories\Users;

use App\Models\Users\User;
use Exception;
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
     * @throws Exception
     */
    public function definition(): array
    {
        $login = $this->faker->userName;

        while (in_array($login, self::$used, true)) {
            $login = $this->faker->userName;
        }

        self::$used[] = $login;

        $gender = ['male', 'female'][random_int(0, 1)];

        return [
            'lastname' => $this->faker->lastName($gender),
            'firstname' => $this->faker->firstName($gender),
            'patronymic' => $this->faker->middleName($gender),
            'phone' => $this->faker->numerify('+7##########'),
            'email' => $this->faker->email,
            'username' => $login,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
