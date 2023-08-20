<?php
declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\ApiInteractions;
use Tests\Traits\CreatesPosition;
use Tests\Traits\CreatesUser;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, ApiInteractions, CreatesUser, CreatesPosition;
}
