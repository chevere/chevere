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

namespace Chevere\Components\Plugs\Interfaces;

use Chevere\Components\Cache\Interfaces\CacheKeyInterface;
use Chevere\Components\ClassMap\Interfaces\ClassMapInterface;

interface PlugsRegistryInterface
{
    public function withAddedClassMap(CacheKeyInterface $key, PlugsMapInterface $plugsMap): PlugsRegistryInterface;

    public function hasClassMap(CacheKeyInterface $key): bool;

    public function getClassMap(CacheKeyInterface $key): ClassMapInterface;
}
