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
use Chevere\Exceptions\VarSupport\VarObjectNotClonableException;
use Chevere\Tests\VarSupport\_resources\ClassWithResource;
use finfo;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarObjectTest extends TestCase
{
    public function testClonable(): void
    {
        $anonObject = new class() {
            private string $property = 'test';
        };
        $varObject = new VarObject($anonObject);
        $varObject->assertClonable();
        $this->assertSame($anonObject, $varObject->var());
    }

    public function testNotClonable(): void
    {
        $class = new class() {
            private function __clone()
            {
            }
        };
        $varObject = new VarObject($class);
        $this->expectException(VarObjectNotClonableException::class);
        $varObject->assertClonable();
    }

    public function testNestedNotClonable(): void
    {
        $object = new stdClass();
        $resource = [
            [0, 1, 2],
            [
                new finfo(FILEINFO_MIME)
            ]
        ];
        $childObject = new ClassWithResource($resource);
        $object->array = [1, 2, 3, $childObject];
        $atBreadcrumb = [
            'object: stdClass',
            'property: $array',
            '(iterable)',
            'key: 3',
            'object: ' . ClassWithResource::class,
            'property: array $array',
            '(iterable)',
            'key: 0',
            '(iterable)',
            'key: 1',
            '(iterable)',
            'key: 0',
            'object: finfo',
        ];
        $atString = '[' . implode('][', $atBreadcrumb) . ']';
        $varObject = new VarObject($object);
        $this->expectException(VarObjectNotClonableException::class);
        $this->expectExceptionMessage($atString);
        $varObject->assertClonable();
    }
}
