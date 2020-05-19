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

namespace Chevere\Interfaces\Plugin;

use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Interfaces\ClassMap\ClassMapInterface;

interface PlugsRegistryInterface
{
    public function withAddedClassMap(CacheKeyInterface $key, PlugsMapInterface $plugsMap): PlugsRegistryInterface;

    public function hasClassMap(CacheKeyInterface $key): bool;

    public function getClassMap(CacheKeyInterface $key): ClassMapInterface;
}
