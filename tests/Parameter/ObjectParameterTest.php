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

use function Chevere\Parameter\object;
use Chevere\Parameter\ObjectParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ObjectParameter();
        $this->assertEquals($parameter, object(stdClass::class));
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

    public function testAssertCompatible(): void
    {
        $parameter = (new ObjectParameter())->withClassName(__CLASS__);
        $compatible = (new ObjectParameter())->withClassName(__CLASS__);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = (new ObjectParameter())->withClassName(ObjectParameter::class);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }
}
