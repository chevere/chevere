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
use Chevere\Contracts\App\LoaderContract;
use Chevere\File;
use Chevere\Path\PathHandle;
use Chevere\Router\Router;

final class Build
{
    const FILE_INDETIFIER = 'var:build';

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

    public function make(Parameters $parameters): Container
    {
        $this->routerMaker = new RouterMaker();
        $container = new Container();
        if ($this->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $this->cacheChecksums = [];
        if (!empty($parameters->api())) {
            $pathHandle = new PathHandle($parameters->api());
            $this->apiMaker = ApiMaker::create($pathHandle, $this->routerMaker);
            $container->setApi(
                Api::fromMaker($this->apiMaker)
            );
            $this->cacheChecksums = $this->apiMaker->cache()->toArray();
        }
        if (!empty($parameters->routes())) {
            $this->routerMaker->addRoutesArrays($parameters->routes());
            $container->setRouter(
                Router::fromMaker($this->routerMaker)
            );
            $this->cacheChecksums = array_merge($this->routerMaker->cache()->toArray(), $this->cacheChecksums);
        }
        $this->checkout = new Checkout($this);
        $this->isBuilt = true;

        return $container;
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
}
