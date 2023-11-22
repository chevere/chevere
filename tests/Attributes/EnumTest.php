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

namespace Chevere\Tests\Attributes;

use Chevere\Attributes\Enum;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [
                ['one'],
            ],
            [
                ['one', 'two'],
            ],
            [
                ['one', 'two', 'three'],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testConstruct(array $args): void
    {
        $attribute = new Enum(...$args);
        $this->assertSame(
            $attribute->regex()->noDelimitersNoAnchors(),
            '\b(' . implode('|', $args) . ')\b'
        );
    }
}
