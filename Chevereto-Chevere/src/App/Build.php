<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\App;

use Chevere\Api\Api;
use Chevere\Api\Maker as ApiMaker;
use Chevere\Router\Maker as RouterMaker;
use Chevere\App\Exceptions\AlreadyBuiltException;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\CheckoutContract;
use Chevere\Contracts\App\ParametersContract;
use Chevere\File\File;
use Chevere\Path\PathHandle;
use Chevere\Router\Router;

final class Build implements BuildContract
{
    /** @var BuilderContract */
    private $builder;

    /** @var Container */
    private $container;

    /** @var File */
    private $path;

    /** @var bool True if the App was built (cache) */
    private $isBuilt;

    /** @var CheckoutContract */
    private $checkout;

    /** @var array An array containing the collection of Cache->toArray() data (checksums) */
    private $cacheChecksums;

    /** @var ApiMaker */
    private $apiMaker;

    /** @var RouterMaker */
    private $routerMaker;

    public function __construct(BuilderContract $builder)
    {
        $this->builder = $builder;
        $this->container = new Container();
        $this->file = (new PathHandle(static::FILE_INDETIFIER))->file();
    }

    public function exists(): bool
    {
        return $this->file->exists();
    }

    public function withContainer(Container $container): BuildContract
    {
        $new = clone $this;
        $new->container = $container;

        return $new;
    }

    public function withParameters(ParametersContract $parameters): BuildContract
    {
        $new = clone $this;
        $new->routerMaker = new RouterMaker();
        if ($new->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $new->cacheChecksums = [];
        if (!empty($parameters->api())) {
            $pathHandle = new PathHandle($parameters->api());
            $new->apiMaker = new ApiMaker($new->routerMaker);
            $new->apiMaker = $new->apiMaker
                ->withPath($pathHandle->path());
            $new->container = $new->container
                ->withApi(
                    (new Api())
                        ->withMaker($new->apiMaker)
                );
            $new->apiMaker = $new->apiMaker
                ->withCache();
            $new->cacheChecksums = $new->apiMaker->cache()->toArray();
        }
        if (!empty($parameters->routes())) {
            $new->routerMaker = $new->routerMaker
                ->withAddedRouteIdentifiers($parameters->routes());
            $new->container = $new->container
                ->withRouter(
                    (new Router())
                        ->withMaker($new->routerMaker)
                );
            $new->routerMaker = $new->routerMaker
                ->withCache();
            $new->cacheChecksums = array_merge($new->routerMaker->cache()->toArray(), $new->cacheChecksums);
        }
        $new->checkout = new Checkout($new);
        $new->isBuilt = true;
        $new->builder = $new->builder
            ->withParameters($parameters);

        return $new;
    }

    public function container(): Container
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function cacheChecksums(): array
    {
        return $this->cacheChecksums;
    }

    public function checkout(): CheckoutContract
    {
        return $this->checkout;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        unlink($this->path->path());
        $path = (new PathHandle('cache'))->path();
        $path->removeContents();
    }
}
