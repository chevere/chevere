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
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ObjectParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        new ObjectParameter(__CLASS__);
        $this->expectException(InvalidArgumentException::class);
        new ObjectParameter(Type::STRING);
    }
}
