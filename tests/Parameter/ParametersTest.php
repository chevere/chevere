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
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    public function testEmpty(): void
    {
        $key = 'name';
        $parameters = new Parameters;
        $this->assertCount(0, $parameters->toArray());
        $this->assertFalse($parameters->has($key));
        $this->expectException(OutOfBoundsException::class);
        $parameters->get($key);
    }

    public function testWithAdded(): void
    {
        $key = 'name';
        $parameter = new Parameter($key);
        $parameters = (new Parameters)->withAdded($parameter);
        $this->assertCount(1, $parameters->toArray());
        $this->assertTrue($parameters->has($key));
        $this->assertSame($parameter, $parameters->get($key));
        $this->expectException(OverflowException::class);
        $parameters->withAdded($parameter);
    }

    public function testWithModified(): void
    {
        $key = 'name';
        $parameter = new Parameter($key);
        $parameters = (new Parameters)->withAdded($parameter);
        $parameters = $parameters
            ->withModify(
                (new Parameter($key))->withDescription('modify')
            );
        $this->assertTrue($parameters->has($key));
        $this->assertSame('modify', $parameters->get($key)->description());
        $this->expectException(OutOfBoundsException::class);
        $parameters->withModify(new Parameter('not-found'));
    }
}
