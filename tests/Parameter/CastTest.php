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

use ArrayObject;
use Chevere\Parameter\CastArgument;
use PHPUnit\Framework\TestCase;

final class CastTest extends TestCase
{
    public function castDataProvider(): array
    {
        return [
            [null, 'mixed'],
            [1, 'int'],
            [1.1, 'float'],
            [true, 'bool'],
            ['string', 'string'],
            [[], 'array'],
            [new CastArgument(''), 'object'],
            [
                fn () => null,
                'callable',
            ],
            [[], 'iterable'],
            [1, 'nullInt'],
            [null, 'nullInt'],
            [1.1, 'nullFloat'],
            [null, 'nullFloat'],
            [true, 'nullBool'],
            [null, 'nullBool'],
            ['string', 'nullString'],
            [null, 'nullString'],
            [[], 'nullArray'],
            [null, 'nullArray'],
            [new CastArgument(''), 'nullObject'],
            [null, 'nullObject'],
            [
                fn () => null,
                'nullCallable',
            ],
            [
                null,
                'nullCallable',
            ],
            [[], 'nullIterable'],
            [null, 'nullIterable'],
        ];
    }

    /**
     * @dataProvider castDataProvider
     */
    public function testCast($expected, string $method): void
    {
        $cast = new CastArgument($expected);
        $this->assertSame($expected, $cast->{$method}());
    }

    public function testArrayAccess(): void
    {
        $input = ['foo'];
        $value = new ArrayObject($input);
        $cast = new CastArgument($value);
        $this->assertSame($input, $cast->array());
    }
}
