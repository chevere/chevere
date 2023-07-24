<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Parameter;

use Chevere\Parameter\Cast;
use PHPUnit\Framework\TestCase;

final class CastTest extends TestCase
{
    public function castDataProvider(): array
    {
        return [
            [null, 'mixed'],
            [1, 'integer'],
            [1.1, 'float'],
            [true, 'boolean'],
            ['string', 'string'],
            [[], 'array'],
            [new Cast(''), 'object'],
            [
                fn () => null,
                'callable',
            ],
            [[], 'iterable'],
        ];
    }

    /**
     * @dataProvider castDataProvider
     */
    public function testCast($expected, string $method): void
    {
        $cast = new Cast($expected);
        $this->assertSame($expected, $cast->{$method}());
    }
}
