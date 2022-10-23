<?php

namespace Database\Factories\Users;

use App\Models\Users\UserInfo;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserInfoFactory extends Factory
{
    protected $model = UserInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        $gender = ['male', 'female'][random_int(0, 1)];

        return [
            'gender' => $gender,
            'birthdate' => $this->faker->date('Y-m-d', '-20 years'),
            'notes' => $this->faker->text,
        ];
    }
}
