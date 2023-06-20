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

namespace Chevere\Tests\DataStructure;

use PHPUnit\Framework\TestCase;
use function Chevere\DataStructure\data;

final class FunctionsTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [[0, 1, 2]],
            [[
                'cero' => 0,
                'uno' => 1,
                'dos' => 2,
            ]],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testData(array $arguments): void
    {
        $args = data(...$arguments);
        $this->assertSame($arguments, $args);
    }
}
