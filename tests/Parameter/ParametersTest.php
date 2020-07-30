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

use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    public function testEmpty(): void
    {
        $key = 'name';
        $parameters = new Parameters;
        $this->assertCount(0, $parameters->toArray());
        $this->assertFalse($parameters->hasParameterName($key));
        $this->expectException(OutOfBoundsException::class);
        $parameters->get($key);
    }

    public function testPut(): void
    {
        $key = 'name';
        $parameter = new Parameter($key);
        $parameters = (new Parameters)->withAdded($parameter);
        $this->assertCount(1, $parameters->toArray());
        $this->assertTrue($parameters->hasParameterName($key));
        $this->assertSame($parameter, $parameters->get($key));
    }
}
