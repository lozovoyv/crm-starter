<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Utils\Translate;

/**
 * Assertions for localized messages.
 */
trait AssertLocalizations
{
    protected array $testingLocales = ['ru', 'en'];

    protected function getTestingLocales(): array
    {
        return $this->testingLocales;
    }

    protected function assertLocalizations(array $expected, callable $actual, ...$params): void
    {
        foreach ($this->getTestingLocales() as $locale) {
            app()->setLocale($locale);
            $actualMessages = $actual(...$params);
            $expectedMessages = Translate::array($expected);
            foreach ($actualMessages as $key => $message) {
                $this->assertNotEquals($expected[$key] ?? null, $message, sprintf('There are no translations for [%s] %s', $locale, $expected[$key] ?? $message));
            }
            $this->assertEquals($expectedMessages, $actualMessages, 'Messages are not equals for [' . $locale . ']');
        }
    }
}
