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
use Chevere\Components\Api\ApiMaker;
use Chevere\Components\App\Exceptions\BuildAlreadyMakedException;
use Chevere\Components\App\Exceptions\BuildFileNotExistsException;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Type\Type;
use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\CheckoutContract;
use Chevere\Contracts\App\ParametersContract;
use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;
use Chevere\Contracts\Path\PathContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterMakerContract;
use Chevere\Contracts\Router\RouterContract;
use LogicException;

/**
 * The Build container.
 */
final class Build implements BuildContract
{
    private AppContract $app;

    private FilePhpContract $filePhp;

    private DirContract $cacheDir;

    /** @var bool True if the App was just built */
    private bool $isMaked = false;

    private CheckoutContract $checkout;

    /** @var array Containing the collection of Cache->toArray() data checksums (if any)*/
    private array $checksums;

    private ApiMaker $apiMaker;

    private ParametersContract $parameters;

    private RouterMakerContract $routerMaker;

    /**
     * {@inheritdoc}
     */
    public function __construct(AppContract $app, PathContract $path)
    {
        $this->isMaked = false;
        $this->checksums = [];
        $this->app = $app;
        $this->filePhp = new FilePhp(
            new File(
                $path->getChild('build.php')
            )
        );
        $this->cacheDir = new Dir($path);

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
    public function withRouterMaker(RouterMakerContract $routerMaker): BuildContract
    {
        $new = clone $this;
        $new->routerMaker = $routerMaker;

        return $new;
    }

    public function hasRouterMaker(): bool
    {
        return isset($this->routerMaker);
    }

    public function routerMaker(): RouterMakerContract
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
            'routerMaker' => RouterMakerContract::class,
        ] as $property => $contract) {
            if (!isset($this->{$property})) {
                $missing[] = (new Message('%s'))->code('%s', $contract)->toString(0);
            }
        }
        if (isset($missing)) {
            throw new LogicException(
                (new Message('Method %method% can be only called when the instance of %className% has %contracts%'))
                    ->code('%method%', __METHOD__)
                    ->code('%className%', self::class)
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
                    ->withApiMaker($this->apiMaker)
            );
        $this->app = $this->app
            ->withServices($services);
        $this->apiMaker = $this->apiMaker
            ->withCache(
                new Cache($this->cacheDir->getChild(ApiContract::CACHE_ID))
            );
        $this->checksums[ApiContract::CACHE_ID] = $this->apiMaker->cache()->toArray();
    }

    private function makeRouter(): void
    {
        foreach ($this->parameters->routes() as $fileHandleString) {
            $arrayFile =
                (new ArrayFile(
                    new FilePhp(
                        new File(
                            new Path($fileHandleString)
                        )
                    )
                ))
                ->withMembersType(new Type(RouteContract::class));
            foreach ($arrayFile->array() as $route) {
                $this->routerMaker = $this->routerMaker
                    ->withAddedRouteable(
                        new Routeable($route),
                        $fileHandleString
                    );
            }
        }
        $routerCache =
            (new RouterCache(
                new Cache(
                    $this->cacheDir
                        ->getChild(RouterContract::CACHE_ID)
                )
            ))
            ->withPut($this->routerMaker);

        $services = $this->app->services()
            ->withRouter(
                (new Router())
                    ->withProperties($this->routerMaker->properties())
            );
        $this->app = $this->app
            ->withServices($services);

        $this->checksums[RouterContract::CACHE_ID] = $routerCache->cache()
            ->toArray();
    }
}
