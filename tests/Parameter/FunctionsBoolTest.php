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

use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\bool;

final class FunctionsBoolTest extends TestCase
{
    public function testBool(): void
    {
        $bool = bool();
        $this->assertSame('', $bool->description());
        $this->assertNull($bool->default());
    }

    public static function boolArgumentsProvider(): array
    {
        return [
            ['foo', true],
            ['bar', false],
        ];
    }

    /**
     * @dataProvider boolArgumentsProvider
     */
    public function testBoolArguments(string $description, bool $default): void
    {
        $bool = bool($description, $default);
        $this->assertSame($description, $bool->description());
        $this->assertSame($default, $bool->default());
    }
}
