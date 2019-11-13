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

namespace Chevere\Components\App;

use Chevere\Components\Api\Api;
use Chevere\Components\Api\Maker as ApiMaker;
use Chevere\Components\App\Exceptions\BuildAlreadyMakedException;
use Chevere\Components\App\Exceptions\BuildFileNotExistsException;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Message\Message;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\CheckoutContract;
use Chevere\Contracts\App\ParametersContract;
use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;
use Chevere\Contracts\Path\PathContract;
use Chevere\Contracts\Router\MakerContract;
use LogicException;

/**
 * The Build container.
 */
final class Build implements BuildContract
{
    /** @var AppContract */
    private $app;

    /** @var ParametersContract */
    private $parameters;

    /** @var FilePhpContract */
    private $filePhp;

    /** @var DirContract */
    private $cacheDir;

    /** @var bool True if the App was just built */
    private $isMaked;

    /** @var CheckoutContract */
    private $checkout;

    /** @var array Containing the collection of Cache->toArray() data (checksums) */
    private $checksums;

    /** @var ApiMaker */
    private $apiMaker;

    /** @var MakerContract */
    private $routerMaker;

    /**
     * {@inheritdoc}
     */
    public function __construct(AppContract $app, PathContract $path)
    {
        $this->isMaked = false;
        $this->app = $app;
        $this->filePhp = new FilePhp(
            new File(
                $path->getChild('build.php')
            )
        );
        $this->cacheDir = new Dir(
            $path->getChild('cache')
        );

        if (!$this->cacheDir->exists()) {
            $this->cacheDir->create();
        }

        $this->assertCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    public function withApp(AppContract $app): BuildContract
    {
        $new = clone $this;
        $new->app = $app;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function app(): AppContract
    {
        return $this->app;
    }

    /**
     * {@inheritdoc}
     */
    public function withParameters(ParametersContract $parameters): BuildContract
    {
        $new = clone $this;
        $new->parameters = $parameters;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameters(): bool
    {
        return isset($this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function parameters(): ParametersContract
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function withRouterMaker(MakerContract $maker): BuildContract
    {
        $new = clone $this;
        $new->routerMaker = $maker;

        return $new;
    }

    public function hasRouterMaker(): bool
    {
        return isset($this->routerMaker);
    }

    public function routerMaker(): MakerContract
    {
        return $this->routerMaker;
    }

    /**
     * Make the build, provide AppContract services.
     */
    public function make(): BuildContract
    {
        $this->assertCanMake();
        $new = clone $this;
        $new->checksums = [];
        if ($new->parameters->hasApi()) {
            $new->makeApi();
        }
        if ($new->parameters->hasRoutes()) {
            $new->makeRouter();
        }
        $new->isMaked = true;
        $new->checkout = new Checkout($new);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function isMaked(): bool
    {
        return $this->isMaked;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        if (!$this->filePhp->file()->exists()) {
            throw new BuildFileNotExistsException();
        }
        $this->filePhp->file()->remove();
        if ($this->cacheDir->exists()) {
            $this->cacheDir
                ->removeContents();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function file(): FileContract
    {
        return $this->filePhp->file();
    }

    /**
     * {@inheritdoc}
     */
    public function cacheDir(): DirContract
    {
        return $this->cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function checksums(): array
    {
        return $this->checksums;
    }

    /**
     * {@inheritdoc}
     */
    public function checkout(): CheckoutContract
    {
        return $this->checkout;
    }

    private function assertCacheDir(): void
    {
        if (!$this->cacheDir->exists()) {
            throw new PathIsNotDirectoryException(
                (new Message('The application needs a cache directory at %path%'))
                    ->code('%path%', $this->cacheDir->path()->absolute())
                    ->toString()
            );
        }
    }

    private function assertCanMake(): void
    {
        if ($this->isMaked) {
            throw new BuildAlreadyMakedException();
        }
        foreach ([
            'parameters' => ParametersContract::class,
            'routerMaker' => MakerContract::class,
        ] as $property => $contract) {
            if (!isset($this->{$property})) {
                $missing[] = (new Message('%s'))->code('%s', $contract)->toString(0);
            }
        }
        if (isset($missing)) {
            throw new LogicException(
                (new Message('Method %method% can be only called when the instance of %className% has %contracts%'))
                    ->code('%method%', __METHOD__)
                    ->code('%className%', __CLASS__)
                    ->strtr('%contracts%', implode(', ', $missing))
                    ->toString()
            );
        }
    }

    private function makeApi(): void
    {
        $this->apiMaker = new ApiMaker($this->routerMaker);
        $this->apiMaker = $this->apiMaker
            ->withPath(
                new Path(
                    $this->parameters->api()
                )
            );

        $services = $this->app->services()
            ->withApi(
                (new Api())
                    ->withMaker($this->apiMaker)
            );
        $this->app = $this->app
            ->withServices($services);
        $this->apiMaker = $this->apiMaker
            ->withCache(
                new Cache(new CacheKey('api'), $this->cacheDir)
            );
        $this->checksums = $this->apiMaker->cache()->toArray();
    }

    private function makeRouter(): void
    {
        $this->routerMaker = $this->routerMaker
            ->withAddedRouteFiles(...$this->parameters->routes());
        $services = $this->app->services()
            ->withRouter(
                (new Router())
                    ->withMaker($this->routerMaker)
            );
        $this->app = $this->app
            ->withServices($services);
        $this->routerMaker = $this->routerMaker
            ->withCache(
                new Cache(new CacheKey('router'), $this->cacheDir)
            );
        $this->checksums = array_merge($this->routerMaker->cache()->toArray(), $this->checksums);
    }
}
