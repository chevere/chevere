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
use function Chevere\Parameter\boolean;

final class FunctionsBooleanTest extends TestCase
{
    public function testBoolean(): void
    {
        $boolean = boolean();
        $this->assertSame('', $boolean->description());
        $this->assertNull($boolean->default());
    }

    public static function booleanArgumentsProvider(): array
    {
        return [
            ['foo', true],
            ['bar', false],
        ];
    }

    /**
     * @dataProvider booleanArgumentsProvider
     */
    public function testBooleanArguments(string $description, bool $default): void
    {
        $boolean = boolean($description, $default);
        $this->assertSame($description, $boolean->description());
        $this->assertSame($default, $boolean->default());
    }
}
