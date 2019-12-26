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
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\Variable\VariableExport;
use Chevere\Contracts\Router\RouterMakerContract;
use Chevere\Contracts\Router\RouterPropertiesContract;

final class RouterCache implements RouterCacheContract
{
    private CacheContract $cache;

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
        foreach ($routerMaker->properties()->toArray() as $name => $value) {
            $new->cache = $new->cache
                ->withPut(
                    new CacheKey($name),
                    new VariableExport($value)
                );
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties(): RouterPropertiesContract
    {
        $properties = new RouterProperties();
        try {
            foreach ($properties->toArray() as $name => $value) {
                $method = 'with' . ucfirst($name);
                $properties = $properties
                    ->$method(
                        $this->cache
                            ->get(new CacheKey($name))
                            ->raw()
                    );
            }
        } catch (FileNotFoundException $e) {
            throw new CacheNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        return $properties;
    }
}
