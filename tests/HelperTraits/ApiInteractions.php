<?php
declare(strict_types=1);

namespace Tests\HelperTraits;

use App\Current;
use App\Models\Users\User;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;

trait ApiInteractions
{
    private function initialize(?User $user, array $session): void
    {
        if ($user) {
            $this->actingAs($user)->withSession($session);
            Sanctum::actingAs($user);
        }
        Current::unset();
    }

    private function makeHeaders(array $headers): array
    {
        $headers['Accept'] = 'application/json';
        $headers['origin'] = '127.0.0.1';

        return $headers;
    }

    public function getApi(string $url, ?User $user = null, array $session = [], array $headers = []): TestResponse
    {
        $this->initialize($user, $session);

        return $this->get($url, $this->makeHeaders($headers));
    }
}
