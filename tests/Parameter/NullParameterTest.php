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

use Chevere\Parameter\NullParameter;
use PHPUnit\Framework\TestCase;

final class NullParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new NullParameter();
        $this->assertSame(null, $parameter->default());
        $compatible = new NullParameter();
        $parameter->assertCompatible($compatible);
        $this->assertSame([
            'type' => 'null',
            'description' => null,
            'default' => null,
        ], $parameter->schema());
    }
}
