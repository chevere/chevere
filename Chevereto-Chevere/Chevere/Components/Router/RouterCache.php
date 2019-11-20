<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router;

use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Router\RouterCacheContract;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Variable\VariableExport;
use Chevere\Contracts\Router\CacheKeysContract;
use Chevere\Contracts\Router\RouterMakerContract;

final class RouterCache implements RouterCacheContract
{
    /** @var CacheContract */
    private $cache;

    /**
     * {@inheritdoc}
     */
    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function cache(): CacheContract
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function withPut(RouterMakerContract $routerMaker): RouterCacheContract
    {
        $new = clone $this;
        $new->cache = $new->cache
            ->withPut(
                new CacheKey(CacheKeysContract::REGEX),
                new VariableExport(
                    $routerMaker->regex()
                )
            )
            ->withPut(
                new CacheKey(CacheKeysContract::ROUTES),
                new VariableExport(
                    $routerMaker->routes()
                )
            )
            ->withPut(
                new CacheKey(CacheKeysContract::INDEX),
                new VariableExport(
                    $routerMaker->index()
                )
            );

        return $new;
    }
}
