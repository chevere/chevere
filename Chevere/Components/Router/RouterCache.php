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

namespace Chevere\Components\Router;

use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\Filesystem\File\Exceptions\FileNotFoundException;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Interfaces\RouterPropertiesInterface;

final class RouterCache implements RouterCacheInterface
{
    private CacheInterface $cache;

    /**
     * Creates a new instance.
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function cache(): CacheInterface
    {
        return $this->cache;
    }

    public function withPut(RouterMakerInterface $routerMaker): RouterCacheInterface
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

    public function getProperties(): RouterPropertiesInterface
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
