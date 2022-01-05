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

use Chevere\Components\VarSupport\ObjectClonable;
use finfo;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ObjectClonableTest extends TestCase
{
    public function testClonable(): void
    {
        $class = new class() {
            private string $property = '';
        };
        $objectClonable = new ObjectClonable($class);
        $this->assertSame($class, $objectClonable->var());
    }

    public function testNotClonable(): void
    {
        $class = new class() {
            private function __clone()
            {
            }
        };
        $this->expectException(LogicException::class);
        new ObjectClonable($class);
    }

    public function testNestedNotClonable(): void
    {
        $class = new class() {
            public function __construct()
            {
                $this->property = [
                    [0, 1, 2],
                    [
                        new finfo(FILEINFO_MIME)
                    ]
                ];
            }
        };
        $this->expectException(LogicException::class);
        new ObjectClonable($class);
    }
}
