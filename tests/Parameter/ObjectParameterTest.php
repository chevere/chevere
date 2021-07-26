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
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectParameterTest extends TestCase
{
    public function testConstructor(): void
    {
        $parameter = new ObjectParameter();
        $this->assertSame(stdClass::class, $parameter->className());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ObjectParameter())->withClassName('');
    }

    public function testWithClassName(): void
    {
        $parameter = (new ObjectParameter())
            ->withClassName(__CLASS__);
        $this->assertSame($parameter->className(), __CLASS__);
    }
}
