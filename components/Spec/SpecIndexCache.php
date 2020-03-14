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

namespace Chevere\Components\Spec;

use BadMethodCallException;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\Interfaces\SpecIndexCacheInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use LogicException;

final class SpecIndexCache implements SpecIndexCacheInterface
{
    private CacheInterface $cache;

    private array $array;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function put(SpecIndexInterface $spec): void
    {
        // Add this header to all responses: Link: </spec/api/routes.json>; rel="describedby"
    }
}
