<?php
declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\HelperTraits\ApiInteractions;
use Tests\HelperTraits\CreatesPosition;
use Tests\HelperTraits\CreatesUser;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, ApiInteractions, CreatesUser, CreatesPosition;
}
