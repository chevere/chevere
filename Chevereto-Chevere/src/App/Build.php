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

    public function apply()
    {
        $consoleIsBuilding = Console::isBuilding();
        try {
            $this->container->setApi(
                !$consoleIsBuilding ? Api::fromCache() : new Api()
            );
            $this->container->setRouter(
                !$consoleIsBuilding ? Router::fromCache() : new Router()
            );
        } catch (CacheNotFoundException $e) {
            $message = sprintf('The app must be re-build due to missing cache. %s', $e->getMessage());
            throw new NeedsToBeBuiltException($message, $e->getCode(), $e);
        }
    }

    /**
     * Makes the application Api and Router, store these in the Build container.
     */
    public function make(Parameters $parameters): void
    {
        $this->routerMaker = new RouterMaker();
        if ($this->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $this->cacheChecksums = [];
        if (!empty($parameters->api())) {
            $pathHandle = new PathHandle($parameters->api());
            $this->apiMaker = ApiMaker::create($pathHandle, $this->routerMaker);
            $this->container->setApi(
                Api::fromMaker($this->apiMaker)
            );
            $this->cacheChecksums = $this->apiMaker->cache()->toArray();
        }
        if (!empty($parameters->routes())) {
            $this->routerMaker->addRoutesArrays($parameters->routes());
            $this->container->setRouter(
                Router::fromMaker($this->routerMaker)
            );
            $this->cacheChecksums = array_merge($this->routerMaker->cache()->toArray(), $this->cacheChecksums);
        }
        $this->checkout = new Checkout($this);
        $this->isBuilt = true;
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

    public function destroy(): void
    {
        unlink($this->pathHandle->path());
        $cachePath = Path::fromIdentifier('cache');
        Path::removeContents($cachePath);
    }
}
