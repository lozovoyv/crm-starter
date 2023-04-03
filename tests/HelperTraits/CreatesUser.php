<?php
declare(strict_types=1);

namespace Tests\HelperTraits;

use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;

trait CreatesUser
{
    /**
     * Creates the application.
     *
     * @param string|null $lastname
     * @param string|null $firstname
     * @param string|null $patronymic
     * @param string|null $email
     * @param string|null $username
     * @param string|null $phone
     * @param string|null $display_name
     * @param string|null $password
     *
     * @return User
     */
    public function createUser(
        ?string $lastname = null, ?string $firstname = null, ?string $patronymic = null, ?string $email = null, ?string $username = null, ?string $phone = null,
        ?string $display_name = null, ?string $password = null
    ): User
    {
        $user = new User();
        $user->lastname = $lastname;
        $user->firstname = $firstname;
        $user->patronymic = $patronymic;
        $user->email = $email;
        $user->username = $username;
        $user->phone = $phone;
        $user->display_name = $display_name;
        $user->password = Hash::make($password);
        $user->save();

        return $user;
    }
}
