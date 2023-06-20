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

namespace Chevere\Tests\VariableSupport;

use Chevere\Tests\VariableSupport\_resources\ClassWithResource;
use Chevere\VariableSupport\Exceptions\ObjectNotClonableException;
use Chevere\VariableSupport\ObjectVariable;
use finfo;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectVariableTest extends TestCase
{
    public function testClonable(): void
    {
        $anonObject = new class() {
            private string $property = 'test';
        };
        $objectVariable = new ObjectVariable($anonObject);
        $objectVariable->assertClonable();
        $this->assertSame($anonObject, $objectVariable->variable());
    }

    public function testNotClonable(): void
    {
        $class = new class() {
            private function __clone()
            {
            }
        };
        $objectVariable = new ObjectVariable($class);
        $this->expectException(ObjectNotClonableException::class);
        $objectVariable->assertClonable();
    }

    public function testNestedNotClonable(): void
    {
        $object = new stdClass();
        $resource = [
            [0, 1, 2],
            [
                new finfo(FILEINFO_MIME),
            ],
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
        $objectVariable = new ObjectVariable($object);
        $this->expectException(ObjectNotClonableException::class);
        $this->expectExceptionMessage($atString);
        $objectVariable->assertClonable();
    }
}
