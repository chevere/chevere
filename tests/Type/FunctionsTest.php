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

namespace Chevere\Tests\Type;

use PHPUnit\Framework\TestCase;
use function Chevere\Type\getType;

final class FunctionsTest extends TestCase
{
    public function testVariableType(): void
    {
        $table = [
            'object' => $this,
            'float' => 10.10,
            'null' => null,
        ];
        foreach ($table as $type => $variable) {
            $this->assertSame($type, getType($variable));
        }
    }
}
