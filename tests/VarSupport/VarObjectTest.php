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

use Chevere\Components\VarSupport\VarObject;
use finfo;
use LogicException;
use PHPUnit\Framework\TestCase;

final class VarObjectTest extends TestCase
{
    public function testClonable(): void
    {
        $anonObject = new class() {
            private string $property = '';
        };
        $object = new VarObject($anonObject);
        $object->assertClonable();
        $this->assertSame($anonObject, $object->var());
    }

    public function testNotClonable(): void
    {
        $class = new class() {
            private function __clone()
            {
            }
        };
        $this->expectException(LogicException::class);
        $object = new VarObject($class);
        $object->assertClonable();
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
        $object = new VarObject($class);
        $object->assertClonable();
    }
}
