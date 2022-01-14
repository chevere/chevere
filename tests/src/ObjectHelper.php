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

namespace Chevere\Tests\src;

use ReflectionObject;

final class ObjectHelper
{
    private ReflectionObject $reflection;

    public function __construct(private object $object)
    {
        $this->reflection = new ReflectionObject($this->object);
    }

    public function getPropertyValue(string $name): mixed
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }
}
