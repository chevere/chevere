<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

abstract class Container
{
    protected $objects = [];

    /**
     * Checks if an object exists and is not null.
     */
    public function hasObject(string $key): bool
    {
        if (!(in_array($key, $this->objects) || property_exists($this, $key))) {
            return false;
        }

        return isset($this->{$key});
    }

    public function listObjects(): array
    {
        return $this->objects;
    }

    public function getObject(string $key): ?object
    {
        if ($this->hasObject($key)) {
            return $this->{$key};
        }

        return null;
    }
}
