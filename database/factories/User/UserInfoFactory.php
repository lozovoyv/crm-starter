<?php

namespace Database\Factories\User;

use App\Models\User\UserInfo;
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
            'lastname' => $this->faker->lastName($gender),
            'firstname' => $this->faker->firstName($gender),
            'patronymic' => $this->faker->middleName($gender),
            'gender' => $gender,
            'birthdate' => $this->faker->date('Y-m-d', '-20 years'),
            'mobile_phone' => $this->faker->numerify('+7 (###) ###-##-##'),
            'email' => $this->faker->email,
            'notes' => $this->faker->text,
        ];
    }
}
