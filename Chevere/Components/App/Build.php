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
use Chevere\Components\Api\Interfaces\ApiInterface;
use Chevere\Components\App\Interfaces\AppInterface;
use Chevere\Components\App\Interfaces\BuildInterface;
use Chevere\Components\App\Interfaces\CheckoutInterface;
use Chevere\Components\App\Interfaces\ParametersInterface;
use Chevere\Components\Dir\Interfaces\DirInterface;
use Chevere\Components\File\Interfaces\FileInterface;
use Chevere\Components\File\Interfaces\FilePhpInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use LogicException;

/**
 * The Build container.
 */
final class Build implements BuildInterface
{
    private AppInterface $app;

    private FilePhpInterface $filePhp;

    private DirInterface $dir;

    /** @var bool True if the App was just built */
    private bool $isMaked = false;

    private CheckoutInterface $checkout;

    /** @var array Containing the collection of Cache->toArray() data checksums (if any)*/
    private array $checksums;

    private ApiMaker $apiMaker;

    private ParametersInterface $parameters;

    private RouterMakerInterface $routerMaker;

    /**
     * Constructs the Build instance.
     *
     * A Build instance allows to interact with the application build, which refers to the base
     * application service layer which consists of API and Router services.
     *
     * @param AppInterface  $app  The application container
     *
     * @throws PathIsNotDirectoryException if the $path doesn't exists and unable to create
     */
    public function __construct(AppInterface $app)
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

    public function withApp(AppInterface $app): BuildInterface
    {
        $new = clone $this;
        $new->app = $app;

        return $new;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }

    public function withParameters(ParametersInterface $parameters): BuildInterface
    {
        $new = clone $this;
        $new->parameters = $parameters;

        return $new;
    }

    public function hasParameters(): bool
    {
        return isset($this->parameters);
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function withRouterMaker(RouterMakerInterface $routerMaker): BuildInterface
    {
        $new = clone $this;
        $new->routerMaker = $routerMaker;

        return $new;
    }

    public function hasRouterMaker(): bool
    {
        return isset($this->routerMaker);
    }

    public function routerMaker(): RouterMakerInterface
    {
        return $this->routerMaker;
    }

    /**
     * Make the build, provide AppInterface services.
     */
    public function make(): BuildInterface
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

    public function isMaked(): bool
    {
        return $this->isMaked;
    }

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

    public function file(): FileInterface
    {
        return $this->filePhp->file();
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function checksums(): array
    {
        return $this->checksums;
    }

    public function checkout(): CheckoutInterface
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
        $missing = [];
        foreach ([
            'parameters' => ParametersInterface::class,
            'routerMaker' => RouterMakerInterface::class,
        ] as $property => $contract) {
            if (!isset($this->{$property})) {
                $missing[] = (new Message('%s'))->code('%s', $contract)->toString();
            }
        }
        if ($missing) {
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
                new Cache($this->dir->getChild(ApiInterface::CACHE_ID))
            );
        $this->checksums[ApiInterface::CACHE_ID] = $this->apiMaker->cache()->toArray();
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
                ->withMembersType(new Type(RouteInterface::class));
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
                        ->getChild(RouterInterface::CACHE_ID)
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

        $this->checksums = [
            RouterInterface::CACHE_ID => $routerCache->cache()->toArray()
        ];
    }
}
