<?php
declare(strict_types=1);

namespace Tests\HelperTraits;

use App\Current;
use App\Models\Users\User;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;

trait ApiInteractions
{
    public function getApi(string $url, ?User $user = null, array $session = [], array $headers = []): TestResponse
    {
        if ($user) {
            $this->actingAs($user)->withSession($session);
            Sanctum::actingAs($user);
        }
        Current::unset();

        $headers['Accept'] = 'application/json';

        return $this->get($url, $headers);
    }
}
