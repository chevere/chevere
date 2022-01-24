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

namespace Chevere\Tests\VarSupport;

use function Chevere\VarSupport\deepCopy;
use Chevere\VarSupport\Exceptions\VarObjectNotClonableException;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testDeepCopy(): void
    {
        $object = new class() {
        };
        $deepCopy = deepCopy($object);
        $this->assertEqualsCanonicalizing($object, $deepCopy);
    }

    public function testDeepCopyException(): void
    {
        $object = new class() {
            private function __clone()
            {
            }
        };
        $this->expectException(VarObjectNotClonableException::class);
        deepCopy($object);
    }
}
