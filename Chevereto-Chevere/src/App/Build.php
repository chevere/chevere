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
use Chevere\App\Exceptions\NeedsToBeBuiltException;
use Chevere\Cache\Exceptions\CacheNotFoundException;
use Chevere\Console\Console;
use Chevere\Contracts\App\LoaderContract;
use Chevere\File\File;
use Chevere\Path\Path;
use Chevere\Path\PathHandle;
use Chevere\Router\Router;

final class Build
{
    const FILE_INDETIFIER = 'var:build';

    /** @var Container */
    private $container;

    /** @var LoaderContract */
    private $loader;

    /** @var PathHandle */
    private $pathHandle;

    /** @var bool True if the App was built (cache) */
    private $isBuilt;

    /** @var Checkout */
    private $checkout;

    /** @var array An array containing the collection of Cache->toArray() data (checksums) */
    private $cacheChecksums;

    /** @var ApiMaker */
    private $apiMaker;

    /** @var RouterMaker */
    private $routerMaker;

    public function __construct(LoaderContract $loader)
    {
        $this->container = new Container();
        $this->loader = $loader;
        $this->pathHandle =  new PathHandle(static::FILE_INDETIFIER);
    }

    public function pathHandle(): PathHandle
    {
        return $this->pathHandle;
    }

    public function exists(): bool
    {
        return File::exists($this->pathHandle->path());
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function withContainer(Container $container): Build
    {
        $new = clone $this;
        $new->container = $container;

        return $new;
    }

    /**
     * Makes the application Api and Router, store these in the Build container.
     */
    public function withParameters(Parameters $parameters): Build
    {
        $new = clone $this;
        $new->routerMaker = new RouterMaker();
        if ($new->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $new->cacheChecksums = [];
        if (!empty($parameters->api())) {
            $pathHandle = new PathHandle($parameters->api());
            $new->apiMaker = ApiMaker::create($pathHandle, $new->routerMaker);
            $new->container = $new->container
                ->withApi(
                    Api::fromMaker($new->apiMaker)
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
                    Router::fromMaker($new->routerMaker)
                );
            $new->routerMaker = $new->routerMaker
                ->withCache();
            $new->cacheChecksums = array_merge($new->routerMaker->cache()->toArray(), $new->cacheChecksums);
        }
        $new->checkout = new Checkout($new);
        $new->isBuilt = true;

        return $new;
    }

    /**
     * Retrieves the file checksums, available only when building the App.
     */
    public function cacheChecksums(): array
    {
        return $this->cacheChecksums;
    }

    public function checkout(): Checkout
    {
        return $this->checkout;
    }

    /**
     * Destroy the build signature and any cache generated.
     */
    public function destroy(): void
    {
        unlink($this->pathHandle->path());
        $cachePath = Path::fromIdentifier('cache');
        Path::removeContents($cachePath);
    }
}
