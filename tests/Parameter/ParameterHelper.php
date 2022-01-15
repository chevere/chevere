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

class ParameterHelper extends TestCase
{
    public function testWithParameterDefault(
        string $primitive,
        object $parameter,
        mixed $default,
        object $parameterWithDefault
    ): void {
        $this->assertNotSame($parameter, $parameterWithDefault);
        $this->assertSame($default, $parameterWithDefault->default());
        $this->assertSame($default, $parameterWithDefault->default());
        $this->assertSame($primitive, $parameterWithDefault->getType()->primitive());
    }
}
