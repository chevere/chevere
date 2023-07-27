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

namespace Chevere\Tests\String;

use Chevere\String\StringAssert;
use Chevere\String\StringModify;
use Chevere\String\StringObject;
use Chevere\String\StringValidate;
use PHPUnit\Framework\TestCase;

final class StringObjectTest extends TestCase
{
    public function testConstruct(): void
    {
        $string = 'string';
        $object = new StringObject($string);
        $this->assertSame($string, (string) $object);
    }

    public function dataProvider(): array
    {
        return [
            [
                StringAssert::class,
                'assert',
            ],
            [
                StringModify::class,
                'modify',
            ],
            [
                StringValidate::class,
                'validate',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEquivalent(string $class, string $method): void
    {
        $string = 'string';
        $object = new StringObject($string);
        $expected = new $class($string);
        $result = $object->{$method}();
        $this->assertEquals($expected, $result);
        $this->assertSame($result, $object->{$method}());
    }
}
