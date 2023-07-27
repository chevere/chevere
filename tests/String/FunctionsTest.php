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
use Chevere\String\StringValidate;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [
                'Chevere\\String\\stringAssert',
                StringAssert::class,
            ],
            [
                'Chevere\\String\\stringModify',
                StringModify::class,
            ],
            [
                'Chevere\\String\\stringValidate',
                StringValidate::class,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEquivalent(string $function, string $class): void
    {
        $string = 'string';
        $expected = new $class($string);
        $function = $function($string);
        $this->assertEquals($expected, $function);
    }
}
