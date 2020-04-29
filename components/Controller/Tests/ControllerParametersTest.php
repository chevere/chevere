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

namespace Chevere\Components\Controller\Tests;

use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Regex\Regex;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ControllerParametersTest extends TestCase
{
    public function testEmpty(): void
    {
        $key = 'name';
        $parameters = new ControllerParameters;
        $this->assertCount(0, $parameters->map());
        $this->assertFalse($parameters->hasParameterName($key));
        $this->expectException(OutOfBoundsException::class);
        $parameters->get($key);
    }

    public function testPut(): void
    {
        $key = 'name';
        $parameter = new ControllerParameter($key, new Regex('/.*/'));
        $parameters = (new ControllerParameters)->withParameter($parameter);
        $this->assertCount(1, $parameters->map());
        $this->assertTrue($parameters->hasParameterName($key));
        $this->assertSame($parameter, $parameters->get($key));
    }
}
