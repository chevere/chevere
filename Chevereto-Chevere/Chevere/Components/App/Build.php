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
use Chevere\Components\Path\PathApp;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Type\Type;
use Chevere\Components\Api\Contracts\ApiContract;
use Chevere\Components\App\Contracts\AppContract;
use Chevere\Components\App\Contracts\BuildContract;
use Chevere\Components\App\Contracts\CheckoutContract;
use Chevere\Components\App\Contracts\ParametersContract;
use Chevere\Components\Dir\Contracts\DirContract;
use Chevere\Components\File\Contracts\FileContract;
use Chevere\Components\File\Contracts\FilePhpContract;
use Chevere\Components\Route\Contracts\RouteContract;
use Chevere\Components\Router\Contracts\RouterMakerContract;
use Chevere\Components\Router\Contracts\RouterContract;
use LogicException;

/**
 * The Build container.
 */
final class Build implements BuildContract
{
    private AppContract $app;

    private FilePhpContract $filePhp;

    private DirContract $dir;

    /** @var bool True if the App was just built */
    private bool $isMaked = false;

    private CheckoutContract $checkout;

    /** @var array Containing the collection of Cache->toArray() data checksums (if any)*/
    private array $checksums;

    private ApiMaker $apiMaker;

    private ParametersContract $parameters;

    private RouterMakerContract $routerMaker;

    /**
     * Constructs the Build instance.
     *
     * A BuildContract instance allows to interact with the application build, which refers to the base
     * application service layer which consists of API and Router services.
     *
     * @param AppContract  $app  The application container
     *
     * @throws PathIsNotDirectoryException if the $path doesn't exists and unable to create
     */
    public function __construct(AppContract $app)
    {
        $path = new PathApp('build');
        $this->isMaked = false;
        $this->checksums = [];
        $this->app = $app;
        $this->filePhp = new FilePhp(
            new File(
                $path->getChild('build.php')
            )
        );
        $this->dir = new Dir($path);

        if (!$this->dir->exists()) {
            $this->dir->create();
        }

        $this->assertDir();
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
        if ($this->dir->exists()) {
            $this->dir
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
    public function dir(): DirContract
    {
        return $this->dir;
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

    private function assertDir(): void
    {
        if (!$this->dir->exists()) {
            throw new PathIsNotDirectoryException(
                (new Message('The application needs a cache directory at %path%'))
                    ->code('%path%', $this->dir->path()->absolute())
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
                $missing[] = (new Message('%s'))->code('%s', $contract)->toString();
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
                new PathApp(
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
                new Cache($this->dir->getChild(ApiContract::CACHE_ID))
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
                            new PathApp($fileHandleString)
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
                    $this->dir
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
