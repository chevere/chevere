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

interface PlugsMapCacheInterface
{
    const KEY_CLASS_MAP = 'classmap';

    public function withPut(PlugsMapInterface $plugsMap): PlugsMapCacheInterface;

    public function hasPlugsQueueFor(string $className): bool;

    public function getPlugsQueueFor(string $className): PlugsQueueTypedInterface;
}
