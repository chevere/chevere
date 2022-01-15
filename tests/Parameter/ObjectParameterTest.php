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

use Chevere\Components\Parameter\ObjectParameter;
use function Chevere\Components\Parameter\objectParameter;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ObjectParameter();
        $this->assertEquals($parameter, objectParameter(stdClass::class));
        $this->assertSame(stdClass::class, $parameter->className());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ObjectParameter())->withClassName('');
    }

    public function testWithClassName(): void
    {
        $parameter = new ObjectParameter();
        $parameterWithClassName = $parameter
            ->withClassName(__CLASS__);
        $this->assertNotSame($parameter, $parameterWithClassName);
        $this->assertSame($parameterWithClassName->className(), __CLASS__);
    }
}
