<?php

namespace App\Exceptions;

use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class WrongStatusException extends InvalidArgumentException implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return 400;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
