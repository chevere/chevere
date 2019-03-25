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

namespace Chevereto\Core\Traits;

trait ContainerTrait
{
    public function hasObject(string $key): bool
    {
        if (false == property_exists($this, 'objects') || false == (in_array($key, $this->objects) || property_exists($this, $key))) {
            return false;
        }

        return isset($this->{$key});
    }

    public function getObjects(): ?array
    {
        return $this->objects ?? null;
    }
}
