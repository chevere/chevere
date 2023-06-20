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

use Chevere\Filesystem\File;
use Chevere\Parameter\ObjectParameter;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Chevere\Filesystem\fileForPath;
use function Chevere\Parameter\object;

final class ObjectParameterTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ObjectParameter())->withClassName('');
    }

    public function testConstruct(): void
    {
        $parameter = new ObjectParameter();
        $this->assertEquals($parameter, object(stdClass::class));
        $this->assertSame(stdClass::class, $parameter->className());
        $this->assertSame([
            'type' => 'className',
            'className' => stdClass::class,
            'description' => '',
            'default' => null,
        ], $parameter->schema());
    }

    public function testWithClassName(): void
    {
        $parameter = new ObjectParameter();
        $withClassName = $parameter->withClassName(__CLASS__);
        $this->assertNotSame($parameter, $withClassName);
        $this->assertSame($withClassName->className(), __CLASS__);
        $this->assertSame([
            'type' => 'className',
            'className' => __CLASS__,
            'description' => '',
            'default' => null,
        ], $withClassName->schema());
    }

    public function testWithDefault(): void
    {
        $parameter = (new ObjectParameter())->withClassName(File::class);
        $withDefault = $parameter->withDefault(fileForPath(__FILE__));
        $this->assertNotSame($parameter, $withDefault);
        $this->assertSame(File::class, $withDefault->className());
        $this->assertSame([
            'type' => 'className',
            'className' => File::class,
            'description' => '',
            'default' => File::class,
        ], $withDefault->schema());
        $this->expectException(TypeError::class);
        $parameter->withDefault(new stdClass());
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
