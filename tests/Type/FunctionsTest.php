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

namespace Chevere\Tests\Type;

use PHPUnit\Framework\TestCase;
use function Chevere\Components\Type\debugType;
use function Chevere\Components\Type\returnTypeExceptionMessage;

final class FunctionsTest extends TestCase
{
    public function testDebugType(): void
    {
        $typeObject = debugType($this);
        $this->assertSame(__CLASS__, $typeObject);
        $typeScalar = debugType('integer');
        $this->assertSame('string', $typeScalar);
    }

    public function testReturnTypeExceptionMessage(): void
    {
        $expected = 'string';
        $provided = 'integer';
        $message = returnTypeExceptionMessage($expected, $provided);
        $this->assertSame("Expecting return type $expected, type $provided provided", $message->toString());
    }
}
